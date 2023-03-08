<template>
    <b-modal 
        hide-footer 
        :visible="visible" 
        @hidden="onHidden" 
        @shown="onShown" 
        ref="bv-modal-methods"
        title="Métodos de pagamento"
    >
        <div class="row gap-2">
            <div class="col-12" v-for="(item, index) in methods" :key="index">
                <b-form @submit.prevent="() => update(item)"> 
                    <div class="row d-flex justify-content-between">
                        <div class="col-10">
                            <b-form-input v-if="editing.id === item.id" v-model="editing.name"/>
                            <b-form-input v-else @focus="() => {(editing = {...item})}" v-model="item.name"/>
                        </div>
                        <div class="col-2 pl-0">
                            <b-button type="submit" v-if="editing.id === item.id && editing.name !== item.name" variant="link" class="btn-lg" title="Salvar"><b-icon variant="success" icon="check-circle"></b-icon></b-button>
                            <b-button @click="() => deleteMethod(item.id)" variant="link" class="btn-lg" title="Excluir"><b-icon variant="danger" icon="x-circle"></b-icon></b-button>
                        </div>
                    </div>
                </b-form>
            </div>
            <div class="col-12 pt-2">
                <b-form @submit.prevent="create">
                    <div class="row d-flex justify-content-between">
                        <div class="col-10">
                            <b-form-input v-model="newMethod"/>
                        </div>
                        <div class="col-2 pl-0">
                            <b-button type="submit" :disabled="!newMethod" variant="link" class="btn-lg" title="Adicionar"><b-icon variant="success" icon="plus-circle"></b-icon></b-button>
                        </div>
                    </div>
                </b-form>
            </div>
        </div>
    </b-modal>
</template>

<script>
import common from '../../../common/common'
export default {
    props:{
        visible: Boolean,
        list: [Array, []]
    },
    data: () => ({
        methods: [],
        editing: {},
        newMethod: ''
    }),
    methods: {
        onHidden(){
            this.methods = []
            this.editing = {}
            this.$emit('onHidden', this.visible)
        },

        hideModal(){
            this.$refs['bv-modal-methods'].hide()
        },

        onShown() {
            if (this.list.length)
                this.methods = this.list
        },

        update(item) {
            if (this.editing.id !== item.id || this.editing.name === item.name) return;

            common.request({
                url: '/api/payment-methods/'+this.editing.id,
                type: 'put',
                auth: true,
                data: {name: this.editing.name},
                load: true,
                setError: true,
                success: (resp) => {
                    const items = [...this.methods]
                    items[this.methods.findIndex(i => i.id == this.editing.id)] = resp
                    this.methods = [...items]
                    this.editing = {}
                    this.$emit('onSave', resp)
                }
            })
        },
        create() {
            if (!this.newMethod) return;

            common.request({
                url: '/api/payment-methods',
                type: 'post',
                auth: true,
                data: {name: this.newMethod},
                setError: true,
                load: true,
                success: (resp) => {
                    this.methods = [
                        ...this.methods,
                        resp
                    ]
                    this.newMethod = ''
                    this.$emit('onSave', resp)
                }
            })
        },

        deleteMethod(id) {
            common.request({
                url: '/api/payment-methods/'+id,
                type: 'delete',
                auth: true,
                setError: true,
                load: true,
                success: (resp) => {
                    const items = [...this.methods]
                    items.splice(items.findIndex(i => i.id == id), 1)
                    this.methods = [...items]
                }
            })
        }
    }
}
</script>

<style scoped>
    .btn-lg {
        padding: 0;
    }
    .btn .b-icon.bi {
        vertical-align: bottom;
    }
</style>