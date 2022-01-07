<template>
    <commom-header>
        <div class="d-flex flex-column justify-content-center bg-light" style="min-height: 100vh !important">

            <div class="w-100 d-flex justify-content-center">

                <b-card style="max-width: 30rem" class="border-0 shadow-sm">
                    <b-form @submit.prevent="send">
                        <div class="flex flex-wrap max-w-xl">

                            <b-row>
                                <div class="col-12">
                                    <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Cadastre sua nova senha</h1></div>
                                </div>
                                
                                <div class="col-12">
                                    <b-form-group>
                                        <label>Senha</label>
                                        <b-form-input placeholder="Informe sua nova senha" type="password" v-model="form.password"/>
                                    </b-form-group>
                                </div>

                                <div class="col-12">
                                    <b-form-group>
                                        <label>Confirme Sua Senha</label>
                                        <b-form-input placeholder="Confirme sua nova senha" type="password" v-model="form.password_confirmation"/>
                                    </b-form-group>
                                </div>

                                <div class="col-12 mt-2">
                                    <b-button type="submit" variant="primary" class="btn-block">
                                        <b-icon icon="key"></b-icon>
                                        Salvar
                                    </b-button>
                                </div>

                                <div class="col-12 mt-2">
                                    <router-link tag="a" class="btn btn-light btn-block" to='/password-recovery' >
                                        <b-icon icon="envelope"></b-icon>
                                        Solicitar Novo Token
                                    </router-link>
                                </div>
                            </b-row>
                        </div> 
                    </b-form>
                </b-card>

            </div>
        </div>
    </commom-header>
</template>

<script>
import common from '../../../common/common'
import CommomHeader from "../../../components/CommomHeader"

export default {
    components: {CommomHeader},
    data: () => ({
        form: {},
    }),
    mounted: function () {

        const token = this.$route.params
        if( !token || token == '' ) {
            this.$router.push('login')
        }
    },
    methods: {
        send(){

            const form = {...this.form, token: this.$route.params.token}
            common.request({
                type: 'post',
                url: '/api/password-reset',
                setError: true,
                data: form,
                savedAlert: true,
                load: true,
                success: (resp) => {
                    this.$router.push({name: 'login'})
                },
            })
        }
    }
}
</script>