<template>
    <admin-base>

        <div class="row">
            <div class="col-12">
                <b-card>

                    <router-link tag="b-button" to='/admin/config/contract'>
                        <b-icon icon="file-earmark-medical"></b-icon>
                        Configurar Contrato
                    </router-link>
                </b-card>
            </div>
        </div>
           
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'

export default {
    components: { AdminBase },


}
</script>
