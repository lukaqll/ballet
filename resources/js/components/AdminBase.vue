<template>

    <div>
        <div class="flex bg-gray-100 border-gray-800 py-4">
            <div class="container mx-auto flex justify-between">
                <div class="flex">
                    <router-link class="mr-4" to='/admin' exact>Home</router-link>
                    <router-link to='/admin/users'>Usuários</router-link>
                </div>

                <div class="flex pull-right">
                    <router-link class="mr-4" to='/login' exact>Login</router-link>
                    <span>{{ user.name }}</span>
                </div>
            </div>
        </div>

        <div class="container mx-auto my-2">
            <h1 class="page-title my-3">{{ title }}</h1>
            <slot></slot>
        </div>
    </div>
    
</template>

<script>
import common from '../common/common'
export default {
    props: {
        title: String
    },
    data: () => ({
        user: {}
    }),
    mounted(){
        common.request({
            type: 'get',
            url: '/api/user',
            auth: true,
            success: (resp) => {
                this.user = resp
            }, 
            error: e => {
                this.$router.push({name: 'login'})
            }
        })
    },
}
</script>