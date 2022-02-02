<template>
    <div>
        <b-modal hide-footer :visible="isVisible" @hidden="onHidden" size="xl" title="Novo Usuário">
            <div class="row">

                <div class="col-12">
                    <h4 class="h4">Dados do Usuário</h4>
                    <hr/>
                </div>
                <div class="col-md-9">
                    <div class="row">

                        <div class="col-md-4">
                            <b-form-group>
                                <label>Nome Completo</label>
                                <b-form-input placeholder="Nome" v-model="user.name"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>E-mail</label>
                                <b-form-input type="email" placeholder="E-mail"  v-model="user.email"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>CPF</label>
                                <b-form-input class="form-control" placeholder="CPF" v-mask="'###.###.###-##'" v-model="user.cpf"/>
                            </b-form-group>
                        </div>

                        <div class="col-md-4">
                            <b-form-group>
                                <label>Data de Nascimento</label>
                                <b-form-input type="date" v-model="user.birthdate"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Telefone</label>
                                <b-form-input placeholder="Telefone" v-mask="'(##) #####-####'" v-model="user.phone"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>WhatsApp</label>
                                <select class="form-control" v-model="user.is_whatsapp">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </b-form-group>
                        </div>

                        <div class="col-md-4">
                            <b-form-group>
                                <label>RG</label>
                                <b-form-input class="form-control" placeholder="Numero do RG" v-model="user.rg"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Órgão Expeditor</label>
                                <b-form-select :options="orgaosExpeditores" class="w-100" v-model="user.orgao_exp"></b-form-select>
                            </b-form-group>
                        </div>

                        <div class="col-md-4">
                            <b-form-group>
                                <label>UF do Órgão Expeditor</label>
                                <b-form-select :options="ufsParam" class="w-100" v-model="user.uf_orgao_exp"></b-form-select>
                            </b-form-group>
                        </div>
                            
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Profissão</label>
                                <b-form-input class="form-control" placeholder="Profissão" v-model="user.profession"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Instagram</label>
                                <b-form-input class="form-control" placeholder="Instagram para marcação do aluno em posts" v-model="user.instagram"/>
                            </b-form-group>
                        </div>

                        <div class="col-md-6">
                            <b-form-group>
                                <label>Senha</label>
                                <b-form-input :disabled="sendPasswordMail" type="password" placeholder="Senha"  v-model="user.password"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-6">
                            <b-form-group>
                                <label>Confirmação da Senha</label>
                                <b-form-input :disabled="sendPasswordMail" type="password" placeholder="Confirme a senha"  v-model="user.password_confirmation"/>
                            </b-form-group>
                        </div>

                        
                        <div class="col-md-6">
                            <b-form-group>
                                <b-form-checkbox v-model="sendPasswordMail" name="check-button" switch>
                                    Enviar E-Mail para recuperação de senha
                                </b-form-checkbox>
                            </b-form-group>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <template>
                        <div>
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
                <!-- addess -->
                <div class="col-md-4">
                    <b-form-group>
                        <label>CEP</label>
                        <b-form-input type="text" placeholder="CEP"  v-model="user.cep" v-mask="'#####-###'"/>
                    </b-form-group>
                </div>
                <div class="col-md-4">
                    <b-form-group>
                        <label>UF</label>
                        <b-form-select :options="ufs" class="w-100" v-model="user.uf"></b-form-select>
                    </b-form-group>
                </div>
                <div class="col-md-4">
                    <b-form-group>
                        <label>Cidade</label>
                        <b-form-input type="text" placeholder="cidade"  v-model="user.city"/>
                    </b-form-group>
                </div>

                <div class="col-md-3">
                    <b-form-group>
                        <label>Bairro</label>
                        <b-form-input type="text" placeholder="Bairro"  v-model="user.district"/>
                    </b-form-group>
                </div>
                <div class="col-md-4">
                    <b-form-group>
                        <label>Logradouro</label>
                        <b-form-input type="text" placeholder="Rua / Av."  v-model="user.street"/>
                    </b-form-group>
                </div>

                <div class="col-md-2">
                    <b-form-group>
                        <label>Nº</label>
                        <b-form-input type="text" placeholder="Nº"  v-model="user.address_number"/>
                    </b-form-group>
                </div>

                <div class="col-md-3">
                    <b-form-group>
                        <label>Complemento</label>
                        <b-form-input type="text" placeholder="Ap 101 / Predio X"  v-model="user.address_complement"/>
                    </b-form-group>
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
        </b-modal>
    </div>
</template>

<script>
import common from '../../../common/common'
import orgaosExpeditores from "../../../params/orgaosExpeditores"
import ufs from "../../../params/ufs"

export default {

    data: () => ({
        user: {},
        student: {},
        classes: [],
        sendPasswordMail: false,
        ufs: [
            {value: 'ES', text: 'Espírito Santo'},
            {value: 'MG', text: 'Minas Gerais'},
        ]
    }),
    props: {
        isVisible: Boolean,
    },
    mounted: function() {
        this.getClasses()
    },
    watch: {
        sendPasswordMail: function(willSend) {
            if(willSend){
                this.user.password_confirmation = null;
                this.user.password = null;
            }
        }
    },
    computed: {
        orgaosExpeditores: function(){
            return orgaosExpeditores
        },
        ufsParam: function(){
            return ufs
        }
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

            formData.append('send_password_mail', this.sendPasswordMail)
            
            common.request({
                url: '/api/users/with-students',
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