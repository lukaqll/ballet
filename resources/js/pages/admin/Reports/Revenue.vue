<template>
    
    <admin-base>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <b-card class="border-0 shadow-sm text-center" title="Faturamento Mensal">
                    <line-chart :styles="lineStyle" :chartdata="dataByMonth" :options="lineOptions"></line-chart>
                    
                </b-card>
            </div>
        </div>
    </admin-base>
</template>
<script>
import common from '../../../common/common'
import AdminBase from '../../../components/AdminBase'
import LineChart from '../../../components/ReportChart/line.vue'
import palette from 'google-palette'

export default {
    components: {AdminBase, LineChart},

    mounted: function() {
        this.getReport()
    },

    computed: {
        dataByMonth: function(){

            if( !this.reportData.by_month ){
                return false
            }
            const repData = this.reportData.by_month
            const labels = repData.map( i => `${i.month} \n R$ ${common.toMoney(i.value)}` )
            const data = repData.map( i => i.value )
            return  {
                        labels, 
                        datasets: [{
                            label: 'Faturamento',
                            data,
                            backgroundColor: 'lightgreen'
                        }]
                    }
        }
    },
    data: () => ({
        reportData: {},
        lineOptions: {
            style: {
                height: '50px'
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [
                    {
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            },
        },
        lineStyle: {
            height: 200
        }
    }),
    methods: {
        getReport(){
            common.request({
                url: '/api/reports/revenue',
                type: 'get',
                auth: true,
                setError: true,
                load: true,
                success: (data) => {
                    console.log(data)
                    this.reportData = data
                }
            })
        },
    },



}
</script>