<template>
    <div class='w-footer'>
        <div class='w-footer-button'>
            <b-button variant='primary' :class="{ disabled: !this.$store.state.allReady }" v-on:click="this.pay">ОПЛАТИТЬ {{this.$store.state.mainData.summ}} RUB</b-button>
        </div>
    </div>
</template>

<script>

export default {
    methods: {
        getPayload() {
            let payload = {
                payParams: this.$store.state.payParams,
                payFields: this.$store.state.payFields,
                merchantType: this.$store.state.mainData.merchantType
            }
            return JSON.stringify(payload);
        },
        pay() {
            this.$store.dispatch('act_PAY', {'url':'/ajax_php/send_merchant.php', 'json':this.getPayload()});
        }
    },
    created() {
        this.unwatch = this.$store.watch(
            (state, getters) => getters.isReady,
            (newS, old) => {
                if (newS) {
                    this.$store.commit('ALL_READY', true);
                } else {
                    this.$store.commit('ALL_READY', false);
                }
            }
        )
    },
}
</script>

<style lang="scss">
.w-footer {
    display: flex;
    flex-direction: column;
    margin-top: 2rem;
    &-check {
        cursor: pointer;
        display: flex;
        flex-direction: row;
        align-items: center;
        .ch-box {
            padding: 0 !important;
        }
        input {
            cursor: pointer;
            margin: 0.5rem !important;
        }
    }
    &-button {
        button {
            width: 100%;
            height: 4.5rem;
            font-size: 1.5rem;
            padding: 0 !important;
            border-radius: 12px;
        }
    }
}
</style>