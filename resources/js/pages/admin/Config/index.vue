<template>
    <div class="row">
        <div class="col-12">
            <b-card class="border-0 shadow-sm">

                <router-link tag="b-button" to='/admin/config/contract'>
                    <b-icon icon="file-earmark-medical"></b-icon>
                    Configurar Contrato
                </router-link>

                <router-link tag="b-button" to='/admin/config/signer'>
                    <b-icon icon="person"></b-icon>
                    Configurar Signatário
                </router-link>
            </b-card>
        </div>

        <div class="col-12 mt-5">
            <b-card class="border-0 shadow-sm">
                <b-form-checkbox v-model="config.invoice_allow" switch>
                    Liberar faturas
                </b-form-checkbox>
                <b-form-checkbox v-model="config.send_invoice_mail" switch>
                    Enviar faturas por e-mail
                </b-form-checkbox>

                <b-button class="mt-4" @click="save" variant="primary">Salvar</b-button>
            </b-card>
        </div>
    </div>
</template>

<script>
import common from '../../../common/common'
export default {

    data: () => ({
        config: {}
    }),

    mounted() {
        this.getConfig()
    },

    methods: {

        getConfig() {
            common.request({
                url: '/api/config/get',
                type: 'get',
                setError: true,
                load: true,
                auth: true,
                success: (resp) => {
                    this.config = {
                        invoice_allow: resp.find(c => c.attribute == 'invoice_allow').value == 1,
                        send_invoice_mail: resp.find(c => c.attribute == 'send_invoice_mail').value == 1
                    }
                }
            })
        },

        save() {
            common.request({
                url: '/api/config/save',
                type: 'post',
                setError: true,
                savedAlert: true,
                load: true,
                auth: true,
                data: this.config,
                success: (resp) => {
                    this.getConfig()
                }
            })
        }
    }

}
</script>
