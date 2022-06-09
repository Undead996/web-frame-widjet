<template>
    <div>
        <div class="w-body-string">
            <p>{{this.$store.state.payResults.result_text}}</p>
        </div>
        <div class="w-body-string">
            <p>Возврат в магазин через: {{this.time}}</p>
        </div>
    </div>
</template>

<script>
import { Func } from '../functions.js'
export default {
  data() {
      return {
          time: 5,
      }
  },
  mounted(){
    let why = this.$store.state.payResults.result ? 'fail' : 'success';
    let timerId = setInterval(() => {
        this.time = this.time - 1;
        if (this.time < 1) {
            clearInterval(timerId);
            let payload =   {result_code: this.$store.state.payResults.result, result_text: this.$store.state.payResults.result_text, full_result: this.$store.state.payResults};
            Func.exit(payload);
        }
    }, 1000);
  }
}
</script>