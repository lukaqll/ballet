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
                            <div class="col-6">
                                <b-form-group>
                                    <label>Nome</label>
                                    <b-form-input placeholder="Nome"  v-model="user.name"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
                                <b-form-group>
                                    <label>CPF</label>
                                    <b-form-input class="form-control" placeholder="CPF" mask="'000.000.000-00'" v-model="user.cpf"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
                                <b-form-group>
                                    <label>Telefone</label>
                                    <b-form-input placeholder="Telefone"  v-model="user.phone"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
                                <b-form-group>
                                    <label>E-Mail</label>
                                    <b-form-input type="email" placeholder="E-Mail"  v-model="user.email"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
                                <b-form-group>
                                    <label>Senha</label>
                                    <b-form-input type="password" placeholder="Senha"  v-model="user.password"/>
                                </b-form-group>
                            </div>
                            <div class="col-6">
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
                                    <b-form-select :options="['selecione']" class="w-100" v-model="student.id_class"></b-form-select>
                                </b-form-group>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-12 text-right">
                        <div>
                            <b-button>Cancelar</b-button>
                            <b-button variant="primary">
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

export default {

    data: () => ({
        user: {},
        student: {}
    }),
    props: {
        isVisible: Boolean
    },
    methods: {
        onHidden(){
            this.user = {}
            this.student = {}
            this.$emit('onHidden', this.isVisible)
        }
    }
}
</script>