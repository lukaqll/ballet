<script>
import { Line } from 'vue-chartjs'

export default {
  extends: Line,
  props: ['chartdata', 'options'],
  watch: {
      chartdata: function(chartdata){
        this.renderChart(chartdata, this.options)
      }
  },
}
</script>