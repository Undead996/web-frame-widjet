<template>
    <div class='template'>
        <div class='template-string'>
            <div class='template-string-content'>
                <p class='t-hint'>Номер карты</p>
                <CardTemplateInput :id='"1"' 
                                   :icon='true' 
                                   :type='"text"'  
                                   :placeholder='"CARD NUMBER"' 
                                   :validator="(val, store) => this.cardNumberListener(val, store)" 
                                   :showRule="(val) => this.showCardNumber(val)"/>
            </div>
        </div>
        <div class="template-string mob-between">
            <div class='template-string date '>
                <div class='template-string-content small'>
                    <p class='t-hint'>ММ</p>
                    <div class='input-wrapper'>
                        <CardTemplateInput :id='"2"' 
                                           :validator="(val, store) => this.mounthListener(val, store)" 
                                           :type='"text"' 
                                           :placeholder='"M"'/>
                    </div>
                </div>
                <p class='slash'>/</p>   
                <div class='template-string-content small'>
                    <p class='t-hint'>ГГ</p>
                    <div class='input-wrapper'>
                        <CardTemplateInput :id='"3"' 
                                           :validator="(val, store) => this.yearListener(val, store)" 
                                           :type='"text"' 
                                           :placeholder='"Y"'/>
                    </div>
                </div>
            </div>
            <div class='template-string-content cvc'>
                <p class='t-hint'>CVC</p>
                <div class='input-wrapper'>
                    <CardTemplateInput :id='"4"' 
                                       :validator="(val, store) => this.cvcListener(val, store)" 
                                       :type='"text"' 
                                       :placeholder='"CVC"'/>
                </div>
            </div>
        </div>
        <div class="template-string">
            <div class='template-string-content'>
                <p class='t-hint'>Владелец</p>
                <div class='input-wrapper'>
                    <CardTemplateInput :id='false' :type='"text"' :placeholder='"CARD HOLDER"'/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CardTemplateInput from '@/components/CardTemplateInput'
import { Func } from '../functions.js'

export default {
    components: {
        CardTemplateInput,
    },
    methods: {
        cardNumberListener(val, store) {
            val = val.replace(/\D+/g, '');
            let isReady = false;
            if (val.length >=1) {
                store.commit('BC_TYPE', Func.getCardType(val));
            }
            if (val.length >= 16) {
                if (Func.luhnAlgorithm(val)) {
                    isReady = true;
                    store.dispatch('act_WARNING', {commit: 'RM_WARNING', warn: '1'});
                } else {
                    isReady = false;
                    store.dispatch('act_WARNING', {commit: 'SET_WARNING', warn: ['1', 'Проверьте правильность номера карты']});
                    console.log(store.state.warnings);
                }
            } else if ( val.length == 0) {
                store.commit('BC_TYPE', Func.getCardType(false));
                store.dispatch('act_WARNING', {commit: 'RM_WARNING', warn: '1'});
                isReady = false;
            }
            val = val ? val : false;
            return [val, isReady];
        },
        showCardNumber(val) {
            return Func.showCardNumber(val);
        },
        mounthListener(val, store) {
            val = val.replace(/\D+/g, '');
            let isReady = false; 
                if (Number(val) > 0 && Number(val) < 13 && val.length == 2) {
                    isReady = true;
                    store.dispatch('act_WARNING', {commit: 'RM_WARNING', warn: '2'});
                } else if ( val.length == 0)  {
                    store.dispatch('act_WARNING', {commit: 'RM_WARNING', warn: '2'});
                    isReady = false;
                } else {
                    isReady = false;
                    store.dispatch('act_WARNING', {commit: 'SET_WARNING', warn: ['2', 'Введите месяц срока действия карты']});
                    console.log(store.state.warnings);
                }
            return [val, isReady];
        },
        yearListener(val, store) {
            val = val.replace(/\D+/g, '');
            let isReady = false; 
            if (val.length === 2) {
                isReady = true;
            }
            return [val, isReady];
        },
        cvcListener(val, store) {
            val = val.replace(/\D+/g, '');
            let isReady = false; 
            if (val.length === 3) {
                isReady = true;
                store.dispatch('act_WARNING', {commit: 'RM_WARNING', warn: '4'});
            } else if (val.length == 0) {
                store.dispatch('act_WARNING', {commit: 'RM_WARNING', warn: '4'});
                isReady = false; 
            } else {
                store.dispatch('act_WARNING', {commit: 'SET_WARNING', warn: ['4', 'CVC - трехзначный код с обратной строрны карты']});
                console.log(store.state.warnings);
                isReady = false; 
            }
            return [val, isReady];
        }
    }
}
</script>

<style lang="scss">
    .template {
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        justify-content: space-between;
        height: 22rem;
        background-color: #FFFFFF;
        padding: 1.5rem 1.7rem 2rem 1.7rem;
        border-radius: 12px;
        .input-wrapper {
            position: relative;
            .warning-hint {
                color: red;
                font-size: 0.8rem;
                position: absolute;
                width: 35rem;
            }
            .cardInputWarn {
                border: solid red;
            }
        }
        .bc_icon {
            height: 3.5rem;
            width: 8.5rem;
            margin-bottom: 0;
            position: absolute;
            right: 0.5rem;
            background-size: 4.5rem;
            background-repeat: no-repeat;
            background-position: top 50% right 20px;
            // background-image: url('~@/assets/img/ps_cards/visa.svg');
        }
        .slash {
            color: black;
            margin-bottom: 0;
            padding-top: 15%;
            font-size: 2rem !important;
            vertical-align: baseline;
        }
        .small {
            width: 4rem;
        }
        .cvc {
            width: 5rem;
            margin-left: 3.5rem;
        }
        .date {
            width: 11.5rem;
            justify-content: space-between;
            padding-bottom: 0 !important;
        }
        .t-hint {
            font-size: 1.3rem;
            margin: 0;
            color: #C4C4C4;
        }
        &-string {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            &-content {
                width: 100%;
                input {
                    height: 3.5rem;
                    background: #F4F4F4;
                    border-radius: 6px;
                    width: 100%;
                    padding: 0 1.2rem;
                }
            }
        }
    }
    @media (max-width: 580px){
        .template {
            height: 100%;
            &-string:not(:last-child) {
                padding-bottom: 2rem;
            }
            .bc_icon {
                height: 4rem;
            }
            .mob-between {
                justify-content: space-between;
                .slash {
                    padding-top: 22%;
                }
                .small {
                    width: 5rem;
                }
                .cvc {
                    width: 7rem;
                }
            }
        }
    }
</style>