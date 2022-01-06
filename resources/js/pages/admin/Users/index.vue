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
                                    <h3>
                                        Usuários    
                                        <b-button class="float-right" variant="primary" @click="() => newUserModalShow = true">Novo Usário</b-button>
                                    </h3>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <b-form-select :options="status" class="w-100" v-model="filter.status"></b-form-select>
                                        </div>
                                        <div class="col-md-4">
                                            <b-button @click="getUsers">Buscar</b-button>
                                            <b-button variant="danger" @click="filter = {}">Limpar</b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive">
                                    <data-table
                                        :rows="users"
                                        :columns="usersBindings"
                                        locale="br"
                                        title=''
                                        :perPage="[50, 100, 200]"
                                        :clickable="false"
                                    >
                                        <th slot="thead-tr"></th>
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
                                            <td>
                                                <span v-if="!props.row.signer_key" v-b-tooltip.hover title="Usuário não cadastrado como signatário">
                                                    <b-icon icon='exclamation-triangle' variant="danger"/>
                                                </span>
                                            </td>
                                            <td>
                                                <b-button variant="light" size="sm" @click="e => editUser(props.row.id)">
                                                    <b-icon  icon="pencil-square"></b-icon>
                                                </b-button>
                                                <b-button variant="light" size="sm" @click="() => toPasswordUpdateId = props.row.id">
                                                    <b-icon  icon="key"></b-icon>
                                                </b-button>
                                            </td>
                                        </template>
                                    </data-table>
                                </div>

                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>

        <new-user-modal 
            :isVisible="newUserModalShow" 
            @onHidden="() => this.newUserModalShow = false"
            @onSave="onNewUserSave"
        />

        <edit-user-modal
            :isVisible="editableUser.id ? true : false"
            :user="editableUser"
            @onHidden="onEditUserModalHidden"
            @onSave="onEditUserSave"
            @editStudent="editStudent"
            @addStudent="addStudent"
            @reloadUser="(data) => editUser(data.id)"
        />

        <student-modal
            :isVisible="studentModalShow"
            :idStudent="editableStudentId"
            :user="editableUser"
            @onHidden="onStudentModalHidden"
            @onSave="onStudentSave"
        />

        <password-update-modal
            :visible="toPasswordUpdateId ? true : false"
            :idUser="toPasswordUpdateId"
            @onHidden="() => toPasswordUpdateId = null"
            @onSave="onPasswordUpdate"
        />

        
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import NewUserModal from './NewUserModal';
import EditUserModal from './EditUserModal';
import StudentModal from '../Students/StudentModal';
import PasswordUpdateModal from './PasswordUpdateModal';
import DataTable from "vue-materialize-datatable";

export default {
    components: { AdminBase, NewUserModal, EditUserModal, StudentModal, PasswordUpdateModal,  DataTable },

    computed: {
        usersBindings(){
            return  [
                    {field: 'name', label: 'Nome'},
                    {field: 'status_text', label: 'Status'},
                    {field: 'email', label: 'E-mail'},
                    {field: 'phone', label: 'Tel'},
                ]
        },
        status(){
            return [
                {text: 'Ativo', value: 'A'},
                {text: 'Inativo', value: 'I'},
                {text: 'Matrícula Pendente', value: 'MP'},
            ]
        }
    },

       
    mounted: function(){
        this.getUsers()
    },

    data: () => ({
        users: [],
        editableUser: {},
        newUserModalShow: false,
        toPasswordUpdateId: null,
        editableStudentId: null,
        studentModalShow: false,
        filter: {},
    }),

    methods: {
        getUsers(){
            let filters = {}
            for(const key in this.filter){
                if( this.filter[key] )
                    filters[key] = this.filter[key]
            }
            let queryString = new URLSearchParams(filters)
            common.request({
                url: '/api/users/list?'+queryString,
                type: 'get',
                auth: true,
                setError: true,
                success: (users) => {
                    this.users = users
                }
            })
        },

        // creating user
        onNewUserSave(){
            this.newUserModalShow = false
            this.getUsers()
        },

        // updating user
        editUser(idUser){
            common.request({
                url: '/api/users/'+idUser,
                type: 'get',
                auth: true,
                setError: true,
                success: (user) => {
                    this.editableUser = user
                }
            })
        },
        onEditUserModalHidden(evt){
            this.editableUser = {}
        },
        onEditUserSave() {
            this.editableUser = {}
            this.getUsers()
        },
        onPasswordUpdate(user){
            common.success({ title: 'Senha alterada'})
            this.toPasswordUpdateId = null
        },
        handleUserAction(){
            alert(1)
        },

        // student
        onStudentModalHidden() {
            this.editableStudentId = null
            this.studentModalShow = false
        },
        onStudentSave(student) {
            this.editableStudentId = null
            this.studentModalShow = false
            this.editUser(this.editableUser.id)
            this.getUsers()
        },
        editStudent(idStudent){
            this.editableStudentId = idStudent
            this.studentModalShow = true
        },
        addStudent(idUser){
            this.editableStudentId = null
            this.studentModalShow = true
        },
    }
}
</script>
