<template>
    <div>
        <b-modal 
            hide-footer 
            size="lg" 
            :title="idStudent ? 'Editar aluno '+student.name  : 'Novo Aluno'"
            :visible="isVisible" 
            @hidden="onHidden" 
            @shown="onShown" 
        >
            <b-form>

                <div class="row">

                    <div class="col-md-8">
                        <div class="row">

                            <div class="col-12">
                                <b-form-group>
                                    <label>Usuário</label>
                                    <v-select 
                                        :options="usersOptions" 
                                        label="text"
                                        :reduce="user => user.value"
                                        v-model="student.id_user"
                                    ></v-select>
                                </b-form-group>
                            </div>

                            <div class="col-12">
                                <b-form-group>
                                    <label>Nome</label>
                                    <b-form-input placeholder="Nome"  v-model="student.name"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
                                <b-form-group>
                                    <label>Apelido</label>
                                    <b-form-input class="form-control" placeholder="Apelido" v-model="student.nick_name"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
                                <b-form-group>
                                    <label>Aniversário</label>
                                    <b-form-input type="date" v-model="student.birthdate"/>
                                </b-form-group>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">

                            <div class="col-12">
                                <b-img v-if="student.id && student.picture" :src="student.picture" fluid alt="Student Image"></b-img>
                            </div>
                            <div class="col-12">
                                <template v-if="student.id">
                                    <b-button 
                                        class="mt-2"
                                        variant="outline-secondary"
                                        @click="() => pictureModalShow = true"
                                    >
                                        {{student.picture ? 'Alterar' : 'Adicionar'}} imagem
                                    </b-button>
                                </template>

                                <template v-else>
                                    <div>
                                        <b-form-file
                                            v-model="student.picture"
                                            :state="Boolean(student.picture)"
                                            placeholder="Escolha ou arraste um arquivo..."
                                            drop-placeholder="Solte um arquivo aqui..."
                                        />
                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <b-form-group>
                            <label>Aulas</label>
                            <div>
                                <b-badge class="py-2 mr-1" variant="secondary" v-for="cl in student.classes" :key="cl.id">
                                    <span>
                                        {{cl.name}} - {{cl.unit_name}}
                                    </span>
                                    <b-icon icon="x" class="hover" @click="handleClassRemove($event, cl.id)"/>
                                </b-badge>
                                <b-button variant="primary" size="sm" @click="() => addClassModalShow = true">Adicionar</b-button>
                            </div>
                        </b-form-group>
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
            </b-form>
        </b-modal>

        <b-modal hide-footer v-model="addClassModalShow" title="Vincular Aula">
            <b-form>
                <div class="row">
                    <div class="col-12">
                        <b-form-group>
                            <label>Selecione uma aula</label>
                            <b-form-select :options="classesOptions" v-model="toAddClass"></b-form-select>
                        </b-form-group>
                    </div>
                    <div class="col-12 text-right">
                        <div>
                            <b-button @click="() => addClassModalShow = false">Cancelar</b-button>
                            <b-button variant="primary" @click="addClass">
                                <b-icon icon="check"/>
                                Adicionar
                            </b-button>
                        </div>
                    </div>
                </div>
            </b-form>
        </b-modal>

        <b-modal hide-footer v-model="pictureModalShow">
            <b-form @submit.prevent="uploadPicture">
                <div class="row">
                    <div class="col-12">
                        <div>
                            <!-- Styled -->
                            <label>Foto</label>
                            <b-form-file
                                v-model="picture"
                                :state="Boolean(picture)"
                                placeholder="Escolha ou arraste um arquivo..."
                                drop-placeholder="Drop file here..."
                            />
                            <div class="mt-3">Arquivo Selecionado: {{ picture ? picture.name : '' }}</div>
                        </div>
                    </div>
                    <div class="col-12 text-right">
                    <div>
                        <b-button @click="() => pictureModalShow = false">Cancelar</b-button>
                        <b-button variant="primary" type="submit">
                            <b-icon icon="check"/>
                            Salvar
                        </b-button>
                    </div>
                </div>
                </div>
            </b-form>
        </b-modal>

    </div>

</template>

<script>
import common from '../../../common/common'

export default {
    components: {  },

    mounted: function(){
        this.getClasses()
        this.getUsers()
    },
    data: () => ({
        student: {},
        classes: [],
        addClassModalShow: false,
        toAddClass: null,
        users: [],
        pictureModalShow: false,
        picture: null
    }),
    props: {
        isVisible: Boolean,
        onSave: Function,
        idStudent: Number,
        user: Object
    },
    computed: {
        classesOptions: function(){
            let options = []
            for( const cl of this.classes ){
                if( this.student.classes && !this.student.classes.find( _cl => _cl.id == cl.id ) ){
                    options.push({value: cl.id, text: `${cl.name} - ${cl.unit_name}`})
                }
            }
            return options
        },

        usersOptions: function(){
            return this.users.map(user => ({
                text: user.name,
                value: user.id
            }))
        }
    },
    watch: {
        idStudent: function(idStudent){
            if( idStudent ){
                common.request({
                    url: '/api/students/'+idStudent,
                    type: 'get',
                    auth: true,
                    setError: true,
                    success: (student) => {
                        this.student = student
                    }
                })
            } else {
                
            }

        }
    },
    methods: {
        onShown(){
            if( !this.idStudent ){
                this.student = {classes: [], id_user: this.user.id}
            }
        },
        onHidden(){
            this.student = {}
            this.$emit('onHidden', this.isVisible)
        },
        save(){

            
            
            if( this.idStudent ) {

                const formData = this.student
                formData.classes = this.student.classes.map(cl => cl.id)
                common.request({
                    url: '/api/students/admin-update/'+this.idStudent,
                    type: 'put',
                    data: formData,
                    auth: true,
                    setError: true,
                    success: (data) => {
                        this.$emit('onSave', data)
                    }
                })
            } else {

                const formData = new FormData();
                for( const attr in this.student ){

                    if( attr == 'classes' ){

                        for( const cl of this.student.classes)
                            formData.append('classes[]', cl.id)

                    } else if ( attr == 'picture' && typeof this.student.picture != 'string' ) {
                        formData.append('picture', this.student.picture)

                    } else {
                        formData.append(attr, this.student[attr])
                    }
                }
                common.request({
                    url: '/api/students',
                    type: 'post',
                    data: formData,
                    auth: true,
                    setError: true,
                    file: true,
                    success: (data) => {
                        this.$emit('onSave', data)
                    }
                })
            }
        },
        getClasses(){
            common.request({
                url: '/api/classes/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (classes) => {
                    this.classes = classes
                }
            })
        },
        toMoney(str){
            return common.toMoney(str)
        },
        handleClassRemove(evt, idClass){
            for( const index in this.student.classes) {
                if( this.student.classes[index].id == idClass )
                    this.student.classes.splice(index, 1)
            }
        },
        addClass(){
            if( this.toAddClass ){

                const toAddClass = this.classes.find(cl => cl.id == this.toAddClass)
                this.student.classes.push( toAddClass )
                this.toAddClass = null
                this.addClassModalShow = false
            }
        },
        getUsers(){
            common.request({
                url: '/api/users/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (users) => {
                    this.users = users
                }
            })
        },

        uploadPicture(){
            const formData = new FormData();
            formData.append('picture', this.picture)
            common.request({
                url: '/api/students/admin-upload-picture/'+this.student.id,
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                success: (student) => {
                    this.pictureModalShow = false
                    this.picture = null
                    this.student = student
                }
            })
        }

    }
}
</script>