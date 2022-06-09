<template>
    <div class='warning'>
        <ul class='warning-body'>
            <li v-for="warn in this.$store.state.warnings.vals" :key="warn">
                {{warn}}
            </li>
        </ul>  
    </div>
</template>

<script>

</script>

<style lang="scss" scoped>
.warning {
    background: red;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 3000;
    &-body {
        width: 40rem;
        margin: 0 auto;
        padding: 1rem 0;
    }
}
</style>
