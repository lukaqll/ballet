<template>
    <div>
        <b-modal 
            @show="onShow"
            hide-footer 
            :visible="visible" 
            @hidden="onHidden"
            title="Faturas"
            size="lg"
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
                                <th>Gerada Em</th>
                                <th>Status</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="invoice in invoices" :key="invoice.id">
                                <td>{{ invoice.created_at_formated }}</td>
                                <td>{{ invoice.status_text }}</td>
                                <td>{{ invoice.expires_at_formated }}</td>
                                <td>R$ {{ toMoney(invoice.value) }}</td>
                                <td>
                                    <b-badge v-if="invoice.is_expired" variant="danger">Vencida</b-badge>
                                </td>
                                <td>
                                    <b-button v-if="invoice.status == 'A'" variant="danger" size="sm" @click="() => cancelInvoice(invoice.id)">Cancelar</b-button>
                                    <b-button variant="light" v-if="invoice.status == 'A'" size="sm" @click="() => editInvoice(invoice.id)">
                                        <b-icon icon="pencil-square"></b-icon>
                                    </b-button>
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
        }
    }
}
</script>