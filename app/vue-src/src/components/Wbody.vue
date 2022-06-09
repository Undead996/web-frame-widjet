<template>
    <CardTemplate v-if="this.$store.state.mainData.payform === this.$store.state.devDefault"/>
    <div class='w-body' v-else-if="this.$store.state.mainData.payform == false">
        <FormSelector v-bind:form="form" v-for="form in this.$store.state.mainData.forms" :key="form.form"/>
    </div>
    <PayFields class='w-body' v-else-if="this.$store.state.mainData.payform && this.$store.state.payFields"/>
    <PayResult class='w-body' v-else-if='this.$store.state.payResults'/>
</template>

<script>
import CardTemplate from '@/components/CardTemplate'
import PayFields from '@/components/PayFields'
import FormSelector from '@/components/FormSelector'
import PayResult from '@/components/PayResult'


export default {
components: {
    FormSelector,
    CardTemplate,
    PayFields,
    PayResult
}

}
</script>

<style lang="scss">
    .w-body {
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        &-string {
            width: 100%;
            margin: 1rem auto;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: space-between;
            #mounth {
                border-right: solid gray 1px;
            }
            input {
                height: 2.5rem;
            }
            .left {
                width: 22rem;
            }
            .right {
                width: 12rem;
                display: flex;
                flex-direction: row;
                input {
                    width: 6rem;
                }
            }
            .field_label {
                max-width: 33%;
            }
            .field_input {
                width: 63%;
            }
        }
    }
        @media (max-width: 480px){
        .w-body-string {
            flex-direction: column;
            margin: 1rem 0;
            align-items: flex-start;
            input {
                margin: 1rem 0;
            }
            .right {
                width: 16rem;
                input {
                    width: 8rem;
                }
            }
            .left {
                width: 100% !important;
            }
            .field_label {
                max-width: 100%;
            }
            .field_input {
                width: 100%;
            }
        }
        .w-body-middlestring {
            flex-direction: column !important;
            #cvc {
                margin: 1rem 0;
            }
            #cardHolder {
                width: 100% !important;
            }
        }
    }
</style>