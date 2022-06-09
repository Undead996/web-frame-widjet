<template>
    <div>
        <div class="w-body-string" v-for="field in this.$store.state.payFields" :key="field.num_field">
            <label class='field_label'>{{field.rus_name}}: </label><InputField class='field_input' :id='String(field.num_field - 1)' type="text"/>
        </div>
    </div>
</template>

<script>
import InputField from '@/components/InputField'

export default {
    components: {
        InputField,
    },

}
</script>

<style lang="scss">

</style>