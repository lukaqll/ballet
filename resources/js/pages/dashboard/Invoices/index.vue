<template>
 
    <div class="row">
        <div class="col-12 mt-3">
            <h3 class="ml-3">Minhas Faturas</h3>
            <hr>
        </div>

        <div class="col-12">

            <div class="row justify-content-center" v-if="invoices.length">
                <div class="col-md-4 my-2"  v-for="invoice in invoices" :key="invoice.id">
                    <b-card class="border-0 shadow-sm">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-center">
                                    <b-badge variant="primary" v-if="invoice.status == 'A'">{{invoice.status_text}}</b-badge>
                                    <b-badge variant="success" v-else-if="invoice.status == 'P'">Pago</b-badge>
                                    <b-badge variant="danger" v-else-if="invoice.status == 'C'">{{invoice.status_text}}</b-badge>
                                    <b-badge variant="danger" v-if="invoice.is_expired">Vencida</b-badge>
                                </h5>
                                <p>
                                    <span v-if="invoice.fee > 0">
                                        Valor: <b>R$ {{toMoney(invoice.value)}}</b> <br>
                                        Multa/Juros: <b>R$ {{toMoney(invoice.fee)}}</b> <br>
                                    </span>
                                    Total: <b>R$ {{toMoney(invoice.total)}}</b> <br>
                                    Gerado Em: <b>{{invoice.created_at_formated}}</b> <br>
                                    Vencimento: <b>{{invoice.expires_at_formated}}</b> <br>
                                    <span v-if="(invoice.status == 'P' || invoice.status == 'PM') && invoice.paid_at">
                                        Pago Em: <b>{{invoice.paid_at_formated}}</b> <br>
                                    </span>
                                </p>
                            </div>

                            <div class="col-12" v-if="!!invoice.invoice_adds.length">
                                <span>Adicionais</span><br>
                                <span v-for="(added, i) in invoice.invoice_adds" :key="i">
                                    <small>- {{added.description}} <b>R$ {{toMoney(added.value)}}</b></small><br>
                                </span>
                            </div>
                            <div class="col-12">
                                <a v-if="invoice.status == 'A'" :href="`/invoice-payment/get/${invoice.id}`" target="_blank" class="btn btn-light btn-block">
                                    Ver Boleto
                                </a>
                            </div>
                        </div>
                    </b-card>
                </div>
            </div>
            <h5 class="text-center text-secondary" v-else>Nenhuma fatura ainda</h5>

        </div>
    </div>
        
</template>

<script>
import common from '../../../common/common'

export default {
       
    mounted: function(){
        this.getinvoices()
    },

    data: () => ({
        invoices: [],
    }),

    methods: {
        toMoney(str){
            return common.toMoney(str)
        },
        getinvoices(){

            common.request({
                url: '/api/invoices/list-self',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (invoices) => {
                    this.invoices = invoices
                }
            })
        },

    }
}
</script>
