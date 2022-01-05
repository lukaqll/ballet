<template>
    <admin-base
        :title="'Usuários'"
    >
        
        <div class="row">

            <div class="col-12">
                <div>
                    <b-card no-body>

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
                            </div>
                            <div>

                                <div class="table-responsive" v-if="contracts.length">
                                    <data-table
                                        :rows="contracts"
                                        :columns="contractsBindings"
                                        locale="br"
                                        title=''
                                        :perPage="[50, 100, 200]"
                                        :clickable="false"
                                    >
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
                                            <td>
                                                <b-button v-if="props.row.status == 'running'" variant="danger" @click="() => cancelContract(props.row.id)" class="btn-sm">Cancelar</b-button>
                                                <b-button v-if="props.row.status == 'running'" variant="light" @click="() => notify(props.row.id)" class="btn-sm">
                                                    <b-icon icon="bell"/>
                                                </b-button>
                                                
                                            </td>
                                        </template>
                                    </data-table>
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
        contractsBindings(){
            return  [
                    {field: 'user.name', label: 'Usuário'},
                    {field: 'student.name', label: 'Aluno'},
                    {field: 'status_text', label: 'status'},
                    {field: 'created_at_format', label: 'Criado Em'},
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
        filter: {}
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
