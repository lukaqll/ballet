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
                                        Alunos    
                                        <b-button class="float-right" variant="primary" @click="() => studentModalShow = true">Novo Aluno</b-button>
                                    </h3>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Status</label>
                                            <b-form-select :options="status" class="w-100" v-model="filter.status"></b-form-select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Unidade</label>
                                            <b-form-select :options="unitsOptions" class="w-100" v-model="filter.id_unit"></b-form-select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Aula</label>
                                            <b-form-select :options="classesOptions" class="w-100" v-model="filter.id_class"></b-form-select>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mt-2">
                                                <b-button @click="getStudents">Buscar</b-button>
                                                <b-button variant="danger" @click="filter = {}">Limpar</b-button>
                                                <span class="ml-2">
                                                    <b>{{ students.length }} resultados</b>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="my-2">
                                                <b-form-input size="sm" v-model="tableFilter" placeholder="Buscar"></b-form-input>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive" v-if="students.length">

                                    <b-table
                                        :fields="tableFields"
                                        :items="studentsItems"
                                        :filter="tableFilter"
                                        hover
                                    >
                                        <template #cell(classes)="row">
                                            <b-badge class="mr-1" pill v-for="cl in row.item.student_classes" :key="cl.id" :variant="cl.approved_at ? 'secondary' : 'danger'">{{cl.class.name}}</b-badge>
                                        </template>
                                        <template #cell(contracts)="row">
                                            <b-badge pill v-if="row.item.open_contracts_count" variant="danger">
                                                {{row.item.open_contracts_count}} Contratos Abertos
                                            </b-badge>
                                        </template>
                                        <template #cell(actions)="row">
                                            <b-button variant="light" size="sm" @click="e => editStudent(row.item.id)">
                                                <b-icon icon="pencil-square"></b-icon>
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

        <student-modal
            :isVisible="studentModalShow"
            :idStudent="editableStudentId"
            :user="editableUser"
            @onHidden="onStudentModalHidden"
            @onSave="onStudentSave"
        />
        
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase/index.vue'
import StudentModal from './StudentModal';
import DataTable from "vue-materialize-datatable";

export default {
    components: { AdminBase, StudentModal, DataTable },

    computed: {
        tableFields(){
            return [
                { key: 'name', label: 'Nome', sortable: true },
                { key: 'status_text', label: 'Status', sortable: true },
                { key: 'user_name', label: 'Usuário', sortable: true },
                { key: 'classes', label: '' },
                { key: 'contracts', label: '' },
                { key: 'actions', label: '' },
            ]
        },
        studentsItems(){
            return this.students.map(st => (
                {...st, user_name: st.user.name}
            ))
        },
        status(){
            return [
                {text: 'Ativo', value: 'A'},
                {text: 'Inativo', value: 'I'},
                {text: 'Matrícula Pendente', value: 'MP'},
                {text: 'Contrato Pendente', value: 'CP'},
            ]
        },

        unitsOptions(){
            return this.units.map(un => ({ text: un.name, value: un.id }))
        },

        classesOptions(){
            return this.classes.map(cl => ({ html: `${cl.name} (${cl.unit_name})`, value: cl.id }))
        }
    },

       
    mounted: function(){
        this.getStudents()
        this.getUnits()
        this.getClasses()
    },

    data: () => ({
        students: [],
        editableUser: {},
        newUserModalShow: false,
        toPasswordUpdateId: null,
        editableStudentId: null,
        studentModalShow: false,

        units: [],
        classes: [],
        filter: {},
        tableFilter: ''
    }),

    methods: {
        getStudents(){

            let filters = {}
            for(const key in this.filter){
                if( this.filter[key] )
                    filters[key] = this.filter[key]
            }
            let queryString = new URLSearchParams(filters)

            common.request({
                url: '/api/students/filter?'+queryString,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (students) => {
                    this.students = students
                }
            })
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
        getClasses(){
            common.request({
                url: '/api/classes/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (classes) => {
                    this.classes = classes
                }
            })
        },

        // student
        onStudentModalHidden() {
            this.editableStudentId = null
            this.studentModalShow = false
        },
        onStudentSave(student) {
            this.editableStudentId = null
            this.studentModalShow = false
            this.getStudents()
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
