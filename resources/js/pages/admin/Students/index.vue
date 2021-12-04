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

                                <div class="col-12">
                                </div>
                            </div>
                            <div>

                                <div class="table-responsive">
                                    <data-table
                                        :rows="students"
                                        :columns="studentsBindings"
                                        locale="br"
                                        title=''
                                        :defaultPerPage="50"
                                    >
                                        <th slot="thead-tr"></th>
                                        <template slot="tbody-tr" slot-scope="props">
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
                    {field: 'nick_name', label: 'Apelido'},
                    {field: 'birthdate_formated', label: 'Aniversário'},
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
    }),

    methods: {
        getStudents(){
            common.request({
                url: '/api/students/list',
                type: 'get',
                auth: true,
                setError: true,
                success: (students) => {
                    this.students = students
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
