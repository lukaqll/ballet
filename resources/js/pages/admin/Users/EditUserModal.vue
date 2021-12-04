<template>
    <div>
        <b-modal 
            hide-footer 
            size="xl" 
            title="Editar Usuário"
            :visible="isVisible" 
            @hidden="onHidden"
        >
            <b-form>

                <div class="row">

                    <div class="col-12">
                        <h4 class="h4">Dados do Usuário</h4>
                        <hr/>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Nome</label>
                                    <b-form-input placeholder="Nome"  v-model="user.name"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>CPF</label>
                                    <b-form-input class="form-control" placeholder="CPF" mask="'000.000.000-00'" v-model="user.cpf"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Telefone</label>
                                    <b-form-input placeholder="Telefone"  v-model="user.phone"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>E-Mail</label>
                                    <b-form-input type="email" placeholder="E-Mail"  v-model="user.email"/>
                                </b-form-group>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">

                            <div class="col-6" v-if="user.picture">
                                <b-img :src="user.picture" fluid alt="User Image"></b-img>
                            </div>
                            <div class="col-6">
                                <b-button 
                                    variant="outline-secondary"
                                    @click="() => pictureModalShow = true"
                                >
                                    {{user.picture ? 'Alterar' : 'Adicionar'}} imagem
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </b-form>
            <div class="row">
                <div class="col-12">
                    <h4 class="h4 mt-4">
                        Alunos
                        <b-button variant="primary" class="float-right" @click="addStudent">Novo Aluno</b-button>    
                    </h4>
                    <hr>
                </div>

                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Apelido</th>
                                    <th>Aniversário</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in user.students" :key="student.id">
                                    <td>{{ student.name }}</td>
                                    <td>{{ student.nick_name }}</td>
                                    <td>{{ student.birthdate_formated }}</td>
                                    <td>
                                        <b-button variant="outline" size="sm" @click="editStudent($event, student.id)">
                                            <b-icon variant="primary" icon="pencil-square"></b-icon>
                                        </b-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

    data: () => ({
        classes: [],
        pictureModalShow: false,
        picture: null
    }),
    props: {
        isVisible: Boolean,
        onSave: Function,
        user: Object,
    },
    methods: {
        onHidden(){
            this.$emit('onHidden', this.isVisible)
        },
        save(){
            common.request({
                url: '/api/users/'+this.user.id,
                type: 'put',
                data: this.user,
                auth: true,
                setError: true,
                success: (data) => {
                    this.$emit('onSave', data)
                }
            })
        },
        editStudent(evt, idStudent){
            this.$emit('editStudent', idStudent)
        },
        addStudent(){
            this.$emit('addStudent', this.user.id)
        },
        uploadPicture(){
            const formData = new FormData();
            formData.append('picture', this.picture)
            common.request({
                url: '/api/users/admin-upload-picture/'+this.user.id,
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                success: (data) => {
                    this.pictureModalShow = false
                    this.picture = null
                    this.$emit('reloadUser', data)
                }
            })
        }

    }
}
</script>