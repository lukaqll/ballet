<template>
<div>
        
        <div class="row">
            <div class="col-12">
                <div>
                    <b-card no-body class="border-0 shadow-sm">

                        <b-card-body>
                            <div class="row gap-2">
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
                                            <b-button variant="danger" @click="filter = {status: null, id_unit: null}">Limpar</b-button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex flex-column gap-1">
                                    <b>{{ users.length }} resultado{{users.length != 1 ? 's' : ''}}</b>
                                    <div class="d-flex align-items-center gap-3" v-if="expiredUsersCount">
                                        <b>{{expiredUsersCount}} usuário{{expiredUsersCount != 1 ? 's' : ''}} com faturas abertas</b>
                                        <b-button size="sm" variant="info" class="py-0" @click="sendMail">Enviar email</b-button>
                                    </div>
                                    <div class="d-flex align-items-center gap-3" v-if="unsignedUsers">
                                        <b>{{unsignedUsers}} assinatura{{unsignedUsers != 1 ? 's' : ''}} pendente{{unsignedUsers != 1 ? 's' : ''}}</b>
                                        <b-button size="sm" variant="info" class="py-0" @click="generateInvoices">Gerar faturas</b-button>
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-4 gap-2" v-if="users.length">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <b-form-input size="sm" v-model="tableFilter" placeholder="Buscar"></b-form-input>
                                        </div>
                                        <div class="col">
                                            <b-button class="float-right" v-if="selectedUsers.length" size="sm" variant="danger" @click="$bvModal.show('delete-selected')">Deletar</b-button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 table-responsive">
                                    <b-table
                                        :fields="tableFields"
                                        :items="usersTable"
                                        :filter="tableFilter"
                                        hover
                                    >
                                        <template #head(select)>
                                            <b-form-checkbox v-model="selectAll" @change="handleSelectAll()"></b-form-checkbox>
                                        </template>
                                        <template #cell(select)="row">
                                            <b-form-checkbox name="selected-users" v-model="selectedUsers" :value="row.item.id"></b-form-checkbox>
                                        </template>
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

                                                <b-dropdown-item @click="e => getUserInvoices(row.item)">
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

                            </div>
                            <div v-else>
                                <h6 class="text-center text-secondary">Nenhum resultado</h6>
                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
        </div>

        <b-modal  hide-footer id="delete-selected">
            <div>
                <div v-if="selectedUsers.length == 1">
                    <h2 class="text-center">Deseja deletar o usuário selecionado?</h2>
                    <h5 v-if="activeUserSelected" class="text-danger text-center">Este usuário está ativo</h5>
                </div>
                <div v-else>
                    <h2 class="text-center">Deseja deletar os {{selectedUsers.length}} usuários selecionados?</h2>
                    <h5 v-if="activeUserSelected" class="text-danger text-center">Existem usuários ativos selecionados</h5>
                </div>
                <p class="text-center">
                    Esta ação será irreversível
                    <br>
                    Digite a palavra 'confirmar' para deletar
                </p>
                <b-form-input v-model="confirmationDelete"></b-form-input>
                <div class="text-right mt-3">
                    <b-button @click="() => {$bvModal.hide('delete-selected'); confirmationDelete=''}">Cancelar</b-button>
                    <b-button variant="danger" @click="deleteSelected">
                        Deletar
                    </b-button>
                </div>
            </div>
        </b-modal>

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
            @update="getUnsignedUsersToInvoice"
        />

        <password-update-modal
            :visible="toPasswordUpdateId ? true : false"
            :idUser="toPasswordUpdateId"
            @onHidden="() => toPasswordUpdateId = null"
            @onSave="onPasswordUpdate"
        />

        <user-invoices
            :visible="userInvoice ? true : false"
            :user="userInvoice"
            @onHidden="() => userInvoice = null"
        />


        
    </div>
</template>

<script>
import common from '../../../common/common'
import NewUserModal from './NewUserModal';
import EditUserModal from './EditUserModal';
import StudentModal from '../Students/StudentModal';
import PasswordUpdateModal from './PasswordUpdateModal';
import DataTable from "vue-materialize-datatable";
import UserInvoices from "./UserInvoices";

