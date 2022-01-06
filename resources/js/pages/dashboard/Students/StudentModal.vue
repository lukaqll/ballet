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

                            <div class="col-md-12">
                                <b-form-group>
                                    <label>Nome</label>
                                    <b-form-input placeholder="Nome"  v-model="student.name"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Apelido</label>
                                    <b-form-input class="form-control" placeholder="Apelido" v-model="student.nick_name"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
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
                                </b-badge>
                            </div>
                        </b-form-group>
                    </div>

                    <div class="col-md-12" v-if="student.id">
                        <h5>Contratos</h5>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Criado Em</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="contract in student.contracts" :key="contract.id">
                                    <td>{{contract.status_text}}</td>
                                    <td>{{formartDate(contract.created_at)}}</td>
                                    <td>
                                        <a :href="`/contracts/sign/${contract.id}`" target="_blank" class="btn btn-light btn-sm" v-if="contract.status == 'running'" v-b-tooltip title="Tela de assinatura">
                                            <b-icon icon="vector-pen"/>
                                        </a>
                                        <a :href="`/contracts/view/${contract.id}`" target="_blank" class="btn btn-light btn-sm" v-b-tooltip title="Ver contrato">
                                            <b-icon icon="download"/>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
        
    },
    data: () => ({
        student: {},
        addClassModalShow: false,
        toAddClass: null,
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
        
    },
    watch: {
        idStudent: function(idStudent){
            if( idStudent ){
                this.getStudent(idStudent)
            } else {
                
            }

        }
    },
    methods: {
        getStudent(idStudent){
            common.request({
                url: '/api/students/get-self/'+idStudent,
                type: 'get',
                auth: true,
                setError: true,
                success: (student) => {
                    this.student = student
                }
            })
        },
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
                    url: '/api/students/self-update/'+this.idStudent,
                    type: 'put',
                    data: formData,
                    auth: true,
                    setError: true,
                    load: true,
                    success: (data) => {
                        this.$emit('onSave', data)
                    }
                })
            }
        },
        toMoney(str){
            return common.toMoney(str)
        },
        uploadPicture(){
            const formData = new FormData();
            formData.append('picture', this.picture)
            common.request({
                url: '/api/students/upload-picture/'+this.student.id,
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                load: true,
                success: (student) => {
                    this.pictureModalShow = false
                    this.picture = null
                    this.student = student
                }
            })
        },

        formartDate(str, hour=false){
            return common.formartDate(str, hour)
        },

        cancelContract(id){

            common.confirmAlert({
                title: 'Deseja mesmo cancelar este contrato?',
                message: 'Esta ação será irreversível',
                onConfirm: () => {
                    common.request({
                        url: '/api/contracts/cancel/'+id,
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (contract) => {
                            this.getStudent(contract.id_student)
                        }
                    })
                }
            })
        },

        createContract(){
            common.confirmAlert({
                title: 'Gerar novo contrato?',
                onConfirm: () => {
                    common.request({
                        url: '/api/contracts/generate/'+this.student.id,
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (contract) => {
                            this.getStudent(contract.id_student)
                        }
                    })
                }
            })
        },

        notify(id){

            common.request({
                url: '/api/contracts/notify/'+id,
                type: 'post',
                auth: true,
                setError: true,
                load: true,
                success: (student) => {
                    common.success({title: 'Notificações enviadas'})
                }
            })
        },

    }
}
</script>