<template>
    <div>
        <b-carousel
            id="carousel-1"
            v-model="slide"
            :interval="7000"
            controls
            indicators
            background="#FFF"
            fade
        >
            <b-carousel-slide
                v-for="(banner, idx) in params.banners"
                :key="idx"
                :img-src="banner.src"
            />

        </b-carousel>

    </div>
</template>

<script>
    import siteParams from '../../params/siteParams.js'
    export default {
        computed: {
            params(){
                return siteParams
            }
        },
        data() {
            return {
                slide: 0,
            }
        },
    }
</script>