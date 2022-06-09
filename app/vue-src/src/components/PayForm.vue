<template>
    <div v-if="this.$store.state.error" class='w-container'>
        <WError />
    </div>
    <b-spinner v-else-if="this.$store.state.inProgress" class='w-loader text-primary' label="Large Spinner"></b-spinner>
    <div v-else class='w-container'>
        <Wheader v-if='!this.$store.state.payResults'/>
        <Wbody/>
        <Wfooter v-if="this.$store.state.mainData.payform"/>
    </div>
</template>

<script>
import Wheader from '@/components/Wheader' 
import Wbody from '@/components/Wbody'
import Wfooter from '@/components/Wfooter' 
import WError from '@/components/WError' 

export default {
components: {
    Wheader,
    Wbody,
    Wfooter,
    WError,
}  
}
</script>

<style lang="scss">
    .w-loader {
        height: 8rem;
        width: 8rem;
        margin: auto;
        display: block;
    }
    .w-container {
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    // width: 43.5rem;
    color: white;
    justify-content: space-between;
    input {
        font-size: 1.5rem;
        border: none;
        outline: none;
    }
    @media (max-width: 480px){
        .w-container {
            height: 40rem;
        }
    }
}
</style>