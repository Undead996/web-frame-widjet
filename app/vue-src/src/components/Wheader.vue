<template>
    <div class='w-header'>
        <h2>{{this.$store.state.mainData.comment}}</h2>
        <p v-on:click="this.exit({result_code: '101', result_text:'User go back', full_result: false})" class='close'></p>
    </div>
</template>

<script>
import { Func } from '../functions.js'
export default {
props: {
    listener: Function,
},
methods: {
    exit(payload) {
        Func.exit(payload);
    }
}

}
</script>

<style lang="scss">
.w-header {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: baseline;
    .close {
        cursor: pointer;
        height: 2rem;
        width: 2rem;
        background-image: url('~@/assets/img/cross.svg');
        background-size: cover;
        @media (max-width: 580px){
            position: absolute;
            top: 5%;
            right: 5%;
        }
    }
}
</style>