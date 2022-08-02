<template>
    <div>
        
        <div class="row">

            <div class="col-12">
                <div>
                    <b-card no-body class="border-0 shadow-sm">

                        <b-card-body>
                            <div class="row">
                                <div class="col-12">
                                    <h3>
                                        Posts    
                                        <b-button class="float-right" variant="primary" @click="() => postModalShow = true">Novo Post</b-button>
                                    </h3>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive" v-if="posts.length">
                                    <data-table
                                        :rows="posts"
                                        :columns="postsBindings"
                                        locale="br"
                                        title=''
                                        :perPage="[50, 100, 200]"
                                        :clickable="false"
                                    >
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
                                            <td>
                                                <b-button variant="light" size="sm" @click="e => getPost(props.row.id)">
                                                    <b-icon icon="pencil-square"></b-icon>
                                                </b-button>                                                
                                            </td>
                                        </template>
                                    </data-table>
                                </div>
                                <div v-else>Nenhum post ainda</div>

                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>

        <post-modal
            :isVisible="postModalShow"
            @onHidden="() => {
                postModalShow = false
                idEditablePost = null
            }"
            :idPost="idEditablePost"
            @onSave="onSavePost"
        />
        
    </div>
</template>

<script>
import common from '../../../common/common'
import PostModal from './PostModal'
import DataTable from "vue-materialize-datatable";

export default {
    components: { DataTable, PostModal },

    computed: {
        postsBindings(){
            return  [
                    {field: 'title', label: 'Título'},
                    {field: 'description', label: 'Descrição'},
                    {field: 'created_at_format', label: 'Postado Em'},
                ]
        },

    },

       
    mounted: function(){
        this.getPosts()
    },

    data: () => ({
        posts: [],
        filter: {},
        post: {},
        postModalShow: false,
        idEditablePost: null
    }),

    methods: {
        getPosts(){

            let filters = {}
            for(const key in this.filter){
                if( this.filter[key] )
                    filters[key] = this.filter[key]
            }
            let queryString = new URLSearchParams(filters)

            common.request({
                url: '/api/posts/list?'+queryString,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (posts) => {
                    this.posts = posts
                }
            })
        },

        getPost(id){
            this.idEditablePost = id
            this.postModalShow = true
        },
        onSavePost(post){
            this.getPosts()
            this.getPost(post.id)
        }
    }
}
</script>
