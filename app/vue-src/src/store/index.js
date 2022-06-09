import { createStore } from 'vuex'
import { Func } from '../functions.js'

export default createStore({
  state: {
    mainData: data.mainData ? data.mainData : {},
    devDefault: devDefaultForm,
    confirm: true,
    inProgress: false,
    bc_type: false,
    error: data.error ? data.error : false,
    warnings: {
      show: false,
      vals: {}
    },
    allReady: false,
    payResults: data.payResults ? data.payResults : false,
    payParams: data.payParams ? data.payParams : false,
    payFields: data.payFields ? data.payFields.map((e) => {
      if (e.num_field == 1) {
        e.sValue = data.mainData.summ;
        e.isReady = true;
      } else if (e.num_field == 6) {
        e.isReady = true;
        e.sValue = '';
      } else {
        e.sValue = '';
        e.isReady = false;
      }
      return e;
    }): false,
  },
  mutations: {
    CONFIRM_CHECKBOX: (state) => {
      state.confirm = !state.confirm;
    },
    SET_WARNING: (state, n) => {
      state.warnings.vals[n[0]] = n[1];
    },
    SHOW_WARNINGS: (state, n) => {
      state.warnings.show = n;
    },
    RM_WARNING: (state, n) => {
      delete state.warnings.vals[n];
    },
    ALL_READY: (state, n) => {
      state.allReady = n;
    },
    PAYFORM: (state, n) => {
      state.mainData.payform = n;
      state.payParams['form'] = {name: 'form', value: n}
    },
    ERROR: (state, n) => {
      state.error = n;
    },
    MERCHANT_TYPE: (state, n) => {
      state.mainData.merchantType = n;
    },
    PAY_FIELDS: (state, n) => {
      state.payFields = n;
    },
    PAY_PARAMS: (state, n) => {
      state.payParams = n;
    },
    PAY_FIELD_CHANGE: (state, n) => {
      state.payFields[n[0]].sValue = n[1];
    },
    PAY_FIELD_ISREADY: (state, n) => {
      state.payFields[n[0]].isReady = n[1];
    },
    IN_PROGRESS: (state) => {
      state.inProgress = !state.inProgress;
    },
    PAY_RESULTS: (state, n) => {
      state.payResults = n;
    },
    BC_TYPE: (state, n) => {
      state.bc_type = n;
    }
  },
  actions: {
    act_SELECT_FORM: (context, payload) => {
      context.commit('IN_PROGRESS');
      fetch('ajax_php/get_form_fields.php', {
        method: 'POST',
        headers: {
          "Content-type": "application/json",
        },
        body: payload,
      }).then((resp) => {
        if (resp.ok) {
          return resp.json();
        } else {
          return {result: resp.status, result_text: `HTTP CODE: ${resp.status} ${resp.statusText}` };
        }
      })
      .then((json) => {
        if (json.result == '0') {
          json.table.colvalues.forEach((e, i) => {
            if (e.type_field == 'AMOUNT') {
              json.table.colvalues[i].sValue = context.state.mainData.summ;
              json.table.colvalues[i].isReady = true;
            } else if (e.form == context.state.devDefault && e.num_field == '6') {
              json.table.colvalues[i].sValue = '';
              json.table.colvalues[i].isReady = true;
            } else {
              json.table.colvalues[i].sValue = '';
              json.table.colvalues[i].isReady = false;
            }
          });
          context.commit('IN_PROGRESS');
          context.commit('PAYFORM', json.table.colvalues['0'].form);
          context.commit('PAY_FIELDS', json.table.colvalues);
        } else {
          context.commit('IN_PROGRESS');
          context.commit('MERCHANT_TYPE', false);
          context.commit('ERROR', {result: json.result, result_text: json.result_text, full_result: json.full_result });
        }
      }).catch((err) => {
        context.commit('IN_PROGRESS');
        context.commit('ERROR', {result: '103', result_text: 'Неизвестная ошибка', full_result: err });
      });
    },

    act_PAY: (context, payload) => {
      context.commit('IN_PROGRESS');
      fetch(payload.url, {
        method: 'POST',
        headers: {
            "Content-type": "application/json",
        },
        body: payload.json,
    }).then((resp) => {
      if (resp.ok) {
        return resp.json();
      } else {
        return {result: resp.status, result_text: `HTTP CODE: ${resp.status} ${resp.statusText}` };
      }
    })
    .then((json) => {
        if (json.result == 0 && !json.table.colvalues ) {
          let res = {
            result: json.result,
            result_text: json.result_text,
            ext_transact: json.ext_transact,
            free_param: context.state.mainData.free_param
          }
          context.commit('PAYFORM', false);
          context.commit('PAY_RESULTS', res);
          context.commit('IN_PROGRESS');
        } else if (json.result == 0 && json.table.colvalues) {
          Func.send3ds(json.table.colvalues);
        } else {
          context.commit('IN_PROGRESS');
          context.commit('ERROR', {result: json.result, result_text: json.result_text, full_result: json.full_result });
        }
    }).catch((err) => {
        context.commit('IN_PROGRESS');
        context.commit('ERROR', {result: '103', result_text: 'Неизвестная ошибка', full_result: err });
        });
    },
    act_WARNING: (context, payload) => {
      if (payload.commit == 'SET_WARNING') {
        context.commit('SET_WARNING', payload.warn);
        // context.commit('SHOW_WARNINGS', true)
      } else if (payload.commit == 'RM_WARNING') {
        context.commit('RM_WARNING', payload.warn);
        // if (Object.keys(context.state.warnings.vals).length == 0) {
        //   context.commit('SHOW_WARNINGS', false);
        // }
      }
    }

  },
  getters: {
    isReady(state) {
      let tmp = [];
      state.payFields.forEach(field => {
        if (field.isReady) {
          tmp.push(field['num_field']);
        }
      });
      if (tmp.length == state.payFields.length && state.confirm) {
        return true;
      } else {
        return false;
      }
    }
  },
  modules: {
  }
})
