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
                                        Alunos    
                                        <b-button class="float-right" variant="primary" @click="() => studentModalShow = true">Novo Aluno</b-button>
                                    </h3>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <b-form-select :options="status" class="w-100" v-model="filter.status"></b-form-select>
                                        </div>
                                        <div class="col-md-4">
                                            <b-button @click="getStudents">Buscar</b-button>
                                            <b-button variant="danger" @click="filter = {}">Limpar</b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive">
                                    <data-table
                                        :rows="students"
                                        :columns="studentsBindings"
                                        locale="br"
                                        title=''
                                        :perPage="[50, 100, 200]"
                                        :clickable="false"
                                    >
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
                                            <td>
                                                <b-badge v-if="props.row.open_contracts_count" variant="primary">
                                                    {{props.row.open_contracts_count}} Contratos Abertos
                                                </b-badge>
                                            </td>
                                            <td>
                                                <b-button variant="outline" size="sm" @click="e => editStudent(props.row.id)">
                                                    <b-icon variant="primary" icon="pencil-square"></b-icon>
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
        studentsBindings(){
            return  [
                    {field: 'name', label: 'Nome'},
                    {field: 'status_text', label: 'status'},
                    {field: 'nick_name', label: 'Apelido'},
                    {field: 'user.name', label: 'Usuário'},
                ]
        },

        status(){
            return [
                {text: 'Ativo', value: 'A'},
                {text: 'Inativo', value: 'I'},
                {text: 'Matrícula Pendente', value: 'MP'},
                {text: 'Contrato Pendente', value: 'CP'},
            ]
        }
    },

       
    mounted: function(){
        this.getStudents()
    },

    data: () => ({
        students: [],
        editableUser: {},
        newUserModalShow: false,
        toPasswordUpdateId: null,
        editableStudentId: null,
        studentModalShow: false,

        filter: {}
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
                url: '/api/students/list?'+queryString,
                type: 'get',
                auth: true,
                setError: true,
                success: (students) => {
                    this.students = students
                    console.log(students)
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
