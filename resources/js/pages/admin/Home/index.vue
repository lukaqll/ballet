<template>
    <admin-base>

        <b-container>
            <div class="row">

                <div class="col-md-3 d-flex" v-for="comp in componentsData" :key="comp.title">

                    <router-link :to="comp.link" tag="span" class="hover w-100 h-100 d-flex">
                        <b-card no-body :bg-variant="comp.color" class="w-100 my-1">
                            <div class="h-100 w-100">
                                <div class="row text-white p-2">
                                    <div class="col-4">
                                        <b-icon :icon="comp.icon" class="h1"></b-icon>
                                    </div>
                                    <div class="col-8">
                                        <h3 class="text-right">{{ data[comp.data] }}</h3>
                                        <p class="text-right">{{comp.title}}</p>
                                    </div>
                                </div>
                            </div>
                        </b-card>
                    </router-link>

                </div>

            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-12">
                    <h3 class="text-center">Aniversariantes do mês</h3>
                </div>
                
                <template v-if="data.birthdays && data.birthdays.length">
                    <div class="col-md-3" v-for="student in data.birthdays" :key="student.id">
                        <b-card class="border-0 shadow-sm">
                            <div class="row">
                                <div class="col-12" v-if="student.picture">
                                    <img :src="`/storage/${student.picture}`" alt="avatar" class="img-fluid rounded">
                                </div>
                            </div>
                            <h4 class="text-center">
                                {{student.name}}
                            </h4>
                            <h5 class="text-center">
                                {{formatBirthdate(student.birthdate)}}
                            </h5>
                        </b-card>
                    </div>
                </template>
                <template v-else>
                    <span class="text-center">Nenhum Aniversariante</span>
                </template>
            </div>
        </b-container>
           
    </admin-base>
</template>

<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'

export default {
    components: { AdminBase },
    data: () => ({
        data: {},
        componentsData: [
            {icon: 'person-plus-fill', color: 'success', data: 'usersRegPendent', title: 'Novas Matrículas', link: '/admin/new-users'},
            {icon: 'file-earmark-medical-fill', color: 'danger', data: 'openContracts', title: 'Contratos Abertos', link: '/admin/contracts'},
            {icon: 'person-badge-fill', color: 'primary', data: 'studentsRegPendent', title: 'Alunos Pendentes de Matrícula', link: '/admin/students'},
            {icon: 'person-badge', color: 'warning', data: 'studentsContPendent', title: 'Alunos Pendentes de Contrato', link: '/admin/students'},
        ]
    }),
    mounted(){
        this.getHome()
    },
    methods: {
        getHome(){
            common.request({
                type: 'get',
                url: '/api/get-home',
                auth: true,
                success: (resp) => {
                    this.data = resp
                }
            })
        },
        formatBirthdate(str){
            if(str == null || str == '' || !str) return ''

            const dateTimeSplit = str.match('T') ? str.split('T') : str.split(' ')

            const parts = dateTimeSplit[0].split('-')
            const day = parts[2]
            const month = parts[1]
            const year = parts[0]
            
            const date = new Date()

            return day + '/' + month + '/' + date.getFullYear()
        }
    }

}
</script>
