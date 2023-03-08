<template>
    <div>
        <b-modal
            @show="onShow"
            hide-footer
            :visible="visible"
            @hidden="onHidden"
            title="Faturas"
            size="xl"
        >
            <div class="row">
                <div class="col-md-12">
                    <b-button variant="primary" @click="newInvoice">
                        Nova Fatura
                    </b-button>
                </div>
                <div class="col-md-12">
                    <table class="table table-hover table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gerada Em</th>
                                <th>Status</th>
                                <th>Pago Em</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Multa/Juros</th>
                                <th>Adicionais</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="invoice in invoices" :key="invoice.id">
                                <td>{{ invoice.id }}</td>
                                <td>{{ invoice.created_at_formated }}</td>
                                <td>{{ invoice.status_text }}</td>
                                <td>{{ invoice.paid_at_formated || '-' }}</td>
                                <td>{{ invoice.expires_at_formated }}</td>
                                <td>R$ {{ toMoney(invoice.value) }}</td>
                                <td>R$ {{ toMoney(invoice.fee) }}</td>
                                <td>R$ {{ toMoney(invoice.added) }}</td>
                                <td>
                                    <b-badge v-if="invoice.is_expired" variant="danger">Vencida</b-badge>
                                    <b-badge v-b-tooltip :title="`Baixa em ${invoice.closed_at_formated}`" v-if="invoice.manual" variant="primary">Baixa Manual</b-badge>
                                    <b-badge v-if="invoice.manual && invoice.method" variant="primary">{{invoice.method}}</b-badge>
                                </td>
                                <td>

                                    <b-dropdown :id="'dropdown-'+invoice.id" size="sm" variant='light' v-if="invoice.status == 'A'">
                                        <template #button-content >
                                            <b-icon icon="three-dots-vertical"></b-icon>
                                        </template>

                                        <b-dropdown-item @click="() => openWindow(`/invoice-payment/get/${invoice.id}`)">
                                            Ver Boleto
                                        </b-dropdown-item>

                                        <b-dropdown-item size="sm" @click="() => sendMail(invoice.id)">
                                            Enviar E-mail
                                        </b-dropdown-item>

                                        <b-dropdown-item variant="danger" size="sm" @click="() => cancelInvoice(invoice.id)">
                                            Cancelar
                                        </b-dropdown-item>

                                        <b-dropdown-item size="sm" @click="() => editInvoice(invoice.id)">
                                            Editar
                                        </b-dropdown-item>

                                        <b-dropdown-item size="sm" @click="() => toPay = invoice.id">
                                            Baixa Manual
                                        </b-dropdown-item>
                                    </b-dropdown>

                                    <div v-else-if="invoice.status == 'P'">
                                        <b-button @click="() => openWindow(invoice.receipt)" variant="success" v-if="!!invoice.receipt" v-b-tooltip title="Ver comprovante"><i class="fa fa-receipt"></i></b-button>
                                        <b-button @click="attachId = invoice.id" v-else v-b-tooltip title="Anexar comprovante"><i class="fa fa-receipt"></i></b-button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </b-modal>

        <b-modal
            :title="`Baixa Manual #${toPay}`"
            hide-footer
            :visible="!!toPay"
            @hidden="hidePaymentModal"
        >
            <div class="row">
                <div class="col-md-6">
                    <b-form-group>
                        <label>Pago em</label>
                        <b-form-input type="date" v-model="payDate"/>
                    </b-form-group>
                </div>
                <div class="col-md-6">
                    <b-form-group>
                        <label>Método</label>
                        <b-form-select :options="paymentMethods" v-model="selectedMethod"/>
                    </b-form-group>
                </div>
                <div class="col-12">
                    <b-form-group>
                        <label>Comprovante</label>
                        <b-form-file
                            v-model="receipt"
                            placeholder="Escolha ou arraste um arquivo..."
                            drop-placeholder="Escolha ou arraste um arquivo..."
                        />
                    </b-form-group>
                    <div class="mt-3">Arquivo Selecionado: {{ receipt ? receipt.name : '' }}</div>
                </div>
                <hr>
                <div class="col-12 text-right">
                    <b-button @click="hidePaymentModal">Cancelar</b-button>
                    <b-button @click="payInvoice" variant="primary"><b-icon icon="check"/>Confirmar</b-button>
                </div>
            </div>
        </b-modal>

        <invoice-modal
            @onSave="() => {
                getInvoices()
                editableInvoice = null
                showInvoiceModal = false
                idUserInvoice = null
            }"
            @onHidden="() => {
                editableInvoice = null
                showInvoiceModal = false
                idUserInvoice = null
            }"
            :idInvoice="editableInvoice"
            :visible="showInvoiceModal"
            :idUser="idUserInvoice"
        />

        <file-modal
            title="Anexar comprovante"
            :visible="!!attachId"
            @upload="attachReceipt"
            @onHidden="hideFileModal"
        />
    </div>

