<template>

    <div>
        <b-navbar toggleable="lg" type="dark" variant="info">
            <b-navbar-brand href="#">ADMIN Panel</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
            <b-navbar-nav>
                <b-nav-item >
                    <router-link class="text-white" to='/admin' exact>Home</router-link>
                </b-nav-item>
                <b-nav-item>
                    <router-link class="text-white" to='/admin/users'>Usuários</router-link>
                </b-nav-item>
                
            </b-navbar-nav>

            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">

                <b-nav-item-dropdown right>
                    <template #button-content>
                        <em>{{ user.name }}</em>
                    </template>
                <b-dropdown-item href="#">
                    
                </b-dropdown-item>
                    <b-dropdown-item href="#">
                        <router-link to='/login' exact>Login</router-link>
                    </b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <b-container class="py-md-5">
            <slot></slot>
        </b-container>
    </div>
    
</template>

<script>
import common from '../../common/common'
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