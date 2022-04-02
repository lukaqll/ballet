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
                                            <label>Status</label>
                                            <b-form-select :options="status" class="w-100" v-model="filter.status"></b-form-select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Unidade</label>
                                            <b-form-select :options="unitsOptions" class="w-100" v-model="filter.id_unit"></b-form-select>
                                        </div>
                                        <div class="col-md-6" style="margin-top: 2rem">
                                            <b-button @click="getUsers">Buscar</b-button>
                                            <b-button variant="danger" @click="filter = {}">Limpar</b-button>
                                            <b>{{ users.length }} resultados</b>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mt-4">
                                    <div class="my-2">
                                        <b-form-input size="sm" v-model="tableFilter" placeholder="Buscar"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive" v-if="users.length">
                                    <b-table
                                        :fields="tableFields"
                                        :items="usersTable"
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
                                                <span v-if="row.item.open_invoices && row.item.open_invoices.length">
                                                    <i class="fa fa-dollar-sign"></i>
                                                </span>
                                        </template>
                                        <template #cell(actions)="row">

                                            <b-dropdown :id="'dropdown-'+row.item.id" size="sm" variant='light'>
                                                <template #button-content >
                                                    <b-icon icon="three-dots-vertical"></b-icon>
                                                </template>
                                                <b-dropdown-item @click="e => editUser(row.item.id)">
                                                    <b-icon icon="pencil-square"></b-icon> Editar
                                                </b-dropdown-item>

                                                <b-dropdown-item @click="e => getUserInvoices(row.item.id)">
                                                    <i class="fa fa-dollar-sign"></i> Faturas 
                                                    <b v-if="row.item.open_invoices.length">
                                                        ({{row.item.open_invoices.length}} faturas abertas)
                                                    </b>
                                                </b-dropdown-item>

                                                <b-dropdown-item @click="() => toPasswordUpdateId = row.item.id">
                                                    <b-icon icon="key"></b-icon> Alterar Senha
                                                </b-dropdown-item>

                                            </b-dropdown>

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
                { key: 'students', label: 'Alunos' },
                { key: 'alerts', label: '' },
                { key: 'actions', label: '' },
            ]
        },
        status(){
            return [
                {text: 'Todos', value: null},
                {text: 'Ativo', value: 'A'},
                {text: 'Inadimplente', value: 'P'},
                {text: 'Inativo', value: 'I'},
                {text: 'Matrícula Pendente', value: 'MP'},
            ]
        },
        unitsOptions(){
            return this.units.map(un => ({ text: un.name, value: un.id }))
        },
        usersTable(){

            return this.users.map((user) => {
                return {...user, _rowVariant: user.open_invoices && user.open_invoices.length ? 'danger' : null}
            })
        }
    },

       
    mounted: function(){
        this.getUsers()
        this.getUnits()
    },

    data: () => ({
        users: [],
        editableUser: {},
        newUserModalShow: false,
        toPasswordUpdateId: null,
        editableStudentId: null,
        studentModalShow: false,
        filter: {status: null},
        tableFilter: '',
        units: [],

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
        },
        getUnits(){
            common.request({
                url: '/api/units/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (units) => {
                    this.units = units
                }
            })
        },
    }
}
</script>
