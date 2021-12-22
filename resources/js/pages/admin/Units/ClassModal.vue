<template>
    <b-modal 
        hide-footer 
        :visible="visible" 
        @hidden="onHidden" 
        @shown="onShown" 
        :title="classModel.id ? 'Editar Aula ' + classModel.name : 'Adicionar aula à '+unit.name"
        
    >

        <b-form @submit.prevent="save">
            <div class="row">
                <div class="col-12">
                    <b-form-group>	
                        <label>Nome</label>
                        <b-form-input v-model="classModel.name"/>
                    </b-form-group>
                </div>

                <div class="col-12">
                    <b-form-group>	
                        <label>Valor</label>
                        <b-form-input v-model="classModel.value" v-money="moneyMask"/>
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
        unit: Object,
        editableClassId: [String, Number]
    },
    data: () => ({
        classModel: {},
        moneyMask: {
            decimal: ',',
            thousands: '.',
            precision: 2,
        }
    }),
    methods: {
        onHidden(){
            this.classModel = {}
            this.$emit('onHidden', this.visible)
        },

        onShown() {
            this.classModel.id_unit = this.unit.id
            if( this.editableClassId ){
                this.getById(this.editableClassId)
            }
        },

        save(){
            if( this.classModel.id ){

                // update
                common.request({
                    url: '/api/classes/'+this.classModel.id,
                    type: 'put',
                    auth: true,
                    data: this.classModel,
                    setError: true,
                    success: (resp) => {
                        this.classModel = {}
                        this.$emit('onSave', resp)
                    }
                })
            } else {

                // create
                common.request({
                    url: '/api/classes',
                    type: 'post',
                    auth: true,
                    data: this.classModel,
                    setError: true,
                    success: (resp) => {
                        this.classModel = {}
                        this.$emit('onSave', resp)
                    }
                })
            }
        },

        getById(id){
            common.request({
                url: '/api/classes/'+id,
                type: 'get',
                auth: true,
                setError: true,
                success: (resp) => {
                    resp.value = common.toMoney(resp.value)
                    this.classModel = resp
                }
            })
        }
    }
}
</script>