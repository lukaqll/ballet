<template>

    <b-container>
        <div class="row">

            <div class="col-md-12 pt-3" v-if="data.user">
                <h2 class="text-center text-secondary">Bem Vindo, {{ data.user.first_name }}</h2>
            </div>

            <div class="col-md-12 pt-3">

                <b-alert v-if="statusVariant" show dismissible :variant="statusVariant.variant">
                    {{statusVariant.text}}
                </b-alert>

                <router-link v-if="data.open_contracts" to="/contratos" tag="span">
                    <b-alert show dismissible variant="danger">
                        Você possui <b>{{ data.open_contracts }}</b> Contratos abertos
                    </b-alert>
                </router-link>

                <router-link v-if="data.user && data.user.open_invoices && data.user.open_invoices.length" to="/faturas" tag="span">
                    <b-alert show dismissible variant="danger">
                        Você possui <b>{{ data.user.open_invoices.length }}</b> Faturas abertas
                    </b-alert>
                </router-link>

            </div>

        </div>
    </b-container>
           
</template>

<script>
import common from '../../../common/common'

export default {
    data: () => ({
        data: {},
    }),
    mounted(){
        this.getHome()
    },
    computed: {
        statusVariant: function(){
            switch( this.data.user_status ){
                case 'A':
                    return {variant: 'success', text: 'Usuário Ativo'}
                case 'MP':
                    return {variant: 'danger', text: 'Cadastro em avaliação'}
                case 'I':
                    return {variant: 'danger', text: 'Cadastro inativado'}
                case 'P':
                    return {variant: 'danger', text: 'Cadastro pendende de pagamento'}
            }
        }
    },
    methods: {
        getHome(){
            common.request({
                type: 'get',
                url: '/api/get-client-home',
                auth: true,
                success: (resp) => {
                    this.data = resp
                }
            })
        }
    }

}
</script>
