<template>
    <div class='w-body-string'>
        <b-button v-on:click="this.select_me()" variant='primary'>{{this.form.name}}</b-button>
    </div>
</template>

<script>
export default {
    props: {
        form: Object,
    },
    methods: {
        select_me() {
            let json = JSON.stringify({'payform':this.form.form, 'webkassa_shop': this.$store.state.payParams.webkassa_shop.value});
            this.$store.commit('MERCHANT_TYPE', this.form.type);
            this.$store.dispatch('act_SELECT_FORM', json);
        }
    },
}
</script>

<style lang="scss" scoped>
    button {
        width: 100%;
        height: 3rem;
    }
</style>