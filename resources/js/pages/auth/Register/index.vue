<template>
    <div class="d-flex flex-column h-100 bg-light mt-3 main-container" style="min-height: 100vh !important">
        <component :is="containerTag" class="w-100 d-flex py-5">
            <template v-if="!saved">
                <div class="row">
                    <div class="col-12">
                        <h2 class="p-2">
                            <b-icon icon="person-plus"></b-icon> 
                            Cadastro
                        </h2>
                    </div>

                    <!-- steps -->
                    <div class="col-12">
                        <b-card class="border-0 shadow-sm">
                            <div class="stepper-wrapper">
                                <div 
                                    v-for="step in steps" :key="step.index"
                                    :class="`hover stepper-item ${getStepHandle(step.index)}`" 
                                    @click="currentStep = step.index"
                                >
                                    <div class="step-counter">{{ step.index + 1 }}</div>
                                    <div class="step-name">
                                        <small>{{ step.name }}</small>
                                    </div>
                                </div>
                            </div>
                        </b-card>
                    </div>

                    <!-- files -->
                    <div :class="`col-12 my-2 form-step ${getStepHandle(0)}`">
                        <b-card class="border-0 shadow-sm">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="h4 mt-4">
                                        <b-icon icon="files"></b-icon>
                                        Arquivos
                                    </h4>
                                    <hr>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded my-1 p-2">
                                        <label>Frente do documento de identidade do responsável</label>
                                        <b-form-file
                                            v-model="files.doc1"
                                            :state="Boolean(files.doc1)"
                                            placeholder="Escolha ou arraste um arquivo..."
                                            drop-placeholder="Solte aqui..."
                                        />
                                        <div class="mt-3">Arquivo Selecionado: {{ files.doc1 ? files.doc1.name : '' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded my-1 p-2">
                                        <label>Verso do documento de identidade do responsável</label>
                                        <b-form-file
                                            v-model="files.doc2"
                                            :state="Boolean(files.doc2)"
                                            placeholder="Escolha ou arraste um arquivo..."
                                            drop-placeholder="Solte aqui..."
                                        />
                                        <div class="mt-3">Arquivo Selecionado: {{ files.doc2 ? files.doc2.name : '' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded my-1 p-2">
                                        <label>Comprovante de pagamento da matrícula</label>
                                        <b-form-file
                                            v-model="files.payment"
                                            :state="Boolean(files.payment)"
                                            placeholder="Escolha ou arraste um arquivo..."
                                            drop-placeholder="Solte aqui..."
                                        />
                                        <div class="mt-3">Arquivo Selecionado: {{ files.payment ? files.payment.name : '' }}</div>
                                    </div>
                                </div>

                            </div>
                        </b-card> 
                    </div>

                    <!-- user data -->
                    <div :class="`col-12 my-2 form-step ${getStepHandle(1)}`">
                        <b-card class="border-0 shadow-sm">
                            <div class="row">

                                <div class="col-12">
                                    <h4 class="h4">
                                        <b-icon icon="person-lines-fill"></b-icon>
                                        Dados do Usuário <br>
                                        <small class="text-danger">( Responsável pelo aluno )</small>
                                        
                                    </h4>
                                    <hr/>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">


                                        <div class="col-md-4">
                                            <b-form-group>
                                                <label>Nome Completo do responsável</label>
                                                <b-form-input placeholder="Nome completo no responsável" v-model="user.name"/>
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
                                                <label>Seu telefone é WhatsApp?</label>
                                                <select class="form-control" v-model="user.is_whatsapp" aria-placeholder='selecione'>
                                                    <option value="1">Sim</option>
                                                    <option value="0">Não</option>
                                                </select>
                                            </b-form-group>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <b-form-group>
                                                <label>Nº do documento de identidade</label>
                                                <b-form-input class="form-control" placeholder="Identidade do responsável" v-model="user.rg"/>
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
                                        
                                            
                                        <div class="col-md-6">
                                            <b-form-group>
                                                <label>Profissão</label>
                                                <b-form-input class="form-control" placeholder="Sua Profissão" v-model="user.profession"/>
                                            </b-form-group>
                                        </div>
                                        <div class="col-md-6">
                                            <b-form-group>
                                                <label>Instagram</label>
                                                <b-form-input class="form-control" placeholder="Instagram para marcação do aluno em posts" v-model="user.instagram"/>
                                            </b-form-group>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </b-card>
                    </div>

                    <!-- address -->
                    <div :class="`col-12 my-2 form-step ${getStepHandle(2)}`">
                        <b-card class="border-0 shadow-sm">
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
                                        <b-form-input type="text" placeholder="Seu CEP"  v-model="user.cep" v-mask="'#####-###'"/>
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
                                        <b-form-input type="text" placeholder="Sua cidade"  v-model="user.city"/>
                                    </b-form-group>
                                </div>

                                <div class="col-md-3">
                                    <b-form-group>
                                        <label>Bairro</label>
                                        <b-form-input type="text" placeholder="Seu Bairro"  v-model="user.district"/>
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
                        </b-card>
                    </div>

                    <!-- student data -->
                    <div :class="`col-12 my-2 form-step ${getStepHandle(3)}`">
                        <b-card class="border-0 shadow-sm">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="h4 mt-4">
                                        <b-icon icon="person-badge"></b-icon>
                                        Dados do Aluno
                                    </h4>
                                    <hr>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-8">
                                            <b-form-group>
                                                <label>Qual o nome do aluno?</label>
                                                <b-form-input placeholder="Nome do Aluno" v-model="student.name"/>
                                            </b-form-group>
                                        </div>
                                        <!-- <div class="col-md-4">
                                            <b-form-group>
                                                <label>E o apelido?</label>
                                                <b-form-input placeholder="Apelido do Aluno" v-model="student.nick_name"/>
                                            </b-form-group>
                                        </div> -->
                                        <div class="col-md-4">
                                            <b-form-group>
                                                <label>Data de Nascimento do aluno</label>
                                                <b-form-input type="date" v-model="student.birthdate"/>
                                            </b-form-group>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <b-form-group>
                                                <label>Possui doença ou lesão preexistente?</label>
                                                <b-form-select :options="sampleOptions" class="w-100" v-model="student.has_health_problem"></b-form-select>
                                            </b-form-group>
                                        </div>

                                        <div class="col-md-8">
                                            <b-form-group>
                                                <label>Qual?</label>
                                                <b-form-input placeholder="Doença ou lesão preexistente" :disabled="student.has_health_problem != 1" class="w-100" v-model="student.health_problem"></b-form-input>
                                            </b-form-group>
                                        </div>

                                        <div class="col-md-4">
                                            <b-form-group>
                                                <label>Alguma restrição alimentar?</label>
                                                <b-form-select :options="sampleOptions" class="w-100" v-model="student.has_food_restriction"></b-form-select>
                                            </b-form-group>
                                        </div>

                                        <div class="col-md-8">
                                            <b-form-group>
                                                <label>Qual?</label>
                                                <b-form-input placeholder="Retrição alimentar" :disabled="student.has_food_restriction != 1" class="w-100" v-model="student.food_restriction"></b-form-input>
                                            </b-form-group>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <b-form-group>
                                                <label>Estuda em escola de ensino regular?</label>
                                                <b-form-select :options="sampleOptions" class="w-100" v-model="student.in_school"></b-form-select>
                                            </b-form-group>
                                        </div>

                                        <div class="col-md-8">
                                            <b-form-group>
                                                <label>Em qual horário?</label>
                                                <b-form-input placeholder="Ex.: de segunda à sexta, matutino" :disabled="student.in_school != 1" class="w-100" v-model="student.school_time"></b-form-input>
                                            </b-form-group>
                                        </div>

                                        <div class="col-md-6">
                                            <b-form-group>
                                                <label>Cidade da aula desejada</label>
                                                <b-form-select :options="units" class="w-100" v-model="idUnit"></b-form-select>
                                            </b-form-group>
                                        </div>

                                        <div class="col-md-6">
                                            <b-form-group>
                                                <label>Em qual aula deseja ingressar?</label>
                                                <b-form-select :options="classes" class="w-100" v-model="student.id_class"></b-form-select>
                                            </b-form-group>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </b-card>
                    </div>

                    <!-- know by -->
                    <div :class="`col-12 my-2 form-step ${getStepHandle(4)}`">
                        <b-card class="border-0 shadow-sm">
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <b-form-group label="Como conheceu nossa empresa?" v-slot="{ knowBy }">
                                            <b-form-radio v-for="opt in knowOptions " :key="opt.value" v-model="user.know_by_opt" :aria-describedby="knowBy" name="know-by" :value="opt.value">{{ opt.text }}</b-form-radio>
                                        </b-form-group>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <b-form-group v-if="user.know_by_opt == 0">
                                        <label>Conte-nos como nos conheceu</label>
                                        <textarea class="form-control"  v-model="user.know_by"></textarea>
                                    </b-form-group>
                                </div>
                            </div>
                        </b-card>
                    </div>

                    <!-- save -->
                    <div class="col-12 my-2">
                        <b-card class="border-0 shadow-sm">
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <b-button variant="light" @click="currentStep--" v-if="currentStep > 0">
                                        <b-icon icon="chevron-compact-left"/>
                                        Voltar
                                    </b-button>
                                    <span v-else></span>

                                    <b-button variant="primary" @click="currentStep++" v-if="currentStep < (steps.length-1)">
                                        Próximo
                                        <b-icon icon="chevron-compact-right"/>
                                    </b-button>
                                    <b-button variant="primary" @click="saveHandle" v-else>
                                        <b-icon icon="check"/>
                                        Enviar
                                    </b-button>
                                </div>
                            </div>
                        </b-card>
                    </div>
                </div>
            </template>

            <!-- confirmation -->
            <template v-else>
                <div class="text-center">
                    <h3 class="text-success">
                        <b-icon icon='check'></b-icon>
                        Cadastro efetuado com sucesso
                    </h3>
                    <h5 class="text-center">Vamos analizar seus dados e em breve entraremos em contato!</h5>
                    <h6 class="text-center">Um E-mail foi enviado para <b>{{ user.email }}</b> </h6>
                    <small>Verifique no seu E-mail e acesse o link para cadastrar uma senha de acesso.</small>
                    <router-link tag="b-button" to='/login' variant="primary" class="btn-block mt-2">
                        Ir para o Login
                    </router-link>
                </div>
            </template>

        </component>
    </div>

</template>

<script>
import common from '../../../common/common'
import orgaosExpeditores from "../../../params/orgaosExpeditores"
import ufs from "../../../params/ufs"
export default {
    data: () => ({
        idUnit: null,
        user: {},
        student: {},
        classes: [],
        units: [],
        files: {},
        sendPasswordMail: false,
        ufs: [
            {value: 'ES', text: 'Espírito Santo'},
            {value: 'MG', text: 'Minas Gerais'},
        ],
        saved: false,
        sampleOptions: [
            {value: 1, text: 'Sim'},
            {value: 0, text: 'Não'}
        ],
        knowOptions: [
            {value: 'Instagram', text: 'Instagram'},
            {value: 'Facebook', text: 'Facebook'},
            {value: 'Google', text: 'Google'},
            {value: 0, text: 'Outros'},
        ],

        steps: [
            {index: 0, name: 'Arquivos'},
            {index: 1, name: 'Usuário'},
            {index: 2, name: 'Endereço'},
            {index: 3, name: 'Aluno'},
            {index: 4, name: 'Questionário'}
        ],
        currentStep: 0
    }),
    props: {
        isVisible: Boolean,
    },
    mounted: function() {
        this.getUnits()
    },

    computed: {
        orgaosExpeditores: function(){
            return orgaosExpeditores
        },
        containerTag: function(){
            if( window.innerWidth < 500 ){
                return 'div'
            } else {
                return 'b-container'
            }
        },
        ufsParam: function(){
            return ufs
        }
    },
    watch: {
        sendPasswordMail: function(willSend) {
            if(willSend){
                this.user.password_confirmation = null;
                this.user.password = null;
            }
        },
        'student.has_health_problem': function(val){
            if( val != 1 )
                this.student.health_problem = null
        },
        'student.has_food_restriction': function(val){
            if( val != 1 )
                this.student.food_restriction = null
        },
        'student.in_school': function(val){
            if( val != 1 )
                this.student.school_time = null
        },
        idUnit: function(id){
            this.student = {...this.student, id_class: null}
            if(id){
                this.getClasses(id)
            } else {
                this.classes = []
            }
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

            for( let attr in this.user ){

                let val = this.user[attr]

                if( attr == 'know_by_opt' ){
                    attr = 'know_by'
                    val = this.user.know_by_opt
                    if( this.user.know_by_opt == 0 ){

                        if( !this.user.know_by ){
                            common.setError({title: 'Informe onde nos conheceu'})
                            return
                        }

                        val = this.user.know_by
                    }
                } 

                formData.append(`user_${attr}`, val)
            }

            for( const attr in this.student )
                formData.append(`student_${attr}`, this.student[attr])

            for( const attr in this.files )
                formData.append(`file_${attr}`, this.files[attr])

            formData.append('send_password_mail', this.sendPasswordMail)
            
            common.request({
                url: '/api/register',
                type: 'post',
                data: formData,
                auth: true,
                setError: true,
                file: true,
                load: true,
                success: (data) => {
                    this.saved = true
                }
            })
        },

        saveHandle(){

            common.confirmAlert({
                title: 'Confirma que os dados estão corretos?',
                message: `Será enviado um E-mail à <b>${this.user.email || '(informe seu E-mail)'}</b> para a confirmação e cadastro da senha de acesso.`,
                onConfirm: this.save
            })
        },
        getClasses(idUnit){
            common.request({
                url: '/api/classes/public-list?id_unit='+idUnit,
                type: 'get',
                auth: true,
                setError: true,
                success: (classes) => {
                    this.classes = classes.map(cl => (
                        {value: cl.id, text: `${cl.name} (${cl.team})`})
                    )
                },
            })
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
        },

        getStepHandle(step){
            if( this.currentStep == step ){
                return 'active'
            } else if ( this.currentStep >= step ){
                return 'completed'
            } else {
                return ''
            }
        }
    }
}
</script>

<style scoped>
@import url('../../../../css/stepBar.css');

.form-step{
    display: none;
}
.form-step.active{
    display: initial;
    color: black;
}
</style>