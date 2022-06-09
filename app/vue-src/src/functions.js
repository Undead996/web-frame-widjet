var binmap = {
	cardTypes: {
		VISA: "visa",
		MAESTRO: "maestro",
		MASTERCARD: "mastercard",
		MIR: "mir",
		AMEX: "americanexpress",
		DC: "dinnersclub",
		JCB: "jcb"
	},
	
};

const Func = {
    send3ds(json) {
        let tmp = {};
        json.forEach(e => {
            tmp[e.param] = e.value;
        });
        let form = document.createElement('FORM');
        form.action = tmp.merchant_url;
        form.method = tmp.merchant_method;
        let keys = Object.keys(tmp); 
        for (let i = 0; i < keys.length; i++) {
            let input = document.createElement('INPUT');
            input.type = "hidden";
            input.name = keys[i];
            input.value = tmp[keys[i]];
            form.appendChild(input);
        }
        document.body.append(form);
        // console.log(form);
        form.submit();
    },
    exit(payload) {
        let msg = JSON.stringify({command: 'close', result: {'result_code': payload.result_code, 
                                                             'result_text': payload.result_text, 
                                                             'full_result': payload.full_result}
                                });
        parent.postMessage(msg, '*');
    },
    luhnAlgorithm(value) {
        value = value.replace(/\D/g, '');
        let nCheck = 0;
        let bEven = false;
        for (let n = value.length - 1; n >= 0; n--) {
            let nDigit = parseInt(value.charAt(n), 10);
            if (bEven && (nDigit *= 2) > 9) {
                nDigit -= 9;
            }
            nCheck += nDigit;
            bEven = !bEven;
        }
        return nCheck % 10 === 0;
    },
    showCardNumber(str) {
        let number = undefined;
        if (str.length <= 4) {
          number = `${str.slice(0, 4)}`;
        }
        if (str.length > 4 && str.length < 9) {
          number = `${str.slice(0, 4)} ${str.slice(4, 8)}`;
        }
        if (str.length > 8 && str.length < 13) {
          number = `${str.slice(0, 4)} ${str.slice(4, 8)} ${str.slice(8, 12)}`;
        }
        if (str.length > 12 && str.length < 17) {
          number =  `${str.slice(0, 4)} ${str.slice(4, 8)} ${str.slice(8, 12)} ${str.slice(12, 16)}`;
        }
        if (str.length > 16) {
          number = `${str.slice(0, 4)} ${str.slice(4, 8)} ${str.slice(8, 12)} ${str.slice(12, 16)} ${str.slice(16, 19)}`;
        }
        return number;
    },
    getCardType(e) {
      var t = /^220[0-4]\s?\d\d/;
  
      switch (e[0]) {
          case "2":
              return t.test(e) ? binmap.cardTypes.MIR : "";
          case "3":
              var n = e[1] || "";
              return "7" === n ? binmap.cardTypes.AMEX : "5" === n ? binmap.cardTypes.JCB : n ? binmap.cardTypes.DC : "";
          case "4":
              return binmap.cardTypes.VISA;
          case "5":
              var a = e[1] || "";
              return "0" === a || a > "5" ? binmap.cardTypes.MAESTRO : binmap.cardTypes.MASTERCARD;
          case "6":
              return binmap.cardTypes.MAESTRO;
          default:
              return ""
      }
    }
}

export {Func};
