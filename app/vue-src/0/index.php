<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./cardlib/binmap.js"></script>
    <script src='./components/frontPay.js'></script>
    <script src='./script.js'></script>
    <title>Document</title>
</head>
<body>
    <div class='w-background'>
        <div class='w-wrapper'>
            <div class='w-container'>
                <div class='w-header'>
                    <h2><?php echo $data['comment'] ?></h1>
                    <p id='close'>&#10006;</p>
                </div>
                <div class='w-body'>
                    <div class='w-body-topstring'>
                        <input id='cardNumber' type='text' placeholder="CARD NUMBER">
                        <div class='date'>
                            <input id='mounth' type="text" placeholder="M">
                            <input id='year' type="text" placeholder="Y">
                        </div>
                    </div>
                    <div class="w-body-middlestring">
                        <input id='cardHolder' type='text' placeholder="CARD HOLDER">
                        <input id='cvc' type='text' placeholder="CVC">
                    </div>
                    <div class="w-body-check">
                        <input type="checkbox" name="check" id="check">
                        <label for="check">Confirm something</label>
                    </div>
                    <div class='w-body-button'>
                        <input type="hidden" name='summ' id='summ' value='222'>
                        <button id='mainbtn'>PAY <?php echo $data['summ'] ?></button>
                    </div>
                </div>
                <div class='w-footer'>
                    <p>Something in footer</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>