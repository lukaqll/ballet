<template>
    <div class="d-flex flex-column justify-content-center bg-light" style="min-height: 100vh !important">

        <div class="w-100 d-flex justify-content-center">

            <b-card style="max-width: 30rem" class="border-0 shadow-sm">

                <template v-if="!sended">
                    
                    <b-form @submit.prevent="send">
                        <div class="flex flex-wrap max-w-xl">

                            <b-row>
                                <div class="col-12">
                                    <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Resgatar sua senha</h1></div>
                                </div>
                                
                                <div class="col-12">
                                    <b-form-group>
                                        <label>E-Mail</label>
                                        <b-form-input placeholder="Informe seu email" type="email" v-model="email"/>
                                    </b-form-group>
                                </div>

                                <div class="col-12">
                                    <b-button type="submit" class="btn-block" variant="primary">
                                        <b-icon icon="envelope"></b-icon>
                                        Enviar
                                    </b-button>

                                    <div class="mt-2">
                                    <router-link tag="a" to='/login' >
                                        Voltar para o login
                                    </router-link>
                                </div>
                                </div>
                            </b-row>
                        </div> 
                    </b-form>
                </template>      

                <template v-else>
                    <div class="text-center">
                        <h4 class="text-center">Um E-Mail foi enviado para {{ email }}</h4>
                        <small>
                            Verifique no seu E-mail e acesse o link para recuperar sua senha. <br> 
                            Não esqueça de verificar a caixa de Span e Lixo Eletrônico.
                        </small>
                        <b-button variant="primary" class="btn-block mt-2" @click="sended = false">
                            Enviar Novamente
                        </b-button>
                    </div>
                </template>
            </b-card>

        </div>
    </div>
</template>

<script>
import common from '../../../common/common'

export default {
    data: () => ({
        email: '',
        sended: false
    }),
    mounted: function () {


    },
    methods: {
        send(){

            common.request({
                type: 'post',
                url: '/api/password-reset/send-mail',
                setError: true,
                data: {email: this.email},
                load: true,
                success: (resp) => {
                    this.sended = true
                },
            })
        }
    }
}
</script>