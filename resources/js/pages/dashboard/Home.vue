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
        user: {}
    }),
    mounted(){
        // common.request({
        //     type: 'get',
        //     url: '/api/user',
        //     auth: true,
        //     success: (resp) => {
        //         this.user = resp
        //     }, 
        //     error: e => {
        //         this.$router.push({name: 'login'})
        //     }
        // })
    }

}
</script>
