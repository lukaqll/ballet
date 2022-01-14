<template>
    
    <admin-base>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <b-card class="border-0 shadow-sm text-center" title="Por onde nos conheceram?">
                    <report-chart :chartdata="datacollection" :options="chartOptions"></report-chart>
                    
                </b-card>
            </div>
        </div>
    </admin-base>
</template>
<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'
import ReportChart from '../../../components/ReportChart'
import palette from 'google-palette'

export default {
    components: {AdminBase, ReportChart},

    mounted: function() {
        this.getReport()
    },

    computed: {
        datacollection: function(){
            const labels = this.reportData.map( i => i.name )
            const data = this.reportData.map( i => i.count )
            return  {
                        labels, 
                        datasets: [{
                            data,
                            backgroundColor: palette('tol', data.length).map( hex =>('#' + hex)  )  
                        }]
                    }
        }
    },
    data: () => ({
        reportData: [],
        chartOptions: {
        }
    }),
    methods: {
        getReport(){
            common.request({
                url: '/api/reports/know-by',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (data) => {
                    this.reportData = data
                }
            })
        },
    },



}
</script>