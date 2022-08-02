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
                                <td>{{ invoice.expires_at_formated }}</td>
                                <td>R$ {{ toMoney(invoice.value) }}</td>
                                <td>R$ {{ toMoney(invoice.fee) }}</td>
                                <td>R$ {{ toMoney(invoice.added) }}</td>
                                <td>
                                    <b-badge v-if="invoice.is_expired" variant="danger">Vencida</b-badge>
                                    <b-badge v-if="invoice.manual" variant="primary">Baixa Manual</b-badge>
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

                                        <b-dropdown-item size="sm" @click="() => payInvoice(invoice.id)">
                                            Baixa Manual
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
    </div>

</template>

<script>
import common from '../../../common/common'
import InvoiceModal from '../../../components/InvoiceModal'
export default {
    components: {InvoiceModal},
    props:{
        visible: Boolean,
        idUser: Number
    },
    data: () => ({
        formData: {},
        invoices: [],

        showInvoiceModal: false,
        editableInvoice: null,
        idUserInvoice: null
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
        payInvoice(id){
            common.confirmAlert({
                title: 'Deseja baixar manualmente esta fatura?',
                onConfirm: () => {
                    common.request({
                        url: '/api/invoices/pay-manually/'+id,
                        type: 'post',
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
        }
    }
}
</script>