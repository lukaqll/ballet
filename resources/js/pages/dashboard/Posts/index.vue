<template>

    <div class="row">
        <div class="col-md-12 mt-3">
            <h3 class="pl-3">Posts</h3>
            <hr>
        </div>

        <div class="col-md-12">

            <div class="row" v-if="posts.length">
                <div class="col-md-4 my-2" v-for="post in posts" :key="post.id">
                    <component :is="'div'">
                        <div class="row">

                            <div class="col-md-12">
                                <small class="text-secondary ml-2">
                                    <b-icon icon="clock"></b-icon>
                                    {{ post.created_at_format }}
                                </small>
                                <b-carousel
                                    :id="`carolsel-${post.id}`"
                                    v-model="slide"
                                    :interval="6000"
                                    :controls="post.files.length > 1"
                                    :indicators="post.files.length > 1"
                                    slide
                                    @sliding-start="onSlideStart"
                                    @sliding-end="onSlideEnd"
                                >
                                    <b-carousel-slide
                                        slide
                                        v-for="src in post.files"
                                        :key="src.id"
                                        :img-src="`/storage/${src.src}`"
                                    ></b-carousel-slide>

                                </b-carousel>
                            </div>
                            <div class="col-md-12">
                                <div class="p-1">
                                    <h5>{{ post.title }}</h5>
                                    <p>{{ post.description }}</p>
                                </div>
                            </div>
                        </div>
                    </component>
                </div>
            </div>
            <h5 v-else class="text-center text-secondary">Nenhum post ainda</h5>

        </div>
    </div>
</template>

<script>
import common from '../../../common/common'

export default {

       
    mounted: function(){
        this.getPosts()
    },

    data: () => ({
        posts: [],
        filter: {},

        slide: 0,
        sliding: null
    }),

    methods: {
        getPosts(){

            common.request({
                url: '/api/posts/list-by-user',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (posts) => {
                    this.posts = posts
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