export default {
    components: { NewUserModal, EditUserModal, StudentModal, PasswordUpdateModal,  DataTable, UserInvoices },

    computed: {
        tableFields(){
            return [
                { key: 'select', label: ''},
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
            const units = this.units.map(un => ({ text: un.name, value: un.id }))
            units.unshift({text: 'Todos', value: null})
            return units
        },
        usersTable(){

            return this.users.map((user) => {
                return {...user, _rowVariant: user.open_invoices && user.open_invoices.length ? 'danger' : null}
            })
        },
        expiredUsersCount(){
            return this.users.filter(user => !!user?.open_invoices?.length).length
        },
        activeUserSelected() {
            let sel = false
            this.users.forEach(us => {
                if (this.selectedUsers.find(i => i == us.id && us.status == 'A'))
                    sel = true
            })
            return sel
        }
    },

    watch: {
        selectedUsers: function(val) {
            if (val.length == this.users.length) {
                this.selectAll = true
            } else {
                this.selectAll = false
            }
        },

        users: function(val) {
            this.selectAll = false
            this.selectedUsers = []
        },
        tableFilter: function(val) {
            this.selectAll = false
            this.selectedUsers = []
        }
    },
       
    mounted: function(){
        this.getUsers()
        this.getUnits()
        this.getUnsignedUsersToInvoice()
    },

    data: () => ({
        users: [],
        editableUser: {},
        newUserModalShow: false,
        toPasswordUpdateId: null,
        editableStudentId: null,
        studentModalShow: false,
        filter: {status: null, id_unit: null},
        tableFilter: '',
        units: [],
        selectedUsers: [],
        selectAll: false,
        confirmationDelete: '',

        userInvoice: null,
        unsignedUsers: 0
    }),

    methods: {
        getUsers(load = true){
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
                load,
                success: (users) => {
                    this.users = users
                }
            })
        },

        getUnsignedUsersToInvoice() {
            common.request({
                url: '/api/invoices/unsigned',
                type: 'get',
                auth: true,
                setError: true,
                load: false,
                success: (count) => {
                    this.unsignedUsers = count
                }
            })
        },

        generateInvoices() {
            common.confirmAlert({
                title: 'Isso criará uma fatura para todos os usuários com assinatura pendente',
                confirmButtonText: 'Confirmar',
                onConfirm: () => {
                    common.request({
                        url: '/api/invoices/unsigned/generate',
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (resp) => {
                            const message = resp.map(item => `${item.name}: ${item.message}`).join('.<br>')
                            this.getUsers(false)
                            common.confirmAlert({message: message, confirmButtonText: 'Ok', showCancelButton: false, type: 'success'})
                        }
                    })
                }
            })
        },

        sendMail() {
            common.confirmAlert({
                title: 'Isso enviará um E-mail para cada fatura aberta de todos os usuários',
                confirmButtonText: 'Confirmar',
                onConfirm: () => {
                    common.request({
                        url: '/api/invoices/mail/all',
                        type: 'post',
                        auth: true,
                        setError: true,
                        load: true,
                        success: (resp) => {
                            common.success({title: 'E-mails enviados'})
                        }
                    })
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
        getUserInvoices(user){
            this.userInvoice = user
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

        handleSelectAll(val) {
            if (!val && this.selectedUsers.length == this.users.length) {
                this.selectedUsers = []
            } else if (this.selectedUsers.length != this.users.length) {
                this.selectedUsers = this.users.map(us => us.id)
            }
        },

        deleteSelected() {
            if( this.confirmationDelete != 'confirmar' ){
                common.setError({
                    message: 'Informe o termo correto para confirmar'
                })
                return
            }

            common.request({
                url: '/api/users/delete-multiple',
                type: 'post',
                auth: true,
                setError: true,
                load: true,
                data: {
                    confirmation: this.confirmationDelete,
                    ids: this.selectedUsers
                },
                success: (data) => {
                    this.onEditUserSave()
                    this.confirmationDelete = ''
                    this.$bvModal.hide('delete-selected')
                }
            })
        }
    }
}
</script>
