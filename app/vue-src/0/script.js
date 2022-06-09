window.onload = function() {
    let cardNumber = document.querySelector('#cardNumber');
    let mounth = document.querySelector('#mounth');
    let year = document.querySelector('#year');
    let cardHolder = document.querySelector('#cardHolder');
    let cvc = document.querySelector('#cvc');
    let summ = document.querySelector('#summ');
    let check = document.querySelector('#check');
    let close = document.querySelector('#close');
    let mainbtn = document.querySelector('#mainbtn');
    
    new WidgetScript(cardNumber, mounth, year, cardHolder, cvc, summ, check, close, mainbtn);
}