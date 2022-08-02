<template>

    <div>
        <div v-if="base != 'site-base'">
            <component :is="base" >
                <router-view></router-view>
            </component>
        </div>
        <div v-else>
            <router-view></router-view>
        </div>

    </div>
</template>
<script>

import SiteBase from './components/SiteBase/index.vue'
import CommomHeader from './components/CommomHeader/index.vue'
import AdminBase from './components/AdminBase/index.vue'
import DashboardBase from './components/DashboardBase/index.vue'

export default {
    components: {SiteBase, CommomHeader, AdminBase, DashboardBase},
    
    computed: {
        base(){
            return this.$route.meta.base || 'commom-header'
        }
    }
}
</script>