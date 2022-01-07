<template>
    <div>
        <b-modal 
            hide-footer 
            size="lg" 
            :title="idPost ? 'Editar post '+post.title  : 'Novo Post'"
            :visible="isVisible" 
            @hidden="onHidden" 
            @shown="onShown" 
        >
            <div class="row">

                <div class="col-md-12">
                    <b-form-group>
                        <label>Título</label>
                        <b-form-input placeholder="Título" v-model="post.title"/>
                    </b-form-group>
                </div>

                <div class="col-md-12">
                    <b-form-group>
                        <label>Descrição</label>
                        <b-form-input placeholder="Descrição" v-model="post.description"/>
                    </b-form-group>
                </div>

                <div class="col-md-12">
                    <div class="border rounded my-1 p-2">
                        <label>Arquivos</label>
                        <b-form-file
                            multiple
                            v-model="files"
                            :state="Boolean(files.length)"
                            placeholder="Escolha ou arraste um arquivo..."
                            drop-placeholder="Solte aqui..."
                        />
                        <div class="mt-3">
                            Arquivos Selecionados:
                            <span v-for="file in files" :key="file.name">{{file.name}}, </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" v-if="idPost">
                    <div class="row">
                        <div class="col-md-3 img-container" v-for="img in post.files" :key="img.id">
                            <div class="btn-remove-image">
                                <b-button variant="danger" class="btn-sm p-0" @click="() => removeImage(img.id)">
                                    <b-icon icon="trash"></b-icon>
                                </b-button>
                            </div>
                            <img :src="'/storage/'+img.src" alt="img" class="img-fluid rounded">
                        </div>
                    </div>
                </div>


                <div class="col-12 text-right">
                    <div>
                        <b-button @click="onHidden">Cancelar</b-button>
                        <b-button variant="primary" @click="save">
                            <b-icon icon="check"/>
                            Salvar
                        </b-button>
                    </div>
                </div>
            </div>
        </b-modal>

    </div>

</template>

<script>
import common from '../../../common/common'

export default {
    components: {  },

    mounted: function(){
        
    },
    data: () => ({
        post: {},
        files: []
    }),
    props: {
        isVisible: Boolean,
        idPost: Number,
    },

    watch: {
    
    },
    methods: {
        getPost(id){

            common.request({
                url: '/api/posts/get/'+id,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (post) => {
                    this.post = post
                    this.files = []
                }
            })
        },
        onShown(){
            if( this.idPost ){
                this.getPost(this.idPost)
            }
            this.files = []
        },
        onHidden(){
            this.post = {}
            this.files = []
            this.$emit('onHidden', this.isVisible)
        },
        save(){

            if( this.idPost ) {

                const formData = new FormData();
                for( const attr in this.post ){
                    if( attr != 'files' )
                        formData.append(attr, this.post[attr])
                }

                if( this.files.length ){
                    for(const file of this.files){
                        formData.append('files[]', file)
                    }
                }

                common.request({
                    url: '/api/posts/update/'+this.idPost,
                    type: 'post',
                    data: formData,
                    auth: true,
                    setError: true,
                    load: true,
                    file: true,
                    success: (data) => {
                        this.$emit('onSave', data)
                        this.getPost(data.id)
                    }
                })
            } else {

                const formData = new FormData();
                for( const attr in this.post ){
                    formData.append(attr, this.post[attr])
                }

                for(const file of this.files){
                    formData.append('files[]', file)
                }
                
                common.request({
                    url: '/api/posts',
                    type: 'post',
                    data: formData,
                    auth: true,
                    setError: true,
                    file: true,
                    load: true,
                    success: (data) => {
                        this.$emit('onSave', data)
                        this.getPost(data.id)
                    }
                })
            }
        },

        removeImage(id){
            common.confirmAlert({
                title: 'Remover esta imagem?',
                onConfirm: () => {
                    common.request({
                    url: '/api/posts/remove-image/'+id,
                    type: 'delete',
                    auth: true,
                    setError: true,
                    load: true,
                    success: (data) => {
                        this.$emit('onSave', data)
                        this.getPost(data.id)
                    }
                })
                }
            })
        }
    }
}
</script>

<style scoped>
.btn-remove-image{
    position: absolute;
    right: 10px;
    margin: 10px;
    display: none;
}

.img-container:hover .btn-remove-image{
    display: block;
}
</style>