<template>

    <div>
        <b-navbar toggleable="lg" type="dark" variant="info">
            <b-navbar-brand >
                <router-link class="text-white" to='/admin' exact>Ballet</router-link> 
            </b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>

                    <b-nav-item-dropdown right>
                        <template #button-content>
                            <span class="text-white">
                                Usuários
                            </span>
                        </template>

                        <b-dropdown-item >
                            <router-link tag="b-dropdown-item" to='/admin/users'>
                                <b-icon icon="person-lines-fill"></b-icon>
                                Usuários
                            </router-link>
                        </b-dropdown-item>
                        <b-dropdown-item >
                            <router-link tag="b-dropdown-item" to='/admin/students'>
                                <b-icon icon="person-badge"></b-icon>
                                Alunos
                            </router-link>
                        </b-dropdown-item>
                    </b-nav-item-dropdown>

                    <b-nav-item>
                        <router-link class="text-white" to='/admin/units'>Unidades</router-link>
                    </b-nav-item>
                    
                </b-navbar-nav>

                <b-navbar-nav class="ml-auto">

                    <b-nav-item-dropdown right>
                        <template #button-content>
                            <em>{{ user.name }}</em>
                        </template>

                        <b-dropdown-item >
                            <router-link to='/login' exact>Login</router-link>
                        </b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>

        <b-container class="py-5">
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
<style>
    body{
        background-color: #f8fcff !important;
    }
</style>