<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div>
                    <b-card no-body class="border-0 shadow-sm">

                        <b-card-body>
                            <div class="row">
                                <div class="col-12">
                                    <h3>Novas Matrículas</h3>
                                </div>

                                <div class="col-md-4">
                                    <div class="my-2">
                                        <b-form-input size="sm" v-model="tableFilter" placeholder="Buscar"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive" v-if="users.length">

                                    <b-table
                                        :fields="tableFields"
                                        :items="users"
                                        :filter="tableFilter"
                                        hover
                                    >
                                        <template #cell(students)="row">
                                                <span v-if="row.item.students.length == 1">{{ row.item.students[0].name }}</span>
                                                <span v-else-if="row.item.students.length > 1">{{ row.item.students[0].name }} <b>+{{row.item.students.length - 1}}</b></span>
                                        </template>
                                        <template #cell(actions)="row">
                                            <b-button variant="light" size="sm" @click="e => viewRegistration(row.item.id)">
                                                    <b-icon variant="primary" icon="eye"></b-icon>
                                                </b-button>
                                        </template>
                                    </b-table>
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
    </div>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import DataTable from "vue-materialize-datatable";
import NewRegistrationViewModal from './NewRegistrationViewModal'

export default {
    components: { AdminBase, DataTable, NewRegistrationViewModal },

    computed: {
        tableFields(){
            return [
                { key: 'name', label: 'Nome', sortable: true },
                { key: 'email', label: 'E-mail', sortable: true },
                { key: 'cpf', label: 'CPF', sortable: true },
                { key: 'phone', label: 'Tel', sortable: true },
                { key: 'created_at_format', label: 'Registrado Em', sortable: true },
                { key: 'actions', label: '' },
            ]
        },
    },

       
    mounted: function(){
        this.getUsers()
    },

    data: () => ({
        users: [],
        user: {},
        tableFilter: ''
    }),

    methods: {
        getUsers(){
            common.request({
                url: '/api/users/list?status=MP',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
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
                load: true,
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
