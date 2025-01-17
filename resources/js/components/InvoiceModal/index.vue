<template>
    <b-modal 
        @show="onShow"
        hide-footer 
        :visible="visible" 
        @hidden="onHidden"
        :title="idInvoice ? 'Editar Fatura' : 'Nova Fatura'"
        size="md"
    >
        <div class="row">
            <div class="col-md-12">
                <b-form-group>
                    <label>Usuário</label>
                    <b-form-select :disabled="idInvoice ? true : false" :options="usersOptions" v-model="invoice.id_user"/>
                </b-form-group>
            </div>

            <div class="col-md-12">
                <b-form-group>
                    <label>Valor</label>
                    <b-form-input v-model="invoice.value" v-money="{ decimal: ',', thousands: '.', precision: 2 }"/>
                </b-form-group>
            </div>

            <div class="col-md-6">
                <b-form-group>
                    <label>Juros</label>
                    <b-form-input v-model="invoice.fee" v-money="{ decimal: ',', thousands: '.', precision: 2 }"/>
                </b-form-group>
            </div>
            <div class="col-md-6">
                <b-form-group>
                    <label>Adicional</label>
                    <b-form-input v-model="invoice.added" v-money="{ decimal: ',', thousands: '.', precision: 2 }"/>
                </b-form-group>
            </div>

            <div class="col-md-12">
                <b-form-group>
                    <label>Vencimento</label>
                    <b-form-input type="date" v-model="invoice.expires_at"/>
                </b-form-group>
            </div>

            <div class="col-md-12">
                <b-form-group>
                    <b-form-checkbox v-model="invoice.send_mail" name="check-button" switch>
                        Enviar E-mail de aviso de nova fatura
                    </b-form-checkbox>
                </b-form-group>
            </div>

            <div class="col-12 text-right">
                <b-button @click="onHidden">Cancelar</b-button>
                <b-button @click="save" variant="primary">
                    <b-icon icon="check"></b-icon>
                    Salvar
                </b-button>
            </div>

        </div>
    </b-modal>
</template>

<script>
import common from '../../common/common'
export default {
    props:{
        visible: Boolean,
        idUser: Number,
        idInvoice: Number
    },
    data: () => ({
        formData: {},
        invoice: {},
        moneyMask: { decimal: ',', thousands: '.', precision: 2 },
        users: []
    }),
    mounted: function(){
        this.getUsers()
    },
    computed: {
        usersOptions: function(){
            return this.users.map(us => (
                {value: us.id, text: `${us.name} - ${us.cpf}`}
            ))
        }
    },
    methods: {
        toMoney(str){
            return common.toMoney(str)
        },
        onHidden(){
            this.invoice = {}
            this.$emit('onHidden', this.visible)
        },
        save(){

            if( this.idInvoice ){

                common.confirmAlert({
                    title: 'Alterar esta fatura?',
                    message: 'Caso haja um boleto gerado e o valor ou data de vencimento for alterada, o boleto será cancelado para a geração de outro atualizado.',
                    onConfirm: () => {
                        common.request({
                            url: '/api/invoices/'+this.idInvoice,
                            type: 'put',
                            auth: true,
                            data: this.invoice,
                            setError: true,
                            load: true,
                            success: (resp) => {
                                this.$emit('onSave', resp)
                            }
                        })
                    }
                })

            } else {
                common.request({
                    url: '/api/invoices',
                    type: 'post',
                    auth: true,
                    data: this.invoice,
                    setError: true,
                    load: true,
                    success: (resp) => {
                        this.$emit('onSave', resp)
                    }
                })
            }
        },
        getInvoice() {
            common.request({
                url: '/api/invoices/get/'+this.idInvoice,
                type: 'get',
                auth: true,
                load: true,
                setError: true,
                success: (resp) => {
                    resp.value = common.toMoney(resp.value)
                    resp.fee = common.toMoney(resp.fee)
                    resp.added = common.toMoney(resp.added)
                    resp.expires_at = resp.expires_at.split(' ')[0]
                    this.invoice = resp
                }
            })
        },
        onShow(){
            if( this.idInvoice ){
                this.getInvoice()      
            } else {
                if( this.idUser ){
                    this.invoice.id_user = this.idUser
                }
            }
        },
        getUsers(){
            common.request({
                url: '/api/users/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (resp) => {
                    this.users = resp
                }
            })
        }
    }
}
</script>