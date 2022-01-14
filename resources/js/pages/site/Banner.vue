<template>
    <div>
        <b-carousel
            id="carousel-1"
            v-model="slide"
            :interval="7000"
            controls
            indicators
            background="#FFF"
            img-width="1024"
            img-height="480"
            style="text-shadow: 1px 1px 2px #333;"
            @sliding-start="onSlideStart"
            @sliding-end="onSlideEnd"
        >
            <!-- Text slides with image -->
            <b-carousel-slide
                v-for="banner in params.banners"
                :key="banner.src"
                :img-src="banner.src"
            ></b-carousel-slide>

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
                sliding: null
            }
        },
        methods: {
            onSlideStart(slide) {
                this.sliding = true
            },
            onSlideEnd(slide) {
                this.sliding = false
            }
        }
    }
</script>