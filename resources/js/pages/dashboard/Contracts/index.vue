<template>
    <dashboard-base
        :title="'Usuários'"
    >
        
        <div class="row">
            <div class="col-12 mt-3">
                <h3 class="ml-3">Meus Contratos</h3>
                <hr>
            </div>

            <div class="col-12">

                <div class="row">
                    <div class="col-md-4 my-2" v-for="contract in contracts" :key="contract.id">
                        <b-card>
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-center">
                                        <b-badge variant="primary" v-if="contract.status == 'running'">{{contract.status_text}}</b-badge>
                                        <b-badge variant="success" v-else-if="contract.status == 'closed'">{{contract.status_text}}</b-badge>
                                        <b-badge variant="danger" v-else-if="contract.status == 'canceled'">{{contract.status_text}}</b-badge>
                                    </h5>
                                    <p>
                                        Aluno: <b>{{contract.student.name}}</b> <br>
                                        Gerado Em: <b>{{contract.created_at_format}}</b> <br>
                                    </p>
                                </div>
                                <div class="col-12">

                                    <a :href="`/contracts/sign/${contract.id}`" target="_blank" class="btn btn-success btn-block" v-if="contract.status == 'running'" v-b-tooltip title="Assinar contrato">
                                        <b-icon icon="vector-pen"/>
                                        Assinar
                                    </a>

                                    <a :href="`/contracts/view/${contract.id}`" target="_blank" class="btn btn-light btn-block" v-b-tooltip title="Ver contrato">
                                        <b-icon icon="download"/>
                                        Ver Contrato
                                    </a>
                                    
                                </div>
                            </div>
                        </b-card>
                    </div>
                </div>

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
        this.getContracts()
    },

    data: () => ({
        contracts: [],
    }),

    methods: {
        getContracts(){

            common.request({
                url: '/api/contracts/list-self',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (contracts) => {
                    this.contracts = contracts
                }
            })
        },

    }
}
</script>
