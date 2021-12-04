<template>

    <div>
        <b-navbar class="header" id="header">

            <b-collapse id="nav-collapse" is-nav>

                <b-navbar-nav>
                    <b-nav-item >
                        <span class="text-white">
                            <b-icon id="header-toggle" :class="sibarExpanded ? 'bx-x' : ''" icon="list" @click="toggleSidebar"></b-icon>
                        </span>
                    </b-nav-item>
                </b-navbar-nav>

                <b-navbar-nav class="ml-auto">
                    <b-nav-item-dropdown right>
                        <template #button-content>
                            <em class="text-white">{{ user.name }} </em>
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
                        <router-link tag="a" class="nav_link" to='/admin/users'>
                            <b-icon icon="person-lines-fill"></b-icon>
                            Usuários
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/admin/students'>
                            <b-icon icon="person-badge"></b-icon>
                            Alunos
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/admin/units'>
                            <b-icon icon="calendar2-week"></b-icon>
                            Aulas
                        </router-link>

                        <router-link tag="a" class="nav_link" to='/admin/settings'>
                            <b-icon icon="person-circle"></b-icon>
                            Minha Conta
                        </router-link>
                        
                    </div>
                </div>
            </nav>
        </div>

        <!--Container Main start-->
        <div class="bg-light main-container">
            <b-container class="py-md-5 pd-sm-0">
                <slot></slot>
            </b-container>
        </div>
    </div>
</template>

<script>
import common from '../../common/common'
import Sidebar from './Sidebar'

export default {
    components: {Sidebar},
    props: {
        title: String
    },
    watch: {
        sibarExpanded: function(isExpanded) {
            // if( isExpanded ){
            //     $('#body-pd').addClass('body-pd')
            // } else {
            //     $('#body-pd').removeClass('body-pd')
            // }
        }  
    },
    data: () => ({
        user: {},
        sibarExpanded: false
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