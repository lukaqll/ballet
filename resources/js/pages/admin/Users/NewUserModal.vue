<template>
    <div>
        <b-modal hide-footer :visible="isVisible" @hidden="onHidden" size="xl" title="Novo Usuário">
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
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Senha</label>
                                    <b-form-input type="password" placeholder="Senha"  v-model="user.password"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Confirmação da Senha</label>
                                    <b-form-input type="password" placeholder="Confirme a senha"  v-model="user.password_confirmation"/>
                                </b-form-group>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <template>
                            <div>
                                <!-- Styled -->
                                <label>Foto</label>
                                <b-form-file
                                    v-model="user.picture"
                                    :state="Boolean(user.picture)"
                                    placeholder="Escolha ou arraste um arquivo..."
                                    drop-placeholder="Drop file here..."
                                />
                                <div class="mt-3">Arquivo Selecionado: {{ user.picture ? user.picture.name : '' }}</div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h4 class="h4 mt-4">Dados do Aluno</h4>
                        <hr>
                    </div>

                    <div class="col-md-8">
                        <div class="row">

                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Nome</label>
                                    <b-form-input placeholder="Nome do Aluno" v-model="student.name"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Apelido</label>
                                    <b-form-input placeholder="Apelido do Aluno" v-model="student.nick_name"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Data de Nascimento</label>
                                    <b-form-input type="date" v-model="student.birthdate"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-6">
                                <b-form-group>
                                    <label>Aula</label>
                                    <b-form-select :options="classes" class="w-100" v-model="student.id_class"></b-form-select>
                                </b-form-group>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-md-4">
                        <template>
                            <div>
                                <!-- Styled -->
                                <label>Foto</label>
                                <b-form-file
                                    v-model="student.picture"
                                    :state="Boolean(student.picture)"
                                    placeholder="Escolha ou arraste um arquivo..."
                                    drop-placeholder="Drop file here..."
                                />
                                <div class="mt-3">Arquivo Selecionado: {{ student.picture ? student.picture.name : '' }}</div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="row mt-5">
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
    </div>
</template>

<script>
import common from '../../../common/common'

export default {

    data: () => ({
        user: {},
        student: {},
        classes: []
    }),
    props: {
        isVisible: Boolean,
    },
    mounted: function() {
        this.getClasses()
    },
    methods: {
        onHidden(){
            this.user = {}
            this.student = {}
            this.$emit('onHidden', this.isVisible)
        },
        save(){

            const formData = new FormData()

            for( const attr in this.user )
                formData.append(`user_${attr}`, this.user[attr])

            for( const attr in this.student )
                formData.append(`student_${attr}`, this.student[attr])
            
            common.request({
                url: '/api/users/with-students',
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                success: (data) => {
                    this.$emit('onSave', data)
                }
            })
        },

        getClasses(){
            common.request({
                url: '/api/classes/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (classes) => {
                    this.classes = classes.map(cl => (
                        {value: cl.id, text: `${cl.name} - ${cl.unit_name}`})
                    )
                }
            })
        }
    }
}
</script>