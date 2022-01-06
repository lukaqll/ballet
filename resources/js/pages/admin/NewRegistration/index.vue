<template>
    <admin-base
        :title="'Usuários'"
    >
        
        <div class="row">
            <div class="col-12">
                <div>
                    <b-card no-body>

                        <b-card-body>
                            <div class="row">
                                <div class="col-12">
                                    <h3>Novas Matrículas</h3>
                                </div>

                                <div class="col-12">
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive" v-if="users.length">
                                    <data-table
                                        :rows="users"
                                        :columns="usersBindings"
                                        locale="br"
                                        title=''
                                        :perPage="[50, 100, 200]"
                                        :clickable="false"
                                    >
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
                                            <td>
                                                <b-button variant="light" size="sm" @click="e => viewRegistration(props.row.id)">
                                                    <b-icon variant="primary" icon="eye"></b-icon>
                                                </b-button>
                                            </td>
                                        </template>
                                    </data-table>
                                </div>
                                <div v-else>Nenhuma nova matrícula</div>

                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>
        
        <new-registration-view-modal
            :visible="user.id && true"
            :user="user"
            @hidden="user = {}"
            @onApprove="onApproveRegistration"
        >
            
        </new-registration-view-modal>
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import DataTable from "vue-materialize-datatable";
import NewRegistrationViewModal from './NewRegistrationViewModal'

export default {
    components: { AdminBase, DataTable, NewRegistrationViewModal },

    computed: {
        usersBindings(){
            return  [
                    {field: 'name', label: 'Nome'},
                    {field: 'email', label: 'E-mail'},
                    {field: 'cpf', label: 'CPF'},
                    {field: 'phone', label: 'Tel'},
                    {field: 'created_at_format', label: 'Registrado Em'},
                ]
        }
    },

       
    mounted: function(){
        this.getUsers()
    },

    data: () => ({
        users: [],
        user: {},
    }),

    methods: {
        getUsers(){
            common.request({
                url: '/api/users/list?status=MP',
                type: 'get',
                auth: true,
                setError: true,
                success: (users) => {
                    this.users = users
                }
            })
        },


        // updating user
        viewRegistration(idUser){
            common.request({
                url: '/api/users/get-registration/'+idUser,
                type: 'get',
                auth: true,
                setError: true,
                success: (user) => {
                    this.user = user
                }
            })
        },
        onRegistrationModalHidden(evt){
            this.user = {}
        },
        onApproveRegistration(){
            this.getUsers()
            this.user = {}
        }
    }
}
</script>
