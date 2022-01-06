<template>

    <div id="master-wrap" class="body-pd">
        <b-navbar class="header" id="header">

            <b-collapse id="nav-collapse" is-nav>

                <b-navbar-nav>
                    <b-nav-item v-if="canExpandSidebar">
                        <span class="text-white">
                            <b-icon id="header-toggle" :class="sibarExpanded ? 'bx-x' : ''" icon="list" @click="toggleSidebar"></b-icon>
                        </span>
                    </b-nav-item>
                    <router-link tag="b-nav-item" to="/" exact>
                        <span class="text-white">
                            <span>Ellegance Ballet</span>
                        </span>
                    </router-link>
                </b-navbar-nav>

                <b-navbar-nav class="ml-auto">
                    <b-nav-item-dropdown right>
                        <template #button-content>
                            <em class="text-white">{{ user.fisrt_name }} </em>
                        </template>

                        <b-dropdown-item @click="logout">
                            <b-icon icon="power"></b-icon>
                            Sair
                        </b-dropdown-item>
                    </b-nav-item-dropdown>

                </b-navbar-nav>
            </b-collapse>

        </b-navbar>

        <div class="l-navbar" id="nav-bar" :class="sibarExpanded ? 'show' : '' ">
            <nav class="nav">
                <div>                     
                    <div class="nav_list"> 

                        <router-link tag="a" class="nav_link" to='/' exact>
                            <b-icon icon="house"></b-icon>
                            Home
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/alunos'>
                            <b-icon icon="person-badge"></b-icon>
                            Alunos
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/contratos'>
                            <b-icon icon="file-earmark-medical"></b-icon>
                            Contratos
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/posts'>
                            <b-icon icon="images"></b-icon>
                            Posts
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/minha-conta'>
                            <b-icon icon="person-circle"></b-icon>
                            Minha Conta
                        </router-link>                        
                    </div>
                </div>
            </nav>
        </div>

        <!--Container Main start-->
        <div class="bg-light main-container">
            <component :is="containerTagComponent" class="py-md-5 pd-sm-0">
                <slot></slot>
            </component>
        </div>
    </div>
</template>

<script>
import common from '../../common/common'

export default {
    props: {
        title: String,
        containerTag: String
    },
    computed: {
        containerTagComponent: function(){
            if(this.containerTag){
                return this.containerTag
            } else {

                if( window.innerWidth < 768 ){
                    return 'div'
                } else {
                    return 'b-container'
                }
            }
        },
        canExpandSidebar: function(){
            return window.innerWidth < 768
        }
    },
    watch: { 
    },
    data: function(){
        return {
            user: {},
            sibarExpanded: window.innerWidth >= 768
        }
    },
    mounted(){
        common.request({
            type: 'get',
            url: '/api/user/client',
            auth: true,
            success: (resp) => {
                this.user = resp
            }, 
            error: e => {
                this.$router.push({name: 'login'})
            }
        })
    },
    methods: {

        logout(){
            common.request({
                type: 'post',
                url: '/api/logout',
                auth: true,
                setError: true,
                success: (resp) => {
                    localStorage.removeItem('auth_token')
                    this.$router.push({name: 'login'})
                }, 
                error: e => {
                    this.$router.push({name: 'login'})
                }
            })
        },
        toggleSidebar(){
            this.sibarExpanded = !this.sibarExpanded
        }
    },

}
</script>

<style scoped>
    @import "../../../css/dashboardSidebar.css";
</style>