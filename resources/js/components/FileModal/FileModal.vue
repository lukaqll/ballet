<template>
    <b-modal
        :title="title || 'Arquivo'"
        hide-footer 
        :visible="visible" 
        @hidden="onHidden"
        ref="bv-modal-file"
    >
        <b-form @submit.prevent="upload">
            <div class="row">
                <div class="col-12">
                    <div>
                        <b-form-file
                            v-model="file"
                            placeholder="Escolha ou arraste um arquivo..."
                            drop-placeholder="Escolha ou arraste um arquivo..."
                        />
                        <div class="mt-3">Arquivo Selecionado: {{ file ? file.name : '' }}</div>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <b-button @click="hideModal">Cancelar</b-button>
                    <b-button variant="primary" type="submit"><b-icon icon="check"/>Salvar</b-button>
                </div>
            </div>
        </b-form>
    </b-modal>
</template>

<script>
export default {
    props:{
        visible: Boolean,
        title: [String, 'Arquivo']
    },
    data: () => ({
        file: null
    }),
    methods: {
        onHidden(){
            this.file = null
            this.$emit('onHidden', this.visible)
        },

        hideModal(){
            this.$refs['bv-modal-file'].hide()
        },

        upload() {
            this.$emit('upload', this.file)
        }
    }
}
</script>