<template>
    <div>
        <b-modal 
            hide-footer 
            size="xl" 
            title="Editar Usuário"
            :visible="isVisible" 
            @hidden="onHidden"
        >
        

            <div class="row">

                <div class="col-12">
                    <h4 class="h4">Dados do Usuário</h4>
                    <hr/>
                </div>
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-4">
                            <b-form-group>
                                <label>Nome</label>
                                <b-form-input placeholder="Nome"  v-model="user.name"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>E-Mail</label>
                                <b-form-input :disabled="user.signer_key ? true : false" type="email" placeholder="E-Mail"  v-model="user.email"/>
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
                                <b-form-input class="form-control" placeholder="Sua Profissão" v-model="user.profession"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Instagram</label>
                                <b-form-input class="form-control" placeholder="Instagram para marcação do aluno em posts" v-model="user.instagram"/>
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

            <!-- addess -->
            <div class="row">
                <div class="col-12 mt-3">
                    <h4 class="h4">
                        <b-icon icon="map"></b-icon>
                        Endereço
                    </h4>
                    <hr>
                </div>
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
                        <b-form-input type="text" placeholder="Cidade"  v-model="user.city"/>
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
                                    <th>Status</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in user.students" :key="student.id">
                                    <td>{{ student.name }}</td>
                                    <td>{{ student.status_text }}</td>
                                    <td>
                                        <b-badge v-if="student.open_contracts_count" variant="primary">
                                            {{student.open_contracts_count}} Contratos Abertos
                                        </b-badge>
                                    </td>
                                    <td>
                                        
                                        <b-button variant="light" size="sm" @click="editStudent($event, student.id)">
                                            <b-icon icon="pencil-square"></b-icon>
                                        </b-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <b-button variant="light" @click="addAsSignatory" v-if="!user.signer_key">
                                <b-icon icon="plus"/>
                                Adicionar como signatário
                            </b-button>
                        </div>
                        <div class="col-md-6 text-right">
                            <b-button @click="onHidden">Cancelar</b-button>
                            <b-button variant="primary" @click="save">
                                <b-icon icon="check"/>
                                Salvar
                            </b-button>
                        </div>
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
import orgaosExpeditores from "../../../params/orgaosExpeditores"
import ufs from "../../../params/ufs"
export default {

    data: () => ({
        classes: [],
        pictureModalShow: false,
        picture: null,
        ufs: [
            {value: 'ES', text: 'Espírito Santo'},
            {value: 'MG', text: 'Minas Gerais'},
        ]
    }),
    props: {
        isVisible: Boolean,
        onSave: Function,
        user: Object,
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
            this.$emit('onHidden', this.isVisible)
        },
        save(){
            common.request({
                url: '/api/users/'+this.user.id,
                type: 'put',
                data: this.user,
                auth: true,
                setError: true,
                load: true,
                savedAlert: true,
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
                load: true,
                success: (data) => {
                    this.pictureModalShow = false
                    this.picture = null
                    this.$emit('reloadUser', data)
                }
            })
        },
        addAsSignatory(){
            common.request({
                url: '/api/users/add-signatory/'+this.user.id,
                type: 'post',
                auth: true,
                setError: true,
                load: true,
                savedAlert: true,
                success: (data) => {
                    this.$emit('reloadUser', data)
                }
            })
        }

    }
}
</script>