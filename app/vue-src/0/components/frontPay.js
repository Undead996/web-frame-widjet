class SingleError {
    constructor(message, type) {
      this.message = message;
      this.type = type;
    }
    status = false;
    errorContainer = undefined;
  }
  
  class ErrorHandler {
    action(e, error) {
      if (error.status === false) {
        error.status = true;
        error.errorContainer = document.createElement('DIV');
        // error.errorContainer.className = 'errorField';
        // error.errorContainer.classList.add(error.type);
        // error.errorContainer.color = color;
        // error.errorContainer.innerHTML = error.message;
        e.target.classList.add('error');
        // e.target.parentNode.append(error.errorContainer);
      }
      if (error.status === true) {
        return;
      }
    }
    // Убирает ерор
    disAction(e, error) {
      if (error.errorContainer === undefined) {
        return;
      }
    //   error.errorContainer.remove();
      e.target.classList.remove('error');
      error.errorContainer = undefined;
      error.status = false;
    }
  
    cardNumberLengthError = new SingleError('Укажите минимум 16 символов', 'card');
    moonCheckError = new SingleError('Проверьте правильность номера карты', 'card');
    monthError = new SingleError('Укажите месяц от 01 до 12', 'card');
    yearError = new SingleError('Укажите минимум 2 цифры', 'card');
    cardDateError = new SingleError('Введенная дата уже прошла', 'card');
    cvcError = new SingleError('Укажите минимум 3 цифры', 'card');
  }


class WidgetScript {
    constructor(cardNumber, mounth, year, cardHolder, cvc, summ, check, close, mainbtn) {
        this.cardNumber = cardNumber;
        this.mounth = mounth;
        this.year = year;
        this.cardHolder = cardHolder;
        this.cvc = cvc;
        this.summ = summ;
        this.check = check;
        this.close_btn = close;
        this.mainbtn = mainbtn;
        
        this.cardNumberListener = this.cardNumberListener.bind(this);
        this.mounthListener = this.mounthListener.bind(this);
        this.yearListener = this.yearListener.bind(this);
        this.cardHolderListener = this.cardHolderListener.bind(this);
        this.cvcListener = this.cvcListener.bind(this);
        this.checkListener = this.checkListener.bind(this);
        this.isReady = this.isReady.bind(this);
        this.closeListener = this.closeListener.bind(this);
        this.payListener = this.payListener.bind(this);
        
        this.setEventListeners();
    }
    card = {
        value: '',
        status: false,
        // bank: undefined,
        // errColor: bankData.default.errorColor,
    };
    cardHolder = {
        value: '',
        status: false,
    };
    date = {
        month: NaN,
        year: NaN,
        status: false,
    };
    cvc = {
        value: null,
        status: false,
    };
    checkBox = {
        status: false,
    };
    ready = false;
    data = {};
    errors = new ErrorHandler();
    getRealMonth() {
        let realDate = new Date();
        let realMonth = realDate.getMonth();
        return realMonth;
    }
    getRealYear() {
        let realDate = new Date();
        let realYear = realDate.getFullYear().toString().substr(2, 2);
        return realYear;
    }
    checkRegExpMonthInput(v) {
        const regExp = /^([0]|[1])\d$/g;
        if (!regExp.test(v) || v.length > 2) {
          return false;
        } else {
          return true;
        }
    }
    checkRegExpYearInput(v) {
        const regExp = /^\d+$/g;
        if (!regExp.test(v) || v.length < 2) {
          return false;
        } else {
          return true;
        }
    }
    checkDate(e) {
        if (e.target.id === 'mounth') {
          this.date.status = false;
          this.date.month = e.target.value;
          if (!this.errors.yearError.status) {
            if (!this.checkRegExpMonthInput(this.date.month)) {
              this.errors.disAction(e, this.errors.cardDateError);
              this.errors.action(e, this.errors.monthError);
              return;
            } else {
              this.errors.disAction(e, this.errors.monthError);
              this.year.focus();
            }
          } else {
            this.errors.disAction(e, this.errors.yearError);
            if (!this.checkRegExpMonthInput(this.date.month)) {
              this.errors.disAction(e, this.errors.cardDateError);
              this.errors.action(e, this.errors.monthError);
              return;
            } else {
              this.errors.disAction(e, this.errors.monthError);
              this.date.status = true;
              this.year.focus();
            }
          }
        }
        if (e.target.id === 'year') {
          this.date.status = false;
          this.date.year = e.target.value;
          if (!this.checkRegExpYearInput(this.date.year)) {
            if (!this.errors.monthError.status) {
              this.errors.disAction(e, this.errors.cardDateError);
              this.errors.action(e, this.errors.yearError);
            }
            return;
          } else {
            this.errors.disAction(e, this.errors.yearError);
            this.date.status = true;
            this.cvc.focus();
          }
        }
        if (
          this.date.month.length === 2 &&
          this.date.month <= 12 &&
          this.date.year.length === 2
        ) {
          let rY = +this.getRealYear();
          if (rY > +this.date.year) {
            this.date.status = false;
            this.errors.action(e, this.errors.cardDateError);
          }
          if (rY < +this.date.year) {
            this.errors.disAction(e, this.errors.cardDateError);
          }
          if (rY === +this.date.year) {
            let rM = +this.getRealMonth();
            if (rM < +this.date.month) {
              this.errors.disAction(e, this.errors.cardDateError);
            } else {
              this.date.status = false;
              this.errors.action(e, this.errors.cardDateError);
            }
          }
        }
    }

    luhnAlgorithm(value) {
        value = value.replace(/\D/g, '');
    
        var nCheck = 0;
        var bEven = false;
    
        for (var n = value.length - 1; n >= 0; n--) {
          var nDigit = parseInt(value.charAt(n), 10);
    
          if (bEven && (nDigit *= 2) > 9) {
            nDigit -= 9;
          }
    
          nCheck += nDigit;
          bEven = !bEven;
        }
    
        return nCheck % 10 === 0;
    }
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
    setCardType(target, type) {
      if (target.value.length<2) {
        target.style.backgroundImage = 'none';
      } else {
        target.style.backgroundImage = `url('./img/ps_cards/${type}.svg')`;
      }
    }
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
    }
    isReady() {
        if (
          this.checkState(
            this.card,
            this.cardHolder,
            this.date,
            this.cvc,
            this.checkBox
          )
        ) {
          this.ready = true;
          this.mainbtn.addEventListener('click', this.payListener);
          this.mainbtn.classList.add('readyButton');
        } else {
          this.ready = true;
          this.mainbtn.addEventListener('click', this.payListener);
          this.mainbtn.classList.remove('readyButton');
        }
    }
    checkState(card, cardHolder, date, cvc, check) {
        if (
          card.status &&
          cardHolder.status &&
          date.status &&
          cvc.status &&
          check.status
        ) {
          return true;
        } else {
          return false;
        }
    }
    setData() {
        this.data = {
          data: {
            card: this.card.value,
            cardHolder: this.cardHolder.value,
            date: {
              month: this.date.month,
              year: this.date.year,
            },
            cvc: this.cvc.value,
            summ: this.summ.value,
          },
        };
        console.log(this.data);
    }


    cardNumberListener(e) {
        e.target.value = e.target.value.replace(/\D+/g, '');
        this.card.status = false;
        this.card.value = e.target.value;
        e.target.value = this.showCardNumber(this.card.value);
        if (this.card.value) {
          this.card.sys = this.getCardType(this.card.value);
          this.setCardType(e.target, this.card.sys);
        }
        if (this.card.value.length <= 16) {
          this.errors.disAction(e, this.errors.moonCheckError);
          this.errors.action(e, this.errors.cardNumberLengthError);
        }
        if (this.card.value.length >= 16) {
          this.errors.disAction(e, this.errors.cardNumberLengthError);
          this.luhnAlgorithm(this.card.value)
            ? this.card.status = true
            : this.errors.action(e, this.errors.moonCheckError);
        }
    }
    mounthListener(e) {
        e.target.value = e.target.value.replace(/\D+/g, '');
        this.checkDate(e);
    }
    yearListener(e) {
        e.target.value = e.target.value.replace(/\D+/g, '');
        this.checkDate(e);
    }
    cardHolderListener(e) {
        this.cardHolder.value = e.target.value;
        this.cardHolder.status = true;
    }
    cvcListener(e) {
        e.target.value = e.target.value.replace(/\D+/g, '');
        this.cvc.value = e.target.value;
        this.errors.action(e, this.errors.cvcError);
        this.cvc.status = false;
        if (this.cvc.value.length === 3) {
          this.errors.disAction(e, this.errors.cvcError);
          this.cvc.status = true;
          this.cardHolder.focus();
        }
    }
    checkListener() {
        this.checkBox.status = !this.checkBox.status;
        this.isReady();
    }
    closeListener() {
        this.exit('user_close', '1', 'user go back');
    }

    payListener() {
        this.setData();
        this.exit('success', '0', 'pay success');
    }
    
    exit(why, result_code, result_text) {
        let msg = JSON.stringify({why: why, command: 'close', result: [result_code, result_text]});
        parent.postMessage(msg, '*');
    }
    setEventListeners() {
        window.addEventListener('keyup', this.isReady);
        this.cardNumber.addEventListener('keyup', this.cardNumberListener);
        this.mounth.addEventListener('keyup', this.mounthListener);
        this.year.addEventListener('keyup', this.yearListener);
        this.cardHolder.addEventListener('keyup', this.cardHolderListener);
        this.cvc.addEventListener('keyup', this.cvcListener);
        this.check.addEventListener('change', this.checkListener);
        this.close_btn.addEventListener('click', this.closeListener);
    }
}