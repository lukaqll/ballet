<template>
    
    <div class="d-flex flex-column justify-content-center h-100" style="height: 100vh !important">

        <b-container class="w-100 d-flex justify-content-center">
            <b-card style="max-width: 30rem">
                <b-form @submit.prevent="login">
                    <div class="flex flex-wrap max-w-xl">

                        <b-row>
                            <div class="col-12">
                                <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Login</h1></div>
                            </div>
                            
                            <div class="col-12">
                                <b-form-group>
                                    <label>E-Mail</label>
                                    <b-form-input placeholder="Seu E-Mail" type="email" v-model="form.email"/>
                                </b-form-group>
                            </div>
                            <div class="col-12">
                                <b-form-group>
                                    <label>Senha</label>
                                    <b-form-input placeholder="Informe sua senha" type="password" v-model="form.password"/>
                                </b-form-group>
                            </div>
                            <div class="col-12 mt-4">
                                <b-button type="submit" variant="primary">
                                    <b-icon icon="box-arrow-in-right"></b-icon>
                                    Login
                                </b-button>
                            </div>
                        </b-row>

                        <span v-for="error in errors" :key="error">{{error}}</span>
                    </div> 
                </b-form>
            </b-card>
        </b-container>
    </div>
</template>
<script>

export default {
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
            axios.post('/api/login', this.form)
            .then((resp) => {
                localStorage.setItem('auth_token', resp.data.access_token)
                this.$router.push({ name: 'admin.home'});
            })
            .catch(e => {
                this.errors = e.response.data.errors
            })
        },
    }
}
</script>