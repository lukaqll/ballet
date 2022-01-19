<template>
    <admin-base
        :title="'Usuários'"
    >
        
        <div class="row">

            <div class="col-12">
                <div>
                    <b-card no-body class="border-0 shadow-sm">

                        <b-card-body>
                            <div class="row">
                                <div class="col-12">
                                    <h3>
                                        Contratos    
                                    </h3>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <b-form-select :options="status" class="w-100" v-model="filter.status"></b-form-select>
                                        </div>
                                        <div class="col-md-4">
                                            <b-button @click="getContracts">Buscar</b-button>
                                            <b-button variant="danger" @click="filter = {}">Limpar</b-button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="my-2">
                                        <b-form-input size="sm" v-model="tableFilter" placeholder="Buscar"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive" v-if="contracts.length">
                                    <b-table
                                        :fields="tableFields"
                                        :items="contracts"
                                        :filter="tableFilter"
                                        hover
                                    >
                                        <template #cell(actions)="row">
                                            <b-button v-if="row.item.status == 'running'" variant="danger" @click="() => cancelContract(row.item.id)" class="btn-sm" v-b-tooltip title="Cancelar contrato">Cancelar</b-button>
                                            <b-button v-if="row.item.status == 'running'" variant="light" @click="() => notify(row.item.id)" class="btn-sm" v-b-tooltip title="Enviar notificação">
                                                <b-icon icon="bell"/>
                                            </b-button>
                                            <a :href="`/contracts/sign/${row.item.id}`" target="_blank" class="btn btn-light btn-sm" v-if="row.item.status == 'running'" v-b-tooltip title="Tela de assinatura">
                                                <b-icon icon="vector-pen"/>
                                            </a>
                                            <a :href="`/contracts/view/${row.item.id}`" target="_blank" class="btn btn-light btn-sm" v-b-tooltip title="Ver contrato">
                                                <b-icon icon="download"/>
                                            </a>
                                        </template>
                                    </b-table>
                                </div>
                                <div v-else>Nenhum contrato ainda</div>

                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>
        
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import DataTable from "vue-materialize-datatable";

export default {
    components: { AdminBase, DataTable },

    computed: {

        tableFields(){
            return [
                { key: 'user.name', label: 'Usuário', sortable: true },
                { key: 'student.name', label: 'Aluno', sortable: true },
                { key: 'class.name', label: 'Aula', sortable: true },
                { key: 'status_text', label: 'Status', sortable: true },
                { key: 'created_at_format', label: 'Criado Em', sortable: true },
                { key: 'actions', label: '' },
            ]
        },
        status(){
            return [
                {text: 'Abertos', value: 'running'},
                {text: 'Finalizados', value: 'closed'},
                {text: 'Cancelados', value: 'canceled'},
            ]
        }
    },

       
    mounted: function(){
        this.getContracts()
    },

    data: () => ({
        contracts: [],
        filter: {},
        tableFilter: ''
    }),

    methods: {
        getContracts(){

            let filters = {}
            for(const key in this.filter){
                if( this.filter[key] )
                    filters[key] = this.filter[key]
            }
            let queryString = new URLSearchParams(filters)

            common.request({
                url: '/api/contracts/list-all?'+queryString,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (contracts) => {
                    this.contracts = contracts
                }
            })
        },

        notify(id){

            common.request({
                url: '/api/contracts/notify/'+id,
                type: 'post',
                auth: true,
                setError: true,
                load: true,
                success: (student) => {
                    common.success({title: 'Notificações enviadas'})
                }
            })
        },

        cancelContract(id){

            common.confirmAlert({
                title: 'Deseja mesmo cancelar este contrato?',
                message: 'Esta ação será irreversível',
                onConfirm: () => {
                    common.request({
                        url: '/api/contracts/cancel/'+id,
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (contract) => {
                            this.getContracts()
                        }
                    })
                }
            })
        },
    }
}
</script>
