<template>
    <input v-if="this.id" v-model="val">
    <input v-else>
</template>

<script>
export default {
    props: {
        id: [String, Boolean],
        validator: [Function, null],
        showRule: [Function, null],
    },
    computed: {
        val: {
            get () {
                if (this.showRule) {
                    return this.showRule(this.$store.state.payFields[this.id].sValue);
                } else {
                    return this.$store.state.payFields[this.id].sValue;
                }
            },
            set (value) {
                if ( this.validator) {
                    this.$store.commit('PAY_FIELD_CHANGE', [this.id, '']);
                    value = this.validator(value, this.$store);
                    this.$store.commit('PAY_FIELD_ISREADY', [this.id, value[1]]);
                    this.$store.commit('PAY_FIELD_CHANGE', [this.id, value[0]]);
                } else {
                    this.$store.commit('PAY_FIELD_ISREADY', [this.id, true]);
                    this.$store.commit('PAY_FIELD_CHANGE', [this.id, value]);
                }
            }
        }
    },
}
</script>