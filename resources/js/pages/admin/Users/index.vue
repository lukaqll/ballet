<template>
    <admin-base
        :title="'Usuários'"
    >
        
        <div class="row">
            <div class="col-12">
                <div>
                    <b-card no-body class="border-0 shadow-sm">

                        <b-card-body>
                            <div class="row">
                                <div class="col-12">
                                    <h3>
                                        Usuários    
                                        <b-button class="float-right" variant="primary" @click="() => newUserModalShow = true">Novo Usuário</b-button>
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
                                        <template #cell(alerts)="row">
                                                <span v-if="!row.item.signer_key" v-b-tooltip.hover title="Usuário não cadastrado como signatário">
                                                    <b-icon icon='exclamation-triangle' variant="danger"/>
                                                </span>
                                        </template>
                                        <template #cell(actions)="row">
                                            <b-button variant="light" size="sm" @click="e => editUser(row.item.id)" v-b-tooltip.hover title="Editar usuário">
                                                <b-icon  icon="pencil-square"></b-icon>
                                            </b-button>
                                            <b-button 
                                                :variant="row.item.open_invoices && row.item.open_invoices.length ? 'danger' : 'light'" 
                                                size="sm" 
                                                @click="e => getUserInvoices(row.item.id)"
                                                v-b-tooltip.hover :title="`${row.item.open_invoices.length} faturas abertas`"
                                            >
                                                <i class="fa fa-dollar-sign"></i>
                                            </b-button>
                                            <b-button variant="light" size="sm" @click="() => toPasswordUpdateId = row.item.id" v-b-tooltip.hover title="Alterar senha">
                                                <b-icon  icon="key"></b-icon>
                                            </b-button>
                                        </template>
                                    </b-table>

                                </div>
                                <div v-else>
                                    <h6 class="text-center text-secondary">Nenhum resultado</h6>

                                    
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

        <user-invoices
            :visible="idUserInvoice ? true : false"
            :idUser="idUserInvoice"
            @onHidden="() => idUserInvoice = null"
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
import UserInvoices from "./UserInvoices";

export default {
    components: { AdminBase, NewUserModal, EditUserModal, StudentModal, PasswordUpdateModal,  DataTable, UserInvoices },

    computed: {
        tableFields(){
            return [
                { key: 'name', label: 'Nome', sortable: true },
                { key: 'status_text', label: 'Status', sortable: true },
                { key: 'email', label: 'E-mail', sortable: true },
                { key: 'students', label: 'Alunos' },
                { key: 'alerts', label: '' },
                { key: 'actions', label: '' },
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
        tableFilter: '',

        idUserInvoice: null,
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
                load: true,
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
                load: true,
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
        getUserInvoices(idUser){
            this.idUserInvoice = idUser
        }
    }
}
</script>
