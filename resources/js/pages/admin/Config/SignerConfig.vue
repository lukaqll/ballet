<template>
    <admin-base>

        <div class="row">
            <div class="col-12">
                <b-card no-body class="border-0 shadow-sm">

                    <b-card-header>
                        <h4>
                            Editar Signatário
                        </h4>
                    </b-card-header>

                    <b-card-body>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nome</label>
                                    <b-input v-model="signerData.name"></b-input>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">CPF/CNPJ</label>
                                    <b-input v-model="signerData.cpf" v-mask="'###.###.###-##'"></b-input>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">E-mail</label>
                                    <b-input type="email" v-model="signerData.email"></b-input>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Telefone</label>
                                    <b-input v-model="signerData.phone" v-mask="'(##) #####-####'"></b-input>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Chave ClickSign</label>
                                    <b-input v-model="signerData.signer_key" disabled></b-input>
                                </div>
                            </div>

                            <div class="col-12">
                                <b-button variant="primary" @click="save">Salvar</b-button>
                            </div>
                        </div>
                    </b-card-body>

                </b-card>
            </div>
        </div>
        
    </admin-base>

</template>

<script>
import common from '../../../common/common'
import { VueEditor } from "vue2-editor";
import AdminBase from '../../../components/AdminBase'

export default {
    components: { AdminBase },
    mounted: function(){
        this.getSiger()
     
    },
    data: () => ({
        
        signerData: {}
    }),
    methods: {
        save(){
            
            common.confirmAlert({
                title: 'Deseja alterar os dados?',
                message: 'Isso deletará o signatário atual na ClickSign e criará um novo. Logo, poderá haver divergência nos contratos abertos',
                onConfirm: () => {

                    common.request({
                        url: '/api/signer-config/save',
                        type: 'post',
                        setError: true,
                        load: true,
                        auth: true,
                        savedAlert: true,
                        data: {...this.signerData},
                        success: (resp) => {
                            this.getSiger()
                        }
                    })
                }
            })
        },
        getSiger(){
            common.request({
                url: '/api/signer-config/get',
                type: 'get',
                setError: true,
                load: true,
                auth: true,
                data: {content: this.content},
                success: (resp) => {
                    this.signerData = resp
                }
            })
        }
    }

}
</script>
