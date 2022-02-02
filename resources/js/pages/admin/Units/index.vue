<template>
    
    <admin-base>
        <div class="row">
            <div class="col-12">

                <b-card no-body class="border-0 shadow-sm">

                    <b-card-body>

                        <div class="row">
                            <div class="col-12">
                                <h3>
                                    Unidades
                                    <b-button variant="primary" class="float-right" @click="() => unitModalShow = true">
                                        Nova Unidade
                                    </b-button>
                                </h3>
                            </div>
                        </div>
                        <div class="accordoin" role="tablist">
                            <div v-for="unit in units" :key="unit.id">

                                <b-card no-body class="border-0 shadow-sm">
                                    <b-card-header>
                                        <div class="row">
                                            <div class="col-9" v-b-toggle="'un-'+unit.id">
                                                {{unit.name}}
                                            </div>
                                            <div class="col-3 text-right">
                                                <b-button variant="light" size="sm" v-b-tooltip.hover title="Editar unidade" @click="editUnit($event, unit.id, unit.name)">
                                                    <b-icon class="text-primary" icon="pencil-square" ></b-icon>
                                                </b-button>
                                                <b-button variant="light" size="sm" v-b-tooltip.hover title="Adicionar aula" @click="addClass($event, unit)">
                                                    <b-icon class="text-primary" icon="plus-square" ></b-icon>
                                                </b-button>
                                            </div>
                                        </div>
                                    </b-card-header>
                                    <b-collapse :id="'un-'+unit.id" role="tabpanel">
                                        <b-card-body>
                                            
                                            <div class="row">
                                                
                                                <!-- classes -->
                                                <div class="col-md-4" v-for="cl in unit.classes" :key="cl.id">
                                                    <b-card no-body class="border-0 shadow-sm">
                                                        <b-card-body>
                                                            <div class="row">
                                                                <div class="col-8">
                                                                    <h4>{{cl.name}}</h4>
                                                                    <span class="text-secondary">{{cl.team}}</span>
                                                                </div>
                                                                <div class="col-4 text-right">
                                                                    <div>
                                                                        <b-dropdown :id="'dropdown-'+cl.id" size="sm" variant='light'>
                                                                            <template #button-content >
                                                                                <b-icon icon="three-dots-vertical"></b-icon>
                                                                            </template>
                                                                            <b-dropdown-item @click="editClass($event, cl.id)">
                                                                                <b-icon icon="pencil-square"></b-icon>
                                                                                Editar Aula
                                                                            </b-dropdown-item>
                                                                            <b-dropdown-item @click="addClassTime($event, cl.id)">
                                                                                <b-icon icon="calendar-plus"></b-icon>
                                                                                Adicionar Hrário
                                                                            </b-dropdown-item>
                                                                            <b-dropdown-item @click="deleteClass($event, cl.id)">
                                                                                <b-icon icon="trash"></b-icon>
                                                                                Deletar Aula
                                                                            </b-dropdown-item>

                                                                            <b-dropdown-item @click="() => toggleFull(cl.id)" v-if="!cl.full">
                                                                                <b-icon icon="battery-full"></b-icon>
                                                                                Marcar como lotada
                                                                            </b-dropdown-item>
                                                                            <b-dropdown-item @click="() => toggleFull(cl.id)" v-else>
                                                                                <b-icon icon="battery"></b-icon>
                                                                                Liberar Aula
                                                                            </b-dropdown-item>

                                                                        </b-dropdown>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h5>R$ {{toMoney(cl.value)}}</h5>
                                                                </div>
                                                                <div class="col-12">
                                                                    <b-badge variant="primary" pill>{{cl.students_count}} alunos</b-badge>
                                                                    <b-badge variant="danger" v-if="cl.full" pill>Lotada</b-badge>
                                                                </div>
                                                                <div class="col-12">
                                                                    <b-list-group>

                                                                        <!-- times -->
                                                                        <b-list-group-item v-for="time in cl.times" :key="time.id">
                                                                            <div class="row align-items-center">
                                                                                <div class="col-md-8">
                                                                                    <b-row>
                                                                                        {{time.weeday_text}}
                                                                                    </b-row>
                                                                                    <b-row>
                                                                                        {{time.start_at}}h às {{time.end_at}}h
                                                                                    </b-row>
                                                                                </div>
                                                                                <div class="col-md-4 text-right">
                                                                                    <b-button variant="light" size="sm" @click="editClassTime($event, time.id)" v-b-tooltip.hover title="Editar horário">
                                                                                        <b-icon icon="pencil-square" variant="primary"></b-icon>
                                                                                    </b-button>
                                                                                    <b-button variant="light" size="sm" @click="deleteClassTime($event, time.id)" v-b-tooltip.hover title="Remover Horário">
                                                                                        <b-icon icon="trash" variant="danger"></b-icon>
                                                                                    </b-button>
                                                                                </div>
                                                                            </div>
                                                                        </b-list-group-item>
                                                                        <!-- end times -->
                                                                    </b-list-group>
                                                                </div>
                                                            </div>
                                                        </b-card-body>
                                                    </b-card>
                                                </div>
                                                <!-- end classes -->
                                            </div>
                                        </b-card-body>
                                    </b-collapse>
                                </b-card>

                            </div>
                        </div>
                    </b-card-body>
                </b-card>
            </div>
        </div>
        <unit-modal 
            :visible="unitModalShow" 
            :editableUnit="editableUnit" 
            @onHidden="onUnitModalHidden" 
            @onSave="onUnitSave"
        />

        <class-modal 
            :visible="classModalShow" 
            :editableClassId="editableClassId" 
            @onHidden="onClassModalHidden" 
            @onSave="onClassSave"
            :unit="classUnit"
        />

        <class-time-modal 
            :visible="classTimeModalVisible"
            @onHidden="onClassTimeModalHidden"
            :classModel="classTimeClass"
            @onSave="onClassTimeSave"
            :classTimeId="classTimeId"
        />

    </admin-base>
