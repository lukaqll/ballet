<template>
    <dashboard-base>
        <div>
            <div class="row">
                <div class="col-12 mt-3">
                    <h3 class="ml-3">Minha Conta</h3>
                    <hr>
                </div>
                <div class="col-12">
                    <b-card class="border-0 shadow-sm">
                        
                        <div class="row my-2">
                            <div class="col-md-4" v-if="user.picture">
                                <b-img :src="user.picture" fluid rounded alt="User Image"></b-img>
                            </div>
                            <div class="col-md-6 mt-4">
                                <b-button 
                                    variant="outline-secondary"
                                    @click="() => pictureModalShow = true"
                                >
                                    {{user.picture ? 'Alterar' : 'Adicionar'}} imagem
                                </b-button>
                            </div>
                        </div>
                        <b-form @submit.prevent="alterCredentials">
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
                                        <b-form-input disabled type="email" placeholder="E-Mail"  v-model="user.email"/>
                                    </b-form-group>
                                </div>
                                <div class="col-md-4">
                                    <b-form-group>
                                        <label>CPF</label>
                                        <b-form-input disabled class="form-control" placeholder="CPF" v-mask="'###.###.###-##'" v-model="user.cpf"/>
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




                                <div class="col-12">
                                    <div class="d-flex w-100 justify-content-between">
                                        <b-button @click="modalPassowrdShow = true">Alterar Senha</b-button>
                                        <b-button variant="primary" type="submit">Salvar</b-button>
                                    </div>
                                </div>

                            </div>
                        </b-form>
                    </b-card>
                </div>
            </div>
            
            <b-modal 
                hide-footer 
                :visible="modalPassowrdShow" 
                @hidden="onPassowrdModalHidden"
                title="Alterar Senha"
            >

                <b-form @submit.prevent="updatePasswors">
                    <div class="row">
                        <div class="col-12">
                            <b-form-group>	
                                <label>Nova Senha</label>
                                <b-form-input type="password" v-model="passwordForm.password"/>
                            </b-form-group>
                        </div>
                        <div class="col-12">
                            <b-form-group>	
                                <label>Confirme a senha</label>
                                <b-form-input type="password" v-model="passwordForm.password_confirmation"/>
                            </b-form-group>
                        </div>
                        <div class="col-12 text-right">
                            <b-button @click="onPassowrdModalHidden">Cancelar</b-button>
                            <b-button type="submit" variant="primary">
                                <b-icon icon="check"></b-icon>
                                Salvar
                            </b-button>
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

    </dashboard-base>
</template>

<script>
import common from '../../../common/common'
import DashboardBase from '../../../components/DashboardBase'
import orgaosExpeditores from "../../../params/orgaosExpeditores"
import ufs from "../../../params/ufs"

export default {
    
    components: {DashboardBase},
    computed: {
        orgaosExpeditores: function(){
            return orgaosExpeditores
        },
        ufsParam: function(){
            return ufs
        }
    },
    mounted: function() {
        this.getUser()
    },
    data: () => ({
        ufs: [
            {value: 'ES', text: 'Espírito Santo'},
            {value: 'MG', text: 'Minas Gerais'},
        ],
        user: {},
        modalPassowrdShow: false,
        passwordForm: {},
        pictureModalShow: false,
        picture: null,
    }),
    methods: {
        getUser(){
            common.request({
                type: 'get',
                url: '/api/user/client',
                auth: true,
                setError: true,
                load: true,
                success: (resp) => {
                    this.user = resp
                }
            })
        },
        alterCredentials(){
            common.request({
                type: 'put',
                url: '/api/users/self-update',
                auth: true,
                setError: true,
                savedAlert: true,
                data: this.user,
                load: true,
                success: (resp) => {
                    this.user = resp
                }
            })
        },
        onPassowrdModalHidden(){
            this.passwordForm = {}
            this.modalPassowrdShow = false
        },
        updatePasswors(){

            common.request({
                type: 'post',
                url: '/api/users/password/update',
                auth: true,
                setError: true,
                savedAlert: true,
                data: this.passwordForm,
                load: true,
                success: (resp) => {
                    this.passwordForm = {}
                    this.modalPassowrdShow = false
                }
            })
        },
        uploadPicture(){
            const formData = new FormData();
            formData.append('picture', this.picture)
            common.request({
                url: '/api/users/upload-picture',
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                load: true,
                success: (data) => {
                    this.pictureModalShow = false
                    this.picture = null
                    this.getUser()
                }
            })
        },
    },
}
</script>