<template>
    <admin-base>
        <div>
            <div class="row">
                <div class="col-12">
                    <b-card class="border-0 shadow-sm">
                        
                        <b-form @submit.prevent="alterCredentials">
                            <div class="row">
                                <div class="col-md-6">
                                    <b-form-group>	
                                        <label>Nome</label>
                                        <b-form-input v-model="user.name"></b-form-input>
                                    </b-form-group>
                                </div>
                                <div class="col-md-6">
                                    <b-form-group>	
                                        <label>E-Mail</label>
                                        <b-form-input type="email" v-model="user.email"></b-form-input>
                                    </b-form-group>
                                </div>
                                <div class="col-md-6">
                                    <b-form-group>	
                                        <label>Telefone</label>
                                        <b-form-input v-model="user.phone" v-mask="'(##) #####-####'"></b-form-input>
                                    </b-form-group>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex w-100 justify-content-between">
                                        <b-button @click="modalPassowrdShow = true">Alterar Senha</b-button>
                                        <b-button variant="primary" type="submit">Salvar</b-button>
                                    </div>
                                </div>

                            </div>
                        </b-form>
                    </b-card>
                </div>
            </div>
            
            <b-modal 
                hide-footer 
                :visible="modalPassowrdShow" 
                @hidden="onPassowrdModalHidden"
                title="Alterar Senha"
            >

                <b-form @submit.prevent="updatePasswors">
                    <div class="row">
                        <div class="col-12">
                            <b-form-group>	
                                <label>Nova Senha</label>
                                <b-form-input type="password" v-model="passwordForm.password"/>
                            </b-form-group>
                        </div>
                        <div class="col-12">
                            <b-form-group>	
                                <label>Confirme a senha</label>
                                <b-form-input type="password" v-model="passwordForm.password_confirmation"/>
                            </b-form-group>
                        </div>
                        <div class="col-12 text-right">
                            <b-button @click="onPassowrdModalHidden">Cancelar</b-button>
                            <b-button type="submit" variant="primary">
                                <b-icon icon="check"></b-icon>
                                Salvar
                            </b-button>
                        </div>
                    </div>

                </b-form>
            </b-modal>
        </div>

    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'

export default {
    
    components: {AdminBase},
    mounted: function() {
        common.request({
            type: 'get',
            url: '/api/user/admin',
            auth: true,
            setError: true,
            success: (resp) => {
                this.user = resp
            }
        })
    },
    data: () => ({
        user: {},
        modalPassowrdShow: false,
        passwordForm: {}
    }),
    methods: {
        alterCredentials(){
            common.request({
                type: 'put',
                url: '/api/users/admin/self-update',
                auth: true,
                setError: true,
                savedAlert: true,
                data: this.user,
                success: (resp) => {
                    this.user = resp
                }
            })
        },
        onPassowrdModalHidden(){
            this.passwordForm = {}
            this.modalPassowrdShow = false
        },
        updatePasswors(){

            common.request({
                type: 'post',
                url: '/api/users/password/update',
                auth: true,
                setError: true,
                savedAlert: true,
                data: this.passwordForm,
                success: (resp) => {
                    this.passwordForm = {}
                    this.modalPassowrdShow = false
                }
            })
        }
    },
}
</script>