<template>
    <admin-base>

        <div class="row">

            <div class="col-md-4 d-flex">
                <router-link to='/admin/reports/know-by' tag="span" class="hover w-100 h-100 d-flex">
                    <b-card no-body bg-variant="primary" class="w-100 my-1">
                        <div class="h-100 w-100">
                            <div class="row text-white p-2">
                                <div class="col-4">
                                    <b-icon icon="pie-chart-fill" class="h1"></b-icon>
                                </div>
                                <div class="col-8">
                                    <h3 class="text-right">Por onde nos conheceram?</h3>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </router-link>
            </div>

            <div class="col-md-4 d-flex">
                <router-link to='/admin/reports/revenue' tag="span" class="hover w-100 h-100 d-flex">
                    <b-card no-body bg-variant="success" class="w-100 my-1">
                        <div class="h-100 w-100">
                            <div class="row text-white p-2">
                                <div class="col-4">
                                    <i class="fa fa-money-bill h1"></i>
                                </div>
                                <div class="col-8">
                                    <h3 class="text-right">Faturamento</h3>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </router-link>
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
