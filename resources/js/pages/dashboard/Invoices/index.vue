<template>
    <dashboard-base
        :title="'Usuários'"
    >
        
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
                                        <b-badge variant="success" v-else-if="invoice.status == 'P'">{{invoice.status_text}}</b-badge>
                                        <b-badge variant="danger" v-else-if="invoice.status == 'C'">{{invoice.status_text}}</b-badge>

                                        <b-badge variant="danger" v-if="invoice.is_expired">Vencida</b-badge>

                                    </h5>
                                    <p>
                                        Valor: <b>R$ {{toMoney(invoice.value)}}</b> <br>
                                        Gerado Em: <b>{{invoice.created_at_formated}}</b> <br>
                                        Vencimento: <b>{{invoice.expires_at_formated}}</b> <br>
                                    </p>
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
        
    </dashboard-base>
</template>

<script>
import common from '../../../common/common'
import DashboardBase from '../../../components/DashboardBase/index.vue'

export default {
    components: { DashboardBase },
       
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
