<template>
     <div class="flex flex-wrap w-full justify-center items-center pt-56">
        <form @submit.prevent="login">
            <div class="flex flex-wrap max-w-xl">


                <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Login</h1></div>
                <div class="p-2 w-full">
                    <label for="email">E-Mail</label>
                    <input class="form-input w-full" placeholder="Email" type="email" v-model="form.email">
                </div>
                <div class="p-2 w-full">
                    <label for="password">Senha</label>
                    <input class="form-input w-full" placeholder="Password" type="password" v-model="form.password" name="password">
                </div>

                <div class="p-2 w-full mt-4">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>

                <span v-for="error in errors" :key="error">{{error}}</span>
            </div> 
        </form>
    </div>
</template>
<script>
import common from '../../common/common'

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