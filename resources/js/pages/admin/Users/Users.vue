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

        <t-modal
            header="Title of the modal"
        >
            Content of the modal.
            <template v-slot:footer>
                <div class="flex justify-between">
                    <t-button type="button">
                        Cancel
                    </t-button>
                    <t-button type="button">
                        Ok
                    </t-button>
                </div>
            </template>
        </t-modal>

    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'

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
