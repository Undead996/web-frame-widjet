<template>
    <div>
        <div class="w-body-string">
            <p>{{this.$store.state.error.result_text}}</p>
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
    let why = 'fail';
    let timerId = setInterval(() => {
        this.time = this.time - 1;
        if (this.time < 1) {
            clearInterval(timerId);
            let payload =  {result_code: this.$store.state.error.result, result_text: this.$store.state.error.result_text, full_result: this.$store.state.error.full_result};
            Func.exit(payload);
        }
    }, 1000);
  }
}
</script>