<template>
    
    <b-modal id='sale-modal' hide-footer title="Venda" size="lg" @shown="getSale">

        <div class="row">

            <div class="col-md-12">
                <div class="form-group">
                    <label>Descrição</label>
                    <b-form-input v-model="sale.description"></b-form-input>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Aluno</label>
                    <b-form-select :options="studentsOptions" v-model="sale.id_student"></b-form-select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Unidade</label>
                    <b-form-select :options="unitsOptions" v-model="sale.id_unit"></b-form-select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Cor</label>
                    <b-form-input v-model="sale.color"></b-form-input>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tamanho</label>
                    <b-form-input v-model="sale.size"></b-form-input>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" v-model="sale.status">
                        <option value="Encomendado">Encomendado</option>
                        <option value="Pronto">Pronto</option>
                        <option value="Entregue">Entregue</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Valor R$</label>
                    <b-form-input v-model="sale.price" v-money="moneyMask"></b-form-input>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Valor Pago R$</label>
                    <b-form-input v-model="sale.paid_price" v-money="moneyMask"></b-form-input>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Forma de pagamento</label>
                    <b-form-input v-model="sale.payment_method"></b-form-input>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status do pagamento</label>
                    <select class="form-control" v-model="sale.payment_status">
                        <option value="Pendente">Pendente</option>
                        <option value="Pago">Pago</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Data de pagamento</label>
                    <b-form-input v-model="sale.paid_at" type="date"></b-form-input>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-12 text-right">
                <div>
                    <b-button @click="close">Cancelar</b-button>
                    <b-button variant="primary" @click="save">
                        <b-icon icon="check"/>
                        Salvar
                    </b-button>
                </div>
            </div>
        </div>
    </b-modal>
</template>
<script>
import common from '../../../common/common'
export default {
    
    props: {
        idSale: Number
    },
    data: () => ({
        sale: {},
        students: [],
        units: [],

        moneyMask: {
            decimal: ',',
            thousands: '.',
            precision: 2,
        },
    }),

    mounted() {
        this.getStudents()
        this.getUnits()
    },

    computed: {
        studentsItems(){
            return this.students.map(st => (
                {...st, user_name: st.user.name}
            ))
        },
        unitsOptions(){
            return this.units.map(un => ({ text: un.name, value: un.id }))
        },
        studentsOptions(){
            return this.students.map(st => ({ text: `${st.name} - ${st.user.name}`, value: st.id }))
        }
    },


    methods: {

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

        getSale(){

            if( this.idSale ){
                
                common.request({
                    url: '/api/sales/'+this.idSale,
                    type: 'get',
                    auth: true,
                    setError: true,
                    load: true,
                    success: (sale) => {
                        this.sale = sale
                    }
                })
            } else {
                this.sale = {}
            }
        },

        save() {

            if( this.idSale ){
                common.request({
                    url: '/api/sales/'+this.idSale,
                    type: 'put',
                    auth: true,
                    setError: true,
                    data: this.sale,
                    success: (sale) => {
                        this.$emit('save')
                        this.sale = {}
                    }
                })
            } else {

                common.request({
                    url: '/api/sales',
                    type: 'post',
                    auth: true,
                    setError: true,
                    data: this.sale,
                    success: (sale) => {
                        this.$emit('save')
                        this.sale = {}
                    }
                })
            }
        },

        close() {
            this.$bvModal.hide('sale-modal')
            this.sale = {}
        }
    }
}
</script>