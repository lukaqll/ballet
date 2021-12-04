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
                <div class="col-12">
                    <b-form-group>	
                        <label>Nome</label>
                        <b-form-input v-model="unit.name"/>
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
                    success: (resp) => {
                        this.unit = {}
                        this.onSave && this.onSave(resp)
                    }
                })
            }
        }
    }
}
</script>