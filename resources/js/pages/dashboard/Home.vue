<template>
    <div class="classtext-2x1 text-green-800">
        Dashboard Home
        {{user.name}}
    </div>
</template>

<script>
import common from '../../common/common'
export default {

    data: () => ({
        data: {}
    }),
    mounted(){
        this.getHome()
    },
    methods: {
        getHome(){
            // common.request({
            //     type: 'get',
            //     url: '/api/get-home',
            //     auth: true,
            //     success: (resp) => {
            //         this.data = resp
            //     }
            // })
        }
    }

}
</script>
