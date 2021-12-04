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
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive">
                                    <data-table
                                        :rows="users"
                                        :columns="usersBindings"
                                        locale="br"
                                        title=''
                                    >
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
                                            <td>
                                                <b-button variant="outline" size="sm" @click="e => editUser(props.row.id)">
                                                    <b-icon variant="primary" icon="pencil-square"></b-icon>
                                                </b-button>
                                                <b-button variant="outline" size="sm" @click="() => toPasswordUpdateId = props.row.id">
                                                    <b-icon variant="primary" icon="key"></b-icon>
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
                    {field: 'email', label: 'E-mail'},
                    {field: 'cpf', label: 'CPF'},
                    {field: 'phone', label: 'Tel'},
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
    }),

    methods: {
        getUsers(){
            common.request({
                url: '/api/users/list',
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