</template>
<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'
import UnitModal from './UnitModal'
import ClassModal from './ClassModal'
import ClassTimeModal from './ClassTimeModal'
export default {
    components: {
        AdminBase,
        UnitModal,
        ClassModal,
        ClassTimeModal
    },
    mounted: function () {
        this.listUnits()
    },
    data: () => ({

        // units
        unitModalShow: false,
        units: [],
        editableUnit: null,

        // classes
        classModalShow: false,
        editableClassId: null,
        classUnit: {},

        // class time
        classTimeModalVisible: false,
        classTimeClass: {},
        classTimeId: null
    }),
    methods: {

        toMoney(str){
            return common.toMoney(str)
        },
        // units
        onUnitSave(unit) {
            this.unitModalShow = false
            this.editableUnit = null
            this.listUnits()
        },
        onUnitModalHidden(){
            this.unitModalShow = false
            this.editableUnit = null
        },
        listUnits() {
            common.request({
                url: '/api/units/list',
                type: 'get',
                auth: true,
                load: true,
                success: (units) => {
                    this.units = units
                }
            })
        },
        editUnit(evt, id, name){
            this.editableUnit = {
                id: id, name: name
            }
            this.unitModalShow = true
        },
        addClass(evt, unit){
            this.classUnit = unit
            this.classModalShow = true
        },

        // classes
        onClassModalHidden() {
            this.editableClassId = null
            this.classModalShow = false
        },
        onClassSave() {
            this.editableClassId = null
            this.classModalShow = false
            this.listUnits()
        },
        editClass(evt, id){
            this.editableClassId = id
            this.classModalShow = true
        },

        // class time
        addClassTime(evt, classModel){
            this.classTimeClass = classModel
            this.classTimeModalVisible = true
        },
        onClassTimeModalHidden(){
            this.classTimeModalVisible = false
            this.classTimeId = null
            this.classTimeClass = {}
        },
        onClassTimeSave( classTime ){
            this.classTimeModalVisible = false
            this.classTimeId = null
            this.classTimeClass = {}
            this.listUnits()
        },
        editClassTime(evt, idClassTime){
            this.classTimeModalVisible = true
            this.classTimeId = idClassTime
        },
        deleteClassTime(evt, idClassTime){
            common.confirmAlert({
                title: 'Deseja deletar este horário?',
                onConfirm: () => {
                    common.request({
                        url: '/api/class-time/'+idClassTime,
                        type: 'delete',
                        auth: true,
                        setError: true,
                        load: true,
                        success: () => {
                            this.listUnits()
                        }
                    })
                }
            })
        },
        deleteClass(evt, idClass){
            common.confirmAlert({
                title: 'Deseja deletar esta aula?',
                onConfirm: () => {
                    common.request({
                        url: '/api/classes/'+idClass,
                        type: 'delete',
                        auth: true,
                        setError: true,
                        load: true,
                        success: () => {
                            this.listUnits()
                        }
                    })
                }
            })
        },
        
        toggleFull(idClass){
            common.request({
                url: '/api/classes/toggle-full/'+idClass,
                type: 'post',
                auth: true,
                setError: true,
                load: true,
                success: () => {
                    this.listUnits()
                }
            })
        }

    }
}
</script>