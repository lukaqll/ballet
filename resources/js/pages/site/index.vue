<template>
    
    <site-base :linkRefs="$refs">
        <main>

            <!-- banner -->
            <section ref="home" id="home">
                <banner/>
            </section>

            <!-- about -->
            <section ref="sobre" id="sobre">
                <b-container>
                    <div class="row my-3 justify-content-center">
                        <div class="col-md-12 my-2">
                            <h3 class="text-center text-bold">Quem Somos</h3>
                        </div>
                        <div class="col-md-4">
                            <img src="/site/images/logos/logo-1.png" alt="logo" class="img-fluid">
                        </div>
                        <div class="col-md-8">
                            <p>
                                Ellegance Ballet surgiu com o objetivo de disseminar a magia do ballet, encantando as crianças e realizando
                                sonhos.
                                Somos especialistas no ensino do Ballet Clássico.
                            </p>
                            <p>
                                Amanda Souza, a professora, é formada em ballet clássico na Companhia Brasileira de Ballet no RJ, umas das
                                maiores companhias de toda América Latina, com bagagem valiosa de conhecimentos e experiências.
                            </p>
                            <p>
                                Nosso compromisso é ensinar o verdadeiro Ballet Clássico, com toda preparação, ensino de passos, exercícios
                                de fortalecimentos e alongamentos, postura e elegância.
                                Por isso garantimos o ensino de qualidade em nosso Studio, ao qual levamos a sério o sonho de nossas
                                alunas.
                            </p>
                        </div>
                    </div>
                </b-container>
            </section>

            <!-- rebranding -->
            <section ref="rebranding" id="rebranding">
                <b-container>
                    <b-card title="Rebranding - Reposicionamento da marca" class="bg-light-pink border-0 shadow-sm">
                        <p>
                            Em janeiro de 2022 realizamos o reposicionamento da marca, Art e Dança Escola de Ballet passa a se chamar
                            <b>Ellegance Ballet</b>.
                            “A elegância de nossas alunas é notória e reconhecida na sociedade, esse nome passa a fazer mais sentido
                            atualmente para nossa empresa e para nosso público. O mundo evolui muito rápido, e nossos serviços
                            acompanham essa evolução”, conta Amanda Souza a proprietária do Studio.
                        </p>
                    </b-card>
                </b-container>
            </section>

            <!-- teacher -->
            <section ref="professora" id="professora">
                <b-container>
                    <div class="row my-3 justify-content-center">

                        <div class="col-md-12 my-3">
                            <h3 class="text-center text-bold">A Professora</h3>
                        </div>
                        <div class="col-md-7">
                            <h4 class="text-bold">Amanda Souza</h4>
                            <p>
                                Bailarina, professora de ballet clássico e jazz contemporâneo, coreógrafa, diretora artística, formada em
                                educação física com especialização em educação infantil. Proprietária e fundadora da empresa Art &amp; Dança
                                Escola de Ballet que em janeiro de 2022 passou a ser Ellegance Ballet. Mais de 14 anos de experiência na
                                área, tendo por um período participado da CIA Brasileira de Ballet no Rio e Janeiro/RJ. Já certificou mais de
                                500 alunos com sua metodologia de ensino.
                            </p>
                        </div>
                        <div class="col-md-3">
                            <img src="/site/images/professora.png" alt="professora" class="img-fluid rounded-pill">
                        </div>

                    </div>
                </b-container>
            </section>

            

            <!-- network -->
            <section ref="contato" id="contato">
                <b-container>
                    <div class="row my-5 justify-content-center">

                        <div class="col-md-4 my-2" v-for="nw in siteParam.networks" :key="nw.class">
                            <div class="d-flex justify-content-center">
                                <a :href="nw.link" target="_blank">
                                    <div :class="`net-icon ${nw.class}`"></div>
                                </a>
                            </div>
                            <h6 class="text-secondary text-center">{{ nw.text }}</h6>
                        </div>
                        
                    </div>
                </b-container>
            </section>

            <!-- classes -->
            <section ref="aulas" id="aulas" class="classes-container my-5">
                <b-container>

                    <div class="row p-3 justify-content-center" style="top: 4rem; position: relative">
                        <div class="col-md-4 my-2" v-for="cl in siteParam.classes" :key="cl.name">
                            <b-card class="border-0 shadow">
                                <h4>{{ cl.name }}</h4>
                                <p>{{ cl.description }}</p>
                                <h3>{{ cl.price }}</h3>
                                <router-link tag="a" to="/cadastro" class="btn btn-block btn-secondary rounded-pill">Quero matricular</router-link>
                            </b-card>
                        </div>

                    </div>
                </b-container>
            </section>

            <!-- gallery -->
            <section ref="galeria" id="galeria" style="margin-top: 4rem">
                <b-container>
                    <div class="row justify-content-center my-5">
                        <div class="col-md-12">
                            <h3 class="text-center">Galeria</h3>
                        </div>
                        <div class="col-md-12">
                            <b-carousel
                                id="carousel-1"
                                v-model="slide"
                                :interval="7000"
                                controls
                                indicators
                                background="#FFF"
                                img-width="1024"
                                img-height="480"
                            >
                                <!-- Text slides with image -->
                                <b-carousel-slide v-for="(chunk, index) in galleryCar" :key="index">
                                    <template #img>
                                        <div class="row">
                                            <div class="col-md-4 my-2" v-for="(img, idx) in chunk" :key="idx">
                                                <img
                                                    class="d-block img-fluid w-100 rounded"
                                                    :src="`/site/images/gallery/${img}`"
                                                    alt="image slot"
                                                >
                                            </div>
                                        </div>
                                    </template>
                                </b-carousel-slide>

                            </b-carousel>
                        </div>
                    </div>
                </b-container>
            </section>

            <!-- units -->
            <section ref="unidades" id="unidades">
                <b-container class="my-3">
                    <div class="row p-3 justify-content-center h-100">
                        <div class="col-md-12">
                            <h3 class="text-center">Unidades</h3>
                        </div>

                        <div class="col-md-4 my-2" v-for="un in siteParam.units" :key="un.name">
                            <b-card class="border-0 h-100 shadow-sm bg-light-pink">
                                <h5>
                                    <i class="fa fa-map-marker-alt"></i>
                                    {{ un.name }}
                                </h5>
                                <p>
                                    {{ un.address }}
                                    <br>
                                    {{ un.phone }}
                                </p>
                            </b-card>
                        </div>

                    </div>
                </b-container>
            </section>
        </main>
    </site-base>
</template>
<script>
import SiteBase from '../../components/SiteBase'
import Banner from './Banner.vue'
import siteParam from '../../params/siteParams'
import common from '../../common/common'
export default {
    components: {SiteBase, Banner},
    computed: {
        siteParam(){
            return siteParam
        },
        galleryCar(){

            let perChunk = 3 // items per chunk    

            let result = this.gallery.reduce((resultArray, item, index) => { 
                const chunkIndex = Math.floor(index/perChunk)

                if(!resultArray[chunkIndex]) {
                    resultArray[chunkIndex] = [] // start a new chunk
                }

                resultArray[chunkIndex].push(item)

                return resultArray
            }, [])

            return result
        }
    },
    data: () => ({
        gallery: [],
        slide: 0,
        sliding: null
    }),

    mounted(){
        this.getGallery()
    },
    methods: {
        getGallery(){
            common.request({
                url: '/api/gallery',
                type: 'get',
                success: (resp) => {
                    this.gallery = resp
                }
            })
        },

        onSlideStart(slide) {
            this.sliding = true
        },
        onSlideEnd(slide) {
            this.sliding = false
        }
    }
}
</script>

<style scoped>
    @import url('../../../css/site.css');
    
</style>