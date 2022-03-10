<template>
    <div>
        <b-modal  
            hide-footer 
            :visible="visible" 
            size="xl"
            @hidden="$emit('hidden')"    
        >

            <div class="row">
                <div class="col-12">
                    <h4 class="h4">
                        <b-icon icon="person-lines-fill"></b-icon>
                        Dados do Usuário
                    </h4>
                    <hr/>
                </div>

                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-4">
                            <b-form-group>
                                <label>Nome Completo</label>
                                <b-form-input disabled placeholder="Nome" v-model="user.name"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>E-mail</label>
                                <b-form-input disabled type="email" placeholder="E-mail"  v-model="user.email"/>
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
                                <b-form-input disabled type="date" v-model="user.birthdate"/>
                            </b-form-group>
                        </div> 
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Telefone</label>
                                <b-form-input disabled placeholder="Telefone" v-mask="'(##) #####-####'" v-model="user.phone"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>WhatsApp</label>
                                <select disabled class="form-control" v-model="user.is_whatsapp">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </b-form-group>
                        </div>
                       
                        <div class="col-md-4">
                            <b-form-group>
                                <label>RG</label>
                                <b-form-input disabled class="form-control" placeholder="RG" v-model="user.rg"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Órgão Expeditor</label>
                                <b-form-select disabled :options="orgaosExpeditores" class="w-100" v-model="user.orgao_exp"></b-form-select>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>UF do Órgão Expeditor</label>
                                <b-form-select disabled :options="ufsParam" class="w-100" v-model="user.uf_orgao_exp"></b-form-select>
                            </b-form-group>
                        </div>
                         
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Profissão</label>
                                <b-form-input disabled class="form-control" placeholder="Sua Profissão" v-model="user.profession"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Instagram</label>
                                <b-form-input disabled class="form-control" placeholder="Instagram para marcação do aluno em posts" v-model="user.instagram"/>
                            </b-form-group>
                        </div>

                        <div class="col-md-4">
                            <b-form-group>
                                <label>Conheceu através de:</label>
                                <b-form-input disabled class="form-control" v-model="user.know_by"/>
                            </b-form-group>
                        </div>

                    </div>
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
                        <b-form-input disabled type="text" placeholder="Seu CEP"  v-model="user.cep" v-mask="'#####-###'"/>
                    </b-form-group>
                </div>
                <div class="col-md-4">
                    <b-form-group>
                        <label>UF</label>
                        <b-form-select disabled :options="ufs" class="w-100" v-model="user.uf"></b-form-select>
                    </b-form-group>
                </div>
                <div class="col-md-4">
                    <b-form-group>
                        <label>Cidade</label>
                        <b-form-input disabled type="text" placeholder="Sua cidade"  v-model="user.city"/>
                    </b-form-group>
                </div>

                <div class="col-md-3">
                    <b-form-group>
                        <label>Bairro</label>
                        <b-form-input disabled type="text" placeholder="Seu Bairro"  v-model="user.district"/>
                    </b-form-group>
                </div>
                <div class="col-md-4">
                    <b-form-group>
                        <label>Logradouro</label>
                        <b-form-input disabled type="text" placeholder="Rua / Av."  v-model="user.street"/>
                    </b-form-group>
                </div>

                <div class="col-md-2">
                    <b-form-group>
                        <label>Nº</label>
                        <b-form-input disabled type="text" placeholder="Nº"  v-model="user.address_number"/>
                    </b-form-group>
                </div>

                <div class="col-md-3">
                    <b-form-group>
                        <label>Complemento</label>
                        <b-form-input disabled type="text" placeholder="Ap 101 / Predio X"  v-model="user.address_complement"/>
                    </b-form-group>
                </div>

                <div class="col-12">
                    <h4 class="h4 mt-4">
                        <b-icon icon="person-badge"></b-icon>
                        Dados do Aluno
                    </h4>
                    <hr>
                </div>

                <div class="col-md-12" v-if="user.student && user.student.id">
                    <div class="row">

                        <div class="col-md-6" >
                            <b-form-group>
                                <label>Nome do aluno</label>
                                <b-form-input disabled placeholder="Nome do Aluno" v-model="user.student.name"/>
                            </b-form-group>
                        </div>
                        <div class="col-md-6">
                            <b-form-group>
                                <label>Data de Nascimento</label>
                                <b-form-input disabled type="date" v-model="user.student.birthdate"/>
                            </b-form-group>
                        </div>

                        <div class="col-md-12">
                            <b-form-group>
                                <label>Aula Selecionada</label>
                                <b-form-input disabled :value="user.student.pendent_classes.map(c=>c.name).join(', ')"/>
                            </b-form-group>
                        </div>
                        
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Problema de saúde</label>
                                <b-form-input v-if="user.student.health_problem" disabled placeholder="Problema de saúde" class="w-100" v-model="user.student.health_problem"/>
                                <span v-else> <br> Nenhum problema de saúde</span>
                            </b-form-group>
                        </div>

                        <div class="col-md-4">
                            <b-form-group>
                                <label>Restrição alimentar</label>
                                <b-form-input v-if="user.student.food_restriction" disabled placeholder="Retrição alimentar" class="w-100" v-model="user.student.food_restriction"/>
                                <span v-else> <br> Nenhuma restrição alimentar</span>
                            </b-form-group>
                        </div>

                        
                        <div class="col-md-4">
                            <b-form-group>
                                <label>Ensino regular</label>
                                <b-form-input v-if="user.student.school_time" disabled placeholder="Ex.: de segunda à sexta, matutino"  class="w-100" v-model="user.student.school_time"></b-form-input>
                                <span v-else> <br> Não está no ensino regular</span>
                            </b-form-group>
                        </div>

                    </div>
                </div>
                <div v-else>
                    <h5 class="text-center">Nenhum aluno</h5>
                </div>

                <div class="col-12">
                    <h4 class="h4 mt-4">
                        <b-icon icon="files"></b-icon>
                        Arquivos
                    </h4>
                    <hr>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4" v-for="file in user.register_files" :key="file.id">
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
                </div>

            </div>       
            <div class="row my-2">
                <div class="col-md-12">
                    <b-button variant="success" class="float-right" @click="approve">
                        <b-icon icon="check"/>
                        Aprovar
                    </b-button>
                </div>
            </div>     
        </b-modal>
    </div>
</template>
<script>
import common from '../../../../common/common'

import orgaosExpeditores from "../../../../params/orgaosExpeditores"
import ufs from "../../../../params/ufs"
export default {
    props: {
        visible: Boolean,
        user: Object
    },
    computed: {
        orgaosExpeditores: function(){
            return orgaosExpeditores
        },
        ufs: function () {
            
            return [
                {value: 'ES', text: 'Espírito Santo'},
                {value: 'MG', text: 'Minas Gerais'},
            ]
        },
        ufsParam: function(){
            return ufs
        }
    },
    methods: {
        approve() {

            common.confirmAlert({
                title: 'Aprovar esta matrícula?',
                message: `Certifique-se que os dados estejam corretos. 
                          <br><small>Será enviado um E-mail para <b>${this.user.email}</b> para a assinatura do contrato.</small>`,
                onConfirm: () => {
                    common.request({
                        url: '/api/users/approve-registration/'+this.user.id,
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (data) => {
                            this.$emit('onApprove', data)
                        }
                    })
                }
            })
        }
    }
}
</script>