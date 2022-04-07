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

            <div>
                <b-tabs>
                    <b-tab title="Geral">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">

                                    <div class="col-md-12">
                                        <b-form-group>
                                            <label>Usuário</label>
                                            <v-select 
                                                disabled
                                                :options="usersOptions" 
                                                label="text"
                                                :reduce="user => user.value"
                                                v-model="student.id_user"
                                            ></v-select>
                                        </b-form-group>
                                    </div>

                                    <div class="col-md-12">
                                        <b-form-group>
                                            <label>Nome</label>
                                            <b-form-input placeholder="Nome"  v-model="student.name"/>
                                        </b-form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <b-form-group>
                                            <label>Aniversário</label>
                                            <b-form-input type="date" v-model="student.birthdate"/>
                                        </b-form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <b-form-group>
                                            <label>Status</label>
                                            <select v-model="student.status" class="form-control">
                                                <option value="A">Ativo</option>
                                                <option value="I">Inativo</option>
                                                <option value="MP">Matrícula Pendente</option>
                                                <option value="CP">Contrato Pendente</option>
                                            </select>
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

                            <div class="col-md-4">
                                <b-form-group>
                                    <label>Problema de saúde</label>
                                    <b-form-input v-if="student.health_problem" disabled placeholder="Problema de saúde" class="w-100" v-model="student.health_problem"/>
                                    <span v-else> <br> Nenhum problema de saúde</span>
                                </b-form-group>
                            </div>

                            <div class="col-md-4">
                                <b-form-group>
                                    <label>Restrição alimentar</label>
                                    <b-form-input v-if="student.food_restriction" disabled placeholder="Retrição alimentar" class="w-100" v-model="student.food_restriction"/>
                                    <span v-else> <br> Nenhuma restrição alimentar</span>
                                </b-form-group>
                            </div>

                            
                            <div class="col-md-4">
                                <b-form-group>
                                    <label>Ensino regular</label>
                                    <b-form-input v-if="student.school_time" disabled placeholder="Ex.: de segunda à sexta, matutino"  class="w-100" v-model="student.school_time"></b-form-input>
                                    <span v-else> <br> Não está no ensino regular</span>
                                </b-form-group>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="Aulas">
                        <div class="row">
                            <div class="col-md-12 my-4">
                                <h5>
                                    Aulas
                                    <b-button variant="light"  @click="() => addClassModalShow = true" class="btn-sm float-right">Adicionar Aula</b-button>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Turma</th>
                                                <th>Unidade</th>
                                                <th>Valor R$</th>
                                                <th>Aprovado Em</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="cl in student.student_classes" :key="cl.id">
                                                <td>{{cl.class.name}}</td>
                                                <td>{{cl.class.team}}</td>
                                                <td>{{cl.unit_name}}</td>
                                                <td>{{toMoney(cl.class.value)}}</td>
                                                <td>{{cl.approved_at ? formartDate(cl.approved_at) : 'Não aprovado'}}</td>
                                                <td>
                                                    <b-button variant="danger" size="sm" @click="() => removeClass(cl.class.id)">
                                                        <b-icon icon="x"/>
                                                    </b-button>
                                                    <b-button v-if="!cl.contract" variant="light" @click="() => createContract(cl.class.id)" class="btn-sm">
                                                        Gerar Contrato
                                                    </b-button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="Contratos">
                        <div class="row">
                            <div class="col-md-12" v-if="student.id">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Aula</th>
                                            <th>Criado Em</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="contract in student.contracts" :key="contract.id">
                                            <td>{{contract.status_text}}</td>
                                            <td>{{contract.class ? `${contract.class.name} (${contract.class.team})` : '' }}</td>
                                            <td>{{contract.created_at_format}}</td>
                                            <td>
         
                                                <b-dropdown :id="'dropdown-'+contract.id" size="sm" variant='light'>
                                                    <template #button-content >
                                                        <b-icon icon="three-dots-vertical"></b-icon>
                                                    </template>
                                                    <b-dropdown-item :href="`/contracts/view/${contract.id}`" target="_blank" size="sm">
                                                        Ver contrato
                                                    </b-dropdown-item>
                                                    <b-dropdown-item :href="`/contracts/sign/${contract.id}`" target="_blank" v-if="contract.status == 'running'" size="sm">
                                                        Assinar
                                                    </b-dropdown-item>
                                                    <b-dropdown-item v-if="contract.status == 'running'" size="sm" @click="() => notify(contract.id)">
                                                        Notificar
                                                    </b-dropdown-item>
                                                    <b-dropdown-item @click="() => cancelContract(contract.id)" v-if="contract.status != 'canceled'">
                                                        <span class='text-danger'>Cancelar</span>
                                                    </b-dropdown-item>
                                                </b-dropdown>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>
            </div>
            <div class="row mt-3">
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

        <b-modal hide-footer v-model="addClassModalShow" title="Vincular Aula">
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
                    options.push({value: cl.id, text: `${cl.name} (${cl.team}) - ${cl.unit_name}`})
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
                this.getStudent(idStudent)
            } else {
                
            }

        }
    },
    methods: {
        getStudent(idStudent){
            common.request({
                url: '/api/students/'+idStudent,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
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
                    url: '/api/students/admin-update/'+this.idStudent,
                    type: 'put',
                    data: formData,
                    auth: true,
                    setError: true,
                    load: true,
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
                    load: true,
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
        removeClass(idClass){
            common.confirmAlert({
                title: 'Remover esta aula deste aluno?',
                onConfirm: () => {
                    common.request({
                        url: `/api/student-class/${this.student.id}/${idClass}`,
                        type: 'delete',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (std) => {
                            this.getStudent(this.student.id)
                        }
                    })
                }
            })
        },
        addClass(){
            if( this.toAddClass ){    
                common.confirmAlert({
                    title: 'Matricular este aula à este aluno?',
                    message: `Será gerado um novo contrato se não haver.<br>
                                A matricula será aprovada após a assinatura do contrato, <br>
                                caso já haja um contrato assinado para esta aula, a aprovação será imediata.`,
                    onConfirm: () => {
                        common.request({
                            url: `/api/student-class/${this.student.id}/${this.toAddClass}`,
                            type: 'post',
                            auth: true,
                            setError: true,
                            load: true,
                            success: (std) => {
                                this.getStudent(this.student.id)
                                this.toAddClass = null
                                this.addClassModalShow = false
                            }
                        })
                    }
                })          
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

        createContract(idClass){
            common.confirmAlert({
                title: 'Gerar novo contrato?',
                onConfirm: () => {
                    common.request({
                        url: `/api/contracts/generate/${this.student.id}/${idClass}`,
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