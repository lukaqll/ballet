<template>
    <div>
        <b-modal 
            hide-footer 
            size="xl" 
            title="Editar Usuário"
            :visible="isVisible" 
            @hidden="onHidden"
        >
        
            <div>
                <b-tabs content-class="mt-3">
                
                    <b-tab title='Dados do Usuário' active>
                        <div class="row">
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
                                    <div class="col-md-4">
                                        <b-form-group>
                                            <label>Status</label>
                                            <select class="form-control" v-model="user.status">
                                                <option value="A">Ativo</option>
                                                <option value="I">Inativo</option>
                                                <option value="P">Inadimplente</option>
                                                <option value="CP">Contrato Pendente</option>
                                            </select>
                                        </b-form-group>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="row">

                                    <div class="col-6" v-if="user.picture">
                                        <b-img :src="user.picture" fluid alt="User Image"></b-img>
                                    </div>
                                    <div class="col-12">
                                        <b-button 
                                            variant="outline-secondary"
                                            @click="() => pictureModalShow = true"
                                        >
                                            {{user.picture ? 'Alterar' : 'Adicionar'}} imagem
                                        </b-button>
                                        <b-button
                                            variant="outline-secondary"
                                            @click="getFiles"
                                        >
                                            Arquivos
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-tab>

                    <b-tab title="Endereço">
                        <div class="row">
                            <div class="col-md-3">
                                <b-form-group>
                                    <label>CEP</label>
                                    <b-form-input type="text" placeholder="CEP"  v-model="user.cep" v-mask="'#####-###'"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-3">
                                <b-form-group>
                                    <label>UF</label>
                                    <b-form-select :options="ufs" class="w-100" v-model="user.uf"></b-form-select>
                                </b-form-group>
                            </div>
                            <div class="col-md-3">
                                <b-form-group>
                                    <label>Cidade</label>
                                    <b-form-input type="text" placeholder="Cidade"  v-model="user.city"/>
                                </b-form-group>
                            </div>
                            <div class="col-md-3">
                                <b-form-group>
                                    <label>Unidade</label>
                                    <b-form-select :options="units" class="w-100" v-model="user.id_unit" :value="user.id_unit"></b-form-select>
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
                    </b-tab>

                    <b-tab title="Alunos">
                        <div class="row">
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
                        </div>
                    </b-tab>

                    <b-tab title="Adicionais de Fatura">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <b-button @click="() => this.$bvModal.show('invoice-add-modal')" variant="primary" class="float-right">Novo</b-button>
                                    <table class="table table-striped table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Desc.</th>
                                                <th>Valor</th>
                                                <th>Mês</th>
                                                <th>Fatura</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="added in user.invoice_adds" :key="added.id">
                                                <td>{{ added.description }}</td>
                                                <td>R$ {{ toMoney(added.value) }}</td>
                                                <td>{{ monthFormat(added.month) }}</td>
                                                <td>{{ added.id_invoice }}</td>
                                                <td>
                                                    <div v-if="!added.id_invoice">
                                                        <b-button variant="light" size="sm" @click="() => editInvoiceAdd(added.id)">
                                                            <b-icon icon="pencil-square"></b-icon>
                                                        </b-button>
                                                        <b-button variant="danger" size="sm" @click="() => deleteInvoiceAdds(added.id)">
                                                            <b-icon icon="trash"></b-icon>
                                                        </b-button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <b-button variant="light" @click="addAsSignatory" v-if="!user.signer_key">
                        <b-icon icon="plus"/>
                        Adicionar como signatário
                    </b-button>

                    <b-button variant="danger" @click="inactivate" v-if="user.status != 'I'">
                        <b-icon icon="power"/>
                        Inativar
                    </b-button>
                    <b-button variant="success" @click="activate" v-if="user.status == 'I'">
                        <b-icon icon="power"/>
                        Ativar
                    </b-button>

                    <b-button variant="light" @click="() => $bvModal.show('delete-confirmation')">
                        <b-icon icon="trash"/>
                        Deletar
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

            <!-- addess -->

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

        <b-modal  hide-footer id="delete-confirmation">
            <div>
                <h2 class="text-center">Deseja deletar este usuário?</h2>
                <p class="text-center">
                    Esta ação será irreversível
                    <br>
                    Digite a palavra 'confirmar' para deletar
                </p>
                <b-form-input v-model="confirmationDelete"></b-form-input>
                <div class="text-right mt-3">
                    <b-button @click="() => {$bvModal.hide('delete-confirmation'); confirmationDelete=''}">Cancelar</b-button>
                    <b-button variant="danger" @click="deleteUser">
                        Deletar
                    </b-button>
                </div>
            </div>
        </b-modal>

        <b-modal size="lg" title="Arquivos" hide-footer id="registration-files">
            <div class="row" v-if="registrationFiles.length">
                <div class="col-md-4"  v-for="file in registrationFiles" :key="file.id">
                    <a class="btn btn-sm btn-light position-absolute" target="_blank" :href="'/storage/'+file.name">
                        <b-icon icon="eye"></b-icon>
                    </a>
                    <component 
                        :is="file.extention == 'pdf' ? 'embed' : 'img'" 
                        :src="'/storage/'+file.name" alt="file" 
                        :class="file.extention == 'pdf' ? 'w-100 h-100' : 'img-fluid'"
                    ></component>
                </div>
            </div>
            <h3 class="text-center" v-else>Nenhum Arquivo</h3>
        </b-modal>

        <b-modal size="md" title="Adicional" hide-footer id="invoice-add-modal">
            <div class="row">
                <div class="col-md-12">
                    <b-form-group>
                        <label>Descrição</label>
                        <b-form-input v-model="invoiceAdd.description"/>
                    </b-form-group>
                    <b-form-group>
                        <label>Valor</label>
                        <b-form-input v-model="invoiceAdd.value" v-money="{decimal: ',', thousands: '.', precision: 2}"/>
                    </b-form-group>
                    <b-form-group>
                        <label>Mês</label>
                        <b-form-input v-model="invoiceAdd.month" type='month'/>
                    </b-form-group>
                </div>
                <div class="col-md-12">
                    <b-button @click="() => this.$bvModal.hide('invoice-add-modal')">Cancelar</b-button>
                    <b-button variant="primary" @click="saveInvoiceAdds">
                        <b-icon icon="check"/>
                        Salvar
                    </b-button>
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
        classes: [],
        pictureModalShow: false,
        picture: null,
        confirmationDelete: '',
        ufs: [
            {value: 'ES', text: 'Espírito Santo'},
            {value: 'MG', text: 'Minas Gerais'},
        ],
        registrationFiles: [],
        units: [],
        invoiceAdd: {}
    }),
    props: {
        isVisible: Boolean,
        onSave: Function,
        user: Object,
    },
    mounted: function() {
        this.getUnits()
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
        },

        inactivate() {
            common.confirmAlert({
                title: 'Inativar este usuário?',
                onConfirm: () => {
                    common.request({
                        url: '/api/users/inactivate/'+this.user.id,
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        savedAlert: true,
                        success: (data) => {
                            this.$emit('reloadUser', data)
                            this.$emit('onSave', data)
                        }
                    })
                }
            })
        },

        activate() {
            common.confirmAlert({
                title: 'Ativar este usuário?',
                onConfirm: () => {
                    common.request({
                        url: '/api/users/activate/'+this.user.id,
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        savedAlert: true,
                        success: (data) => {
                            this.$emit('reloadUser', data)
                            this.$emit('onSave', data)
                        }
                    })
                }
            })
        },

        deleteUser() {

            if( this.confirmationDelete != 'confirmar' ){
                common.setError({
                    message: 'Informe o termo correto para confirmar'
                })
                return
            }

            common.request({
                url: '/api/users/'+this.user.id,
                type: 'delete',
                auth: true,
                setError: true,
                load: true,
                data: {
                    confirmation: this.confirmationDelete
                },
                success: (data) => {
                    this.confirmationDelete = ''
                    this.$emit('onSave', data)
                    this.$bvModal.hide('delete-confirmation')
                }
            })
        },
        getFiles() {
            common.request({
                url: '/api/files/get-registration/'+this.user.id,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (data) => {
                    this.registrationFiles = data
                    this.$bvModal.show('registration-files')
                }
            })
        },

        saveInvoiceAdds(){
            if(!!this.invoiceAdd.id){
                this.updateInvoiceAdds()
            } else {
                this.createInvoiceAdds()
            }
        },
        createInvoiceAdds() {
            const data = {...this.invoiceAdd}
            data.id_user = this.user.id

            common.request({
                url: '/api/invoice-add',
                type: 'post',
                auth: true,
                data: data,
                load: true,
                setError: true,
                success: (data) => {
                    this.$emit('reloadUser', this.user)
                    this.$bvModal.hide('invoice-add-modal')
                    this.invoiceAdd = {}
                }
            })
        },
        updateInvoiceAdds() {
            common.request({
                url: '/api/invoice-add/'+this.invoiceAdd.id,
                type: 'put',
                auth: true,
                data: this.invoiceAdd,
                load: true,
                setError: true,
                success: (data) => {
                    this.$emit('reloadUser', this.user)
                    this.$bvModal.hide('invoice-add-modal')
                    this.invoiceAdd = {}
                }
            })
        },

        deleteInvoiceAdds(id) {
            common.request({
                url: '/api/invoice-add/'+id,
                type: 'delete',
                auth: true,
                load: true,
                success: (data) => {
                    this.$emit('reloadUser', this.user)
                }
            })
        },

        editInvoiceAdd(id){
            const added = this.user.invoice_adds.find(i => i.id == id)
            const month = added.month.substring(0, 7)
            this.invoiceAdd = {...added, month: month}
            this.$bvModal.show('invoice-add-modal')
        },

        toMoney(str){
            return common.toMoney(str)
        },
        monthFormat(str){
            return common.monthFormat(str)
        },

        getUnits(){
            common.request({
                url: '/api/units/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (units) => {
                    this.units = units.map(cl => (
                        {value: cl.id, text: `${cl.name}`})
                    )
                },
                error: () => {
                    common.setError({
                        title: 'Ops! Houve algum erro.',
                        message: 'Por favor, recarregue a página e tente novamente'
                    })
                }
            })
        }
    }
}
</script>