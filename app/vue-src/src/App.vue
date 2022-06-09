<template>
<transition appear name="show-anim">
    <div class='w-background'>
        <FieldWarning v-if='this.$store.state.warnings.show'/>
        <div class='w-wrapper'>
            <PayForm v-if='this.$store.state.mainData'/>
        </div>
    </div>
</transition>
</template>

<script>
import PayForm from '@/components/PayForm'
import FieldWarning from '@/components/FieldWarning'
export default {
  components: {
    PayForm,
    FieldWarning
  },
  mounted(){
    console.log(this.$store.state)
  }
}
</script>

<style lang="scss">
@import "@/scss_variables/variables";
* {
    margin: 0;
    box-sizing: border-box;
}
h2 {
  font-size: 2rem;
}
p, span, label {
  font-size: 1.5rem;
}
body {
  background-color: $background;
  font-style: $main-font;
}
.w-background {
    min-height: 100vh;
    width: 100%;
    background-color: rgba($color: #000000, $alpha: 0.7);
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;
}
.w-wrapper {
    height: min-content;
    width: 47.5rem;
    overflow: hidden;
    padding: 0 2rem;
}
.error {
    border: 2px solid #d56262 !important;
}
.readyButton {
    background-color: rgb(0, 89, 255) !important;
    cursor: pointer;
}
html{
    @media (max-width: 765px){
        font-size: 14px;        
    }
    @media (max-width: 680px){
        font-size: 12px;        
    }
    @media (max-width: 580px){
        font-size: 10px;
        h2 {
          font-size: 2.5rem;
        }
        p, label, input, button {
          font-size: 1.5rem !important;
        }
        input {
          height: 4rem !important;
        }
        .w-wrapper {
          overflow: visible;
        }
    }
    @media (max-width: 480px){
        font-size: 8px;        
    }
    @media (max-width: 380px){
        .w-wrapper {
          width: 40rem;
        }        
    }
}

</style>
