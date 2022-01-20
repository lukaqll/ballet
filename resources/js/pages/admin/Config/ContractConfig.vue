<template>
    <admin-base>

        <div class="row">
            <div class="col-12">
                <b-card no-body class="border-0 shadow-sm">

                    <b-card-header>
                        <h4>
                            Editar Contrato
                            <div class="float-right">
                                <b-button variant="light" v-b-modal="'parameters-modal'">
                                    <b-icon icon="info"/> Parâmetros</b-button>
                                <b-button  variant="primary" @click="save">Salvar</b-button>    
                            </div>
                        </h4>
                    </b-card-header>

                    <b-card-body>
                        <div class="row">
                            <div class="col-12">
                                <vue-editor v-model="content"></vue-editor>
                            </div>
                        </div>
                    </b-card-body>

                </b-card>
            </div>
        </div>
        
        <b-modal size="lg" title="Parâmetros" id="parameters-modal" v-model="parametersModal" hide-footer>
            <b-alert show dismissible class="">
                <small><b-icon icon="exclamation-triangle"/> É necessário o ':' no início na plavra-chave</small>
            </b-alert>
            <div class="row">
                <div class="col-md-4 my-2">
                    <b-form-input v-model="filter" placeholder="Buscar"></b-form-input>
                </div>
            </div>
            <b-table class="table-sm table-hover" :items="parameterWords" :filter="filter"></b-table>
        </b-modal>
    </admin-base>

</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'
import { VueEditor } from "vue2-editor";

export default {
    components: { AdminBase, VueEditor },
    mounted: function(){

        common.request({
            url: '/api/contract/get-to-config',
            type: 'get',
            setError: true,
            load: true,
            auth: true,
            success: (resp) => {
                this.content = resp
            }
        })
    },
    data: () => ({
        filter: '',
        content: '',
        parametersModal: false,
        parameterWords: [

            { palavra_chave: 'Usuário', info: '', _cellVariants: { palavra_chave: 'secondary', info: 'secondary' } },
            {palavra_chave: ':nome_usuario', info: 'Nome do usuário responsável'},
            {palavra_chave: ':cpf_usuario', info: 'CPF do usuário'},
            {palavra_chave: ':rg_usuario', info: 'RG do usuário'},
            {palavra_chave: ':orgao_exp_usuario', info: 'Órgão expeditor do RG'},
            {palavra_chave: ':uf_orgao_ex_usuario', info: 'UF do órgão expeditor'},
            {palavra_chave: ':email_usuario', info: 'E-Mail do usuário'},
            {palavra_chave: ':telefone_usuario', info: 'Telefone do usuário'},
            {palavra_chave: ':nascimento_usuario', info: 'Data de nascimento do usuário'},
            {palavra_chave: ':desde_usuario', info: 'Data de criação do usuário'},
            {palavra_chave: ':profissao_usuario', info: 'Profissão do usuário'},
            {palavra_chave: ':instagram_usuario', info: 'Instagram do usuário'},
            

            { palavra_chave: 'Endereço', info: '', _cellVariants: { palavra_chave: 'secondary', info: 'secondary' } },
            {palavra_chave: ':cep_usuario', info: 'CEP do usuário'},
            {palavra_chave: ':uf_usuario', info: 'UF do usuário'},
            {palavra_chave: ':cidade_usuario', info: 'Cidade do usuário'},
            {palavra_chave: ':bairro_usuario', info: 'Bairro do usuário'},
            {palavra_chave: ':rua_usuario', info: 'Rua do usuário'},
            {palavra_chave: ':numero_endereco_usuario', info: 'Numero do endereço do usuário'},
            {palavra_chave: ':complemento_usuario', info: 'Complemento do endereço do usuário'},
            

            { palavra_chave: 'Aluno', info: '', _cellVariants: { palavra_chave: 'secondary', info: 'secondary' } },
            {palavra_chave: ':nome_aluno', info: 'Nome do aluno'},
            {palavra_chave: ':nascimento_aluno', info: 'Data de nascimento do aluno'},
            {palavra_chave: ':desde_aluno', info: 'Data de criação do aluno'},

            { palavra_chave: 'Geral', info: '', _cellVariants: { palavra_chave: 'secondary', info: 'secondary' } },
            {palavra_chave: ':data', info: 'Data atual'},
            {palavra_chave: ':data_hora', info: 'Data e hora atual'},

            { palavra_chave: 'Aula', info: '', _cellVariants: { palavra_chave: 'secondary', info: 'secondary' } },
            {palavra_chave: ':nome_aula', info: 'Nome da aula'},
            {palavra_chave: ':nome_unidade', info: 'Nome da unidade na qual está vinculada a aula'},
            {palavra_chave: ':valor_aula_real', info: 'Valor em real da aula (Ex.: 99,50)'},
            {palavra_chave: ':valor_aula_extenso', info: 'Valor em real da aula por extenso (Ex.: noventa e nove reais e cinquenta centavos)'},


        ]
    }),
    methods: {
        save(){
            common.request({
                url: '/api/contract/update',
                type: 'post',
                setError: true,
                load: true,
                auth: true,
                savedAlert: true,
                data: {content: this.content},
                success: (resp) => {
                    this.content = resp.value
                }
            })
        }
    }

}
</script>
