<template>
    <div>
        <b-navbar toggleable="lg" type="dark" id="main-nav" sticky>
            <b-navbar-brand href="#">Ellegance Ballet</b-navbar-brand>

            <b-navbar-nav class="d-lg-none d-xl-none d-md-none">
                <b-nav-item href="#"  v-b-toggle.sidebar-no-header>
                    <b-icon icon="list" ></b-icon>
                </b-nav-item>
            </b-navbar-nav>
            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    
                    
                </b-navbar-nav>
                <b-navbar-nav>
                    <b-nav-item-dropdown right>
                        <template #button-content>
                            <em class="text-white">{{currentLink}} </em>
                        </template>
                        <b-dropdown-item v-for="(link, idx) in links" :key="idx" @click="() => goto(link.ref)">
                            {{link.text}}
                        </b-dropdown-item>

                        <router-link tag="b-dropdown-item" to="/password-recovery">
                            Esqueci Minha Senha
                        </router-link>
                    </b-nav-item-dropdown>
                </b-navbar-nav>

                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">

                
                    <b-navbar-nav right v-if="!user">
                        <b-form inline @submit.prevent="login">
                            <b-form-input size='sm' placeholder="Seu E-mail" type="email" class="border-0 rounded-pill mr-1" v-model="loginData.email"></b-form-input>
                            <b-form-input size='sm' placeholder="Sua Senha" type="password" class="border-0 rounded-pill mr-1" v-model="loginData.password"></b-form-input>
                            <button class="btn btn-sm btn-link text-white" @click="login">
                                Entrar
                            </button>
                        </b-form>
                        <b-nav-item>
                            <router-link tag="a" to="/cadastro" class="btn btn-sm btn-outline-light rounded-pill">
                                <b-icon icon="person-plus"></b-icon>
                                Matricular
                            </router-link>
                        </b-nav-item>
                    </b-navbar-nav>

                    <b-navbar-nav v-else>
                        <b-nav-item-dropdown right>
                            <template #button-content>
                                <em class="text-white">Olá, {{ user.first_name }} </em>
                            </template>

                            <router-link tag="b-dropdown-item" to="/admin" v-if="user.is_admin == 1">
                                Área Admistrativa
                            </router-link>
                            <router-link tag="b-dropdown-item" to="/home" v-else>
                                Minha Ellegance Ballet
                            </router-link>


                            <b-dropdown-item @click="logout">
                                <b-icon icon="power"></b-icon>
                                Sair
                            </b-dropdown-item>
                        </b-nav-item-dropdown>

                    </b-navbar-nav>

                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <slot/>

        <footer>
            <b-container class="py-2">
                <div class="row">
                    <div class="col-md-4 text-white">
                        <p v-for="nw in siteParam.networks" :key="nw.text">
                            <a>
                                <i :class="`fab fa-${nw.class}`"></i>
                                {{nw.text}}
                            </a>
                        </p>
                    </div>
                </div>
            </b-container>
        </footer>

        <div>
            <b-sidebar id="sidebar-no-header" aria-labelledby="sidebar-no-header-title" no-header backdrop shadow>
            <template>
                <div class="p-3">
                    <h4>Ellegance Ballet</h4>
                    <h5 v-if="user">Olá, {{user.first_name}}</h5>
                    <nav class="mb-3">
                        <b-nav vertical>

                            <b-navbar-nav>
                                <b-nav-item v-for="(link, idx) in links" :key="idx" @click="() => goto(link.ref)">
                                    {{link.text}}
                                </b-nav-item>
                                <router-link tag="b-nav-item" to="/admin" v-if="user && user.is_admin == 1">
                                    Área Admistrativa
                                </router-link>
                                <router-link tag="b-nav-item" to="/home" v-else-if="user && user.is_admin != 1">
                                    Minha Ellegance Ballet
                                </router-link>
                                <b-nav-item @click="logout" v-if="user">
                                    <b-icon icon="power"></b-icon>
                                    Sair
                                </b-nav-item>
                            </b-navbar-nav>
                            
                            <b-form v-if="!user" @submit.prevent="login" class="mt-5">
                                <h6>Login</h6>
                                <b-form-input placeholder="Seu E-mail" type="email" class="border-0 rounded-pill my-1" v-model="loginData.email"></b-form-input>
                                <b-form-input placeholder="Sua Senha" type="password" class="border-0 rounded-pill my-1" v-model="loginData.password"></b-form-input>
                                <button class="btn btn-block bg-light-pink rounded-pill" @click="login">
                                    Entrar
                                </button>
                                <div class="mt-2">
                                    <router-link tag="a" to='/password-recovery' >
                                        Esqueci minha senha
                                    </router-link>
                                </div>
                            </b-form>
                        </b-nav>
                    </nav>
                </div>
            </template>
            </b-sidebar>
        </div>
    </div>
</template>

<script>
import common from '../../common/common'
import siteParam from '../../params/siteParams'
export default {
    props: {
        linkRefs: Object
    },
    computed: {
        siteParam(){
            return siteParam
        }
    },
    data: () => ({
        user: null,
        loginData: {},
        links: [
            {text: 'Sobre Nós', ref: 'sobre'},
            {text: 'A Professora', ref: 'professora'},
            {text: 'Contato', ref: 'contato'},
            {text: 'Aulas', ref: 'aulas'},
            {text: 'Galeria', ref: 'galeria'},
            {text: 'Unidades', ref: 'unidades'},
        ],
        currentLink: 'Início'
    }),
    mounted(){
        this.getUser()
        this.getButton()
    },
    methods: {
        getUser(){
            common.request({
                type: 'get',
                url: '/api/user/commom',
                auth: true,
                success: (resp) => {
                    this.user = resp
                }
            })
        },

        logout(){
            common.request({
                type: 'post',
                url: '/api/logout',
                auth: true,
                setError: true,
                success: (resp) => {
                    localStorage.removeItem('auth_token')
                    this.user = null
                },
            })
        },

        login() {

            common.request({
                url: '/api/login',
                type: 'post',
                data: this.loginData,
                setError: true,
                load: true,
                success: (resp) => {
                    localStorage.setItem('auth_token', resp.access_token)
                    this.$router.push({ path: resp.redirect_to || '/'} )
                }
            })

        },

        getButton(){
            var options = {
                whatsapp: "+5527998650476", // WhatsApp number
                call_to_action: "Envia-nos uma mensagem", // Call to action
                position: "right", // Position may be 'right' or 'left'
            };
            var proto = document.location.protocol, host = "getbutton.io", url = proto + "//static." + host;
            var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
            s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
            var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
        },

        goto(refName) {
            let element = this.linkRefs[refName];
            let top = element.offsetTop;
            this.currentLink = refName
            window.scrollTo(0, top-100);
        }   
    }
}
</script>
<style scoped>
#main-nav{
    background-color: #e979c4 !important;
}
</style>