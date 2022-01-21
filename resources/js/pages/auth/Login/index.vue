<template>
    
    <commom-header>
        <div class="d-flex flex-column justify-content-center h-100 bg-light" style="height: 100vh !important">

            <b-container class="w-100 d-flex justify-content-center">
                <b-card style="max-width: 30rem" class="border-0 shadow-sm">
                    <b-form @submit.prevent="login">
                        <div class="flex flex-wrap max-w-xl">

                            <b-row>
                                <div class="col-12">
                                    <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Login</h1></div>
                                </div>
                                
                                <div class="col-12">
                                    <b-form-group>
                                        <label>E-Mail</label>
                                        <b-form-input required placeholder="Seu E-Mail" type="email" v-model="form.email"/>
                                    </b-form-group>
                                </div>
                                <div class="col-12">
                                    <b-form-group>
                                        <label>Senha</label>
                                        <b-form-input required placeholder="Informe sua senha" type="password" v-model="form.password"/>
                                    </b-form-group>
                                </div>
                                <div class="col-12 mt-4">
                                    <b-button type="submit" variant="primary" class="btn-block py-2">
                                        <b-icon icon="box-arrow-in-right"></b-icon>
                                        Login
                                    </b-button>

                                    <!-- <router-link tag="b-button" to='/cadastro' class="btn-block btn-light">
                                        <b-icon icon="person-plus"></b-icon>
                                        Matricular
                                    </router-link> -->
                                    <router-link tag="b-button" to='/' exact class="btn-block btn-light">
                                        <b-icon icon="globe"></b-icon>
                                        Ir para o site
                                    </router-link>

                                    <div class="my-2">
                                        <router-link tag="a" to='/password-recovery' >
                                            Esqueci minha senha
                                        </router-link>
                                    </div>

                                    

                                </div>
                            </b-row>
                        </div> 
                    </b-form>
                </b-card>
            </b-container>
        </div>
    </commom-header>
</template>
<script>
import common from '../../../common/common'
import CommomHeader from "../../../components/CommomHeader"

export default {
    components: {CommomHeader},
    data(){
        return{
            form:{
                email: '',
                password: ''
            },
            errors: []
        }
    },
    methods:{
        login() {

            common.request({
                url: '/api/login',
                type: 'post',
                data: this.form,
                setError: true,
                load: true,
                success: (resp) => {
                    localStorage.setItem('auth_token', resp.access_token)
                    this.$router.push({ path: resp.redirect_to || '/'} )
                }
            })

        },
    }
}
</script>