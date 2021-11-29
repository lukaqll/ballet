<template>
    <admin-base
        :title="'Usuários'"
    >
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Usuários</div>
                    <div class="card-body">
                        
                        <table class="table">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </admin-base>
</template>

<script>
import common from '../../common/common'
import AdminBase from '../../components/AdminBase.vue'

export default {
      components: { AdminBase },

    data: () => ({
        user: {}
    }),

    methods: {
        getUsers: () => {

        }
    }
}
</script>
