<template>
       
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

                                        <b-dropdown :id="'dropdown-'+row.item.id" size="sm" variant='light'>
                                            <template #button-content >
                                                <b-icon icon="three-dots-vertical"></b-icon>
                                            </template>
                                            <b-dropdown-item :href="`/contracts/view/${row.item.id}`" target="_blank" size="sm">
                                                Ver contrato
                                            </b-dropdown-item>
                                            <b-dropdown-item :href="`/contracts/sign/${row.item.id}`" target="_blank" v-if="row.item.status == 'running'" size="sm">
                                                Assinar
                                            </b-dropdown-item>
                                            <b-dropdown-item v-if="row.item.status == 'running'" size="sm" @click="() => notify(row.item.id)">
                                                Notificar
                                            </b-dropdown-item>
                                            <b-dropdown-item @click="() => cancelContract(row.item.id)" v-if="row.item.status != 'canceled'">
                                                <span class='text-danger'>Cancelar</span>
                                            </b-dropdown-item>
                                        </b-dropdown>
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
</template>

<script>
import common from '../../../common/common'
import DataTable from "vue-materialize-datatable";

export default {
    components: {  DataTable },

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