</template>

<script>
import common from '../../../common/common'
import InvoiceModal from '../../../components/InvoiceModal'
import FileModal from '../../../components/FileModal/FileModal.vue'
export default {
    components: {InvoiceModal, FileModal},
    props:{
        visible: Boolean,
        idUser: Number
    },
    data: () => ({
        formData: {},
        invoices: [],

        showInvoiceModal: false,
        editableInvoice: null,
        idUserInvoice: null,

        attachId: false,

        toPay: null,
        payDate: null,
        paymentMethods: [],
        selectedMethod: null,
        receipt: null
    }),
    methods: {
        toMoney(str){
            return common.toMoney(str)
        },
        onHidden(){
            this.invoices = []
            this.$emit('onHidden', this.visible)
        },
        getInvoices() {
            common.request({
                url: '/api/invoices/list-by-user/'+this.idUser,
                type: 'get',
                auth: true,
                load: true,
                setError: true,
                success: (resp) => {
                    this.invoices = resp
                }
            })
        },
        onShow(){
            this.getInvoices()
            this.listMethods()
        },
        cancelInvoice(id){
            common.confirmAlert({
                title: 'Deseja cancelar esta fatura?',
                onConfirm: () => {
                    common.request({
                        url: '/api/invoices/cancel/'+id,
                        type: 'put',
                        auth: true,
                        load: true,
                        setError: true,
                        success: (resp) => {
                            this.getInvoices(this.idUser)
                        }
                    })
                }
            })
        },
        editInvoice(idInvoice){
            this.editableInvoice = idInvoice
            this.showInvoiceModal = true
        },
        newInvoice(){
            this.editableInvoice = null
            this.showInvoiceModal = true
            this.idUserInvoice = this.idUser
        },
        openWindow(url){
            window.open(url,'_blank');
        },
        payInvoice(){
            common.confirmAlert({
                title: 'Deseja baixar manualmente esta fatura?',
                onConfirm: () => {
                    const data = new FormData
                    !!this.payDate && data.append('paid_at', this.payDate)
                    !!this.selectedMethod && data.append('method', this.selectedMethod)
                    !!this.receipt && data.append('receipt', this.receipt)
                    
                    common.request({
                        url: '/api/invoices/pay-manually/'+this.toPay,
                        type: 'post',
                        file: true,
                        auth: true,
                        load: true,
                        setError: true,
                        data: data,
                        success: (resp) => {
                            this.hidePaymentModal()
                            this.getInvoices(this.idUser)
                        }
                    })
                }
            })
        },
        sendMail(id){
            common.confirmAlert({
                title: 'Enviar E-mail para o usuário desta fatura?',
                onConfirm: () => {
                    common.request({
                        url: '/api/invoices/send-mail/'+id,
                        type: 'post',
                        auth: true,
                        load: true,
                        setError: true,
                        success: (resp) => {
                            common.success({title: 'Email enviado'})
                        }
                    })
                }
            })
        },

        listMethods() {
            common.request({
                url: '/api/payment-methods',
                type: 'get',
                auth: true,
                success: (resp) => {
                    this.paymentMethods = resp.map(method => ({
                        value: method.id,
                        text: method.name
                    }))
                }
            })
        },

        hidePaymentModal() {
            this.toPay = null
            this.selectedMethod = null
            this.payDate = null
            this.receipt = null
        },

        hideFileModal() {
            this.attachId = null
        },

        attachReceipt(file) {
            const formData = new FormData();
            formData.append('receipt', file)
            common.request({
                url: '/api/invoices/attach-receipt/'+this.attachId,
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                load: true,
                success: (resp) => {
                    this.getInvoices()
                    this.hideFileModal()
                }
            })
        }
    }
}
</script>