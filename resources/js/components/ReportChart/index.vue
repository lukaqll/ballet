<script>
import { Doughnut } from 'vue-chartjs'

export default {
  extends: Doughnut,
  props: ['chartdata', 'options', 'styles'],
  watch: {
      chartdata: function(chartdata){
        this.renderChart(chartdata, this.options)
      }
  },
}
</script>