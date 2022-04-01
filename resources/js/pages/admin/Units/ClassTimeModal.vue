<template>
    <b-modal hide-footer :visible="visible" @hidden="onHidden" @shown="onShown" :title="classModel.name ? 'Aula: '+classModel.name : ''">
        <b-form @submit.prevent="save">
            <div class="row">
                <div class="col-md-12">
                    <b-form-group>
                        <label>Dia da Semana</label>
                        <b-form-select 
                            :options="weekDays"
                            v-model="classTime.weekday"
                        >
                        </b-form-select>
                    </b-form-group>
                </div>
                <div class="col-md-6">
                    <b-form-group>
                        <label>Início</label>
                        <b-form-input type="time" v-model="classTime.start_at"/>
                    </b-form-group>
                </div>
                <div class="col-md-6">
                    <b-form-group>
                        <label>Fim</label>
                        <b-form-input type="time" v-model="classTime.end_at"/>
                    </b-form-group>
                </div>
                <div class="col-12 text-right">
                    <b-button @click="onHidden">Cancelar</b-button>
                    <b-button type="submit" variant="primary">
                        <b-icon icon="check"></b-icon>
                        Salvar
                    </b-button>
                </div>
            </div>
        </b-form>
    </b-modal>
</template>

<script>
import common from '../../../common/common'
export default {
    props:{
        visible: Boolean,
        classModel: Object,
        classTimeId: Number
    },
    methods:{
        onShown(){
            this.classTime.id_class = this.classModel.id
            console.log(this.classModel.id)
            if( this.classTimeId ){
                this.getById(this.classTimeId)
            }
        },
        onHidden(){
            this.classTime = {}
            this.$emit('onHidden', this.visible)
        },
        save(){
            if( this.classTimeId ){
                // update
                common.request({
                    url: '/api/class-time/'+this.classTimeId,
                    type: 'put',
                    auth: true,
                    data: this.classTime,
                    setError: true,
                    load: true,
                    success: (resp) => {
                        this.$emit('onSave', resp)
                    }
                })
            } else {

                // create
                common.request({
                    url: '/api/class-time',
                    type: 'post',
                    auth: true,
                    data: this.classTime,
                    setError: true,
                    load: true,
                    success: (resp) => {
                        this.$emit('onSave', resp)
                    }
                })
            }
        },
        getById( id ){
            common.request({
                url: '/api/class-time/'+id,
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (classTIme) => {
                    this.classTime = classTIme
                }
            })
        }
    },
    data: () => ({
        classTime: {},
        weekDays: [
            {value: 0, text:'Segunda'},
            {value: 1, text:'Terça'},
            {value: 2, text:'Quarta'},
            {value: 3, text:'Quinta'},
            {value: 4, text:'Sexta'},
            {value: 5, text:'Sábado'},
            {value: 6, text:'Domingo'}
        ]
    }),
    
}
</script>