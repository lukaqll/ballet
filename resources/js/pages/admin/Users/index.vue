<template>
    <admin-base
        :title="'Usuários'"
    >
        
        <div class="row">
            <div class="col-12">
                <div>
                    <b-card no-body>
                        <b-card-header>
                            Usuários    
                            <b-button class="float-right" @click="() => newUserModalShow = true">Novo Usário</b-button>
                        </b-card-header>

                        <b-card-body>
                            <b-table></b-table>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>

        <div>
            
            <new-user-modal :isVisible="newUserModalShow" @onHidden="() => this.newUserModalShow = false"/>
        </div>

        
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import NewUserModal from './NewUserModal';

export default {
    components: { AdminBase, NewUserModal },
    
    data: () => ({
        user: {},
        student: {},
        newUserModalShow: false
    }),
    

    methods: {
        getUsers: () => {

        },
        _alert(a){  alert(a)}
    }
}
</script>
