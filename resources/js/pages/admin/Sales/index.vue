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
                                        Vendas    
                                    </h3>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3">
                                            <label>Satus</label>
                                            <select class="form-control" v-model="filter.status">
                                                <option value="Encomendado">Encomendado</option>
                                                <option value="Pronto">Pronto</option>
                                                <option value="Entregue">Entregue</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Satus Pag.</label>
                                            <select class="form-control" v-model="filter.payment_status">
                                                <option value="Pendente">Pendente</option>
                                                <option value="Pago">Pago</option>
                                            </select>
                                        </div>

                                         <div class="col-md-3">
                                            <label>Aluno</label>
                                            <b-form-select v-model="filter.id_student" :options="studentsOptions"></b-form-select>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Unidade</label>
                                            <b-form-select v-model="filter.id_unit" :options="unitsOptions"></b-form-select>
                                        </div>

                                        <div class="col-md-4" style="margin-top: 2rem">
                                            <b-button @click="getSales">Buscar</b-button>
                                            <b-button variant="danger" @click="filter = {}">Limpar</b-button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="row my-2">
                                        <div class="col-md-4">
                                            <b-form-input size="sm" v-model="tableFilter" placeholder="Buscar"></b-form-input>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <b-button variant='primary' @click="newSale">Nova Venda</b-button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive" v-if="sales.length">
                                    <b-table
                                        :fields="tableFields"
                                        :items="sales"
                                        :filter="tableFilter"
                                        hover
                                    >
                                        <template #cell(actions)="row">
                                            <b-button variant="light" @click="() => getSale(row.item.id)" class="btn-sm" v-b-tooltip title="Editar">
                                                <b-icon icon="pencil-square"></b-icon>
                                            </b-button>
                                            <b-button variant="danger" @click="() => deleteSale(row.item.id)" class="btn-sm" v-b-tooltip title="Deletar">
                                                <b-icon icon="trash"></b-icon>
                                            </b-button>
                                        </template>
                                    </b-table>
                                </div>
                                <div v-else>Nenhuma venda ainda</div>

                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>
        
        <sale-modal :idSale="idSale" @save="onSave"/>
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import DataTable from "vue-materialize-datatable";
import SaleModal from './SaleModal.vue';

export default {
    components: { AdminBase, DataTable, SaleModal },

    computed: {

        tableFields(){
            return [
                { key: 'student.name', label: 'Aluno', sortable: true },
                { key: 'unit.name', label: 'Unidade', sortable: true },
                { key: 'description', label: 'Desc.', sortable: true },
                { key: 'status', label: 'Status', sortable: true },
                { key: 'payment_status', label: 'Pagamento', sortable: true },
                { key: 'formated_price', label: 'Valor', sortable: true },
                { key: 'actions', label: '' },
            ]
        },

        unitsOptions(){
            return this.units.map(un => ({ text: un.name, value: un.id }))
        },
        studentsOptions(){
            return this.students.map(st => ({ text: `${st.name} - ${st.user.name}`, value: st.id }))
        }
    },
       
    mounted: function(){
        this.getSales()
        this.getStudents()
        this.getUnits()
    },

    data: () => ({
        sales: [],
        idSale: null,
        filter: {},
        tableFilter: '',
        students: [],
        units: []
    }),

    methods: {
        getSales(){

            let filters = {}
            for(const key in this.filter){
                if( this.filter[key] )
                    filters[key] = this.filter[key]
            }
            let queryString = new URLSearchParams(filters)

            common.request({
                url: '/api/sales?'+queryString,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (sales) => {
                    this.sales = sales
                }
            })
        },

        getSale( id ){
            this.idSale = id
            this.$bvModal.show('sale-modal')
        },

        deleteSale( id ){

            common.confirmAlert({
                title: 'Deseja deletar esta venda?',
                onConfirm: () => {
                    common.request({
                        url: '/api/sales/'+id,
                        type: 'delete',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (sale) => {
                            this.getSales()        
                        }
                    })
                }
            })
        },

        onSave() {
            this.$bvModal.hide('sale-modal')
            this.idSale = null
            this.getSales()
        },

        newSale() {
            this.$bvModal.show('sale-modal')
            this.idSale = null
        },

        getStudents() {
            common.request({
                url: '/api/students/filter',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (students) => {
                    this.students = students
                }
            })
        },

        getUnits(){
            common.request({
                url: '/api/units/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (units) => {
                    this.units = units
                }
            })
        },

    }
}
</script>
