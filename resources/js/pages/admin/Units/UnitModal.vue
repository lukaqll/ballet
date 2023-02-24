<template>
    <b-modal 
        hide-footer 
        :visible="visible" 
        @hidden="onHidden" 
        @shown="onShown" 
        ref="bv-modal-unit"
        :title="unit.id ? 'Editar Unidade '+unit.name : 'Nova Unidade'"
    >

        <b-form @submit.prevent="save">
            <div class="row">
                <div class="col-md-6">
                    <b-form-group>	
                        <label for="name">Nome</label>
                        <b-form-input id="name" v-model="unit.name"/>
                    </b-form-group>
                </div>
                <div class="col-md-6">
                    <b-form-group>
                        <label for="day">Dia de vencimento</label>
                        <b-form-input id="day" type="number" min="1" max="31" v-model="unit.due_day" value="1"/>
                    </b-form-group>
                </div>
                <div class="col-12 text-right">
                    <b-button @click="hideModal">Cancelar</b-button>
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
        editableUnit: [Object, null]
    },
    data: () => ({
        unit: {}
    }),
    methods: {
        onHidden(){
            this.unit = {}
            this.$emit('onHidden', this.visible)
        },


        hideModal(){
            this.$refs['bv-modal-unit'].hide()
        },

        onShown() {
            if( this.editableUnit )
                this.unit = this.editableUnit
        },

        save(){
            if( this.unit.id ){

                // update
                common.request({
                    url: '/api/units/'+this.unit.id,
                    type: 'put',
                    auth: true,
                    data: this.unit,
                    load: true,
                    setError: true,
                    success: (resp) => {
                        this.unit = {}
                        this.$emit('onSave', resp)
                    }
                })
            } else {

                // create
                common.request({
                    url: '/api/units',
                    type: 'post',
                    auth: true,
                    data: this.unit,
                    setError: true,
                    load: true,
                    success: (resp) => {
                        this.unit = {}
                        this.$emit('onSave', resp)
                    }
                })
            }
        }
    }
}
</script>