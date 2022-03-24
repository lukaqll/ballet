<template>
    <dashboard-base
        :title="'Usuários'"
    >
        
        <div class="row">

            <div class="col-12 mt-3">
                <h3 class="ml-3">Alunos</h3>
                <hr>
            </div>
            <div class="col-12">
                
                <div class="row justify-content-center">

                    <div class="col-md-6 my-2" v-for="student in students" :key="student.id">

                        <b-card class="border-0 shadow-sm">
                            <div class="row">
                                <div class="col-12" v-if="student.picture">
                                    <img :src="student.picture" alt="" class="img-fluid rounded">
                                </div>
                                <div class="col-12">
                                    <h4 class="text-center">{{ student.name }}</h4>
                                    <p>
                                        Aniversário: <b>{{student.birthdate_formated}}</b> <br>
                                        Status: <b>{{student.status_text}}</b> <br>
                                        <span v-if="student.classes.length">Aulas: <b>{{ student.classes.map(i => i.name).join(', ') }}.</b></span>
                                        <span v-else>Aulas: Nenhuma aula ainda</span>
                                    </p>
                                </div>
                                <div class="col-12">
                                    <b-button variant="block" class="btn-light" @click="() => editStudent(student.id)">Editar</b-button>
                                </div>
                            </div>
                        </b-card>
                    </div>

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
        
    </dashboard-base>
</template>

<script>
import common from '../../../common/common'
import DashboardBase from '../../../components/DashboardBase'
import StudentModal from './StudentModal.vue';
import DataTable from "vue-materialize-datatable";

export default {
    components: { DashboardBase, StudentModal, DataTable },

    computed: {
        studentsBindings(){
            return  [
                    {field: 'name', label: 'Nome'},
                    {field: 'status_text', label: 'status'},
                    {field: 'nick_name', label: 'Apelido'},
                    {field: 'user.name', label: 'Usuário'},
                ]
        },
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
                url: '/api/students/list-self',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
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
