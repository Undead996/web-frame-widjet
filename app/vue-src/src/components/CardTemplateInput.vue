<template>
    <div class='input-wrapper'>
        <p v-if="this.icon" class='bc_icon' :class='this.$store.state.bc_type'/>
        <InputField :id='this.id' 
                    :class='[this.class, this.$store.state.warnings.vals[this.id] ? "cardInputWarn" : ""]' 
                    :validator="(val, store) => this.validator(val, store)" 
                    :showRule="this.showRule ? (val) => this.showRule(val) : null" 
                    :type="this.type" 
                    :placeholder="this.placeholder"/>
        <p v-if="this.$store.state.warnings.vals[this.id]" class='warning-hint'>{{this.$store.state.warnings.vals[this.id]}}</p>
    </div>
</template>

<script>
import InputField from '@/components/InputField'

export default {
    components: {
        InputField
    },
    props: {
        id: [String, Boolean],
        class: [String, null],
        type: [String],
        placeholder: [String, null],
        icon: [Boolean, null],
        validator: [Function, null],
        showRule: [Function, null],
    },
}
</script>

<style lang="scss">

</style>