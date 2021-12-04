<template>
    <b-modal 
        hide-footer 
        :visible="visible" 
        @hidden="onHidden"
        title="Alterar Senha"
    >

        <b-form @submit.prevent="save">
            <div class="row">
                <div class="col-12">
                    <b-form-group>	
                        <label>Nova Senha</label>
                        <b-form-input type="password" v-model="formData.password"/>
                    </b-form-group>
                </div>
                <div class="col-12">
                    <b-form-group>	
                        <label>Confirme a senha</label>
                        <b-form-input type="password" v-model="formData.password_confirmation"/>
                    </b-form-group>
                </div>
                <div class="col-12 text-right">
                    <b-button @click="onHidden">Cancelar</b-button>
                    <b-button type="submit" variant="primary">
                        <b-icon icon="check"></b-icon>
                        Salvar
                    </b-button>
                </div>
            </div>

        </b-form>
    </b-modal>
</template>

<script>
import common from '../../../common/common'
export default {
    props:{
        visible: Boolean,
        idUser: Number
    },
    data: () => ({
        formData: {}
    }),
    methods: {
        onHidden(){
            this.$emit('onHidden', this.visible)
        },
        save(){
            common.request({
                url: '/api/users/admin-password-update/'+this.idUser,
                type: 'put',
                auth: true,
                data: this.formData,
                setError: true,
                success: (resp) => {
                    this.formData = {}
                    this.$emit('onSave', resp)
                }
            })
        }
    }
}
</script>