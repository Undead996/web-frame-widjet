<?php
require './inc.php';
if (defined('TEST_POST_PARAM')) {
    $_POST['pay_params'] = TEST_POST_PARAM;
}

if (isset($_GET['result']) && isset($_GET['result_text'])) {
    $data = ['payResults' => ['result' => $_GET['result'],
                              'result_text' => $_GET['result_text'],
                              'ext_transact' => $_GET['ext_transact'],
                              'success' => $_GET['success'],
                              'free_param' => $_GET['free_param'],
                             ]
            ];
} elseif (isset($_POST['pay_params'])) {
    $data = json_decode($_POST['pay_params'], true);
    $data = check_shop_param($data);
    $CMD = new Use_dkcp();
    if (($data['payform'] == ONLY_CARD_FORM && ONLY_CARD_FORM) || (!$data['payform'] && ONLY_CARD_FORM)) {
        list($res, $answer) = $CMD->DeltaCMD('get_form_fields', ['payform' => ONLY_CARD_FORM, 'webkassa_shop' => $data['num_shop']], 'dkcp/direct.py');
        $data['merchantType'] = '5';
        $data = set_default_data($data, $answer);
    } elseif ($data['payform']) {
        list($res, $answer) = $CMD->DeltaCMD('get_form_fields', ['payform' => $data['payform'], 'webkassa_shop' => $data['num_shop']], 'dkcp/direct.py');
        $data = set_default_data($data, $answer);
    } else {
        list($res, $answer) = $CMD->DeltaCMD('getlist_merchant', ['keyt_pay' => $data['keyt_shop'], 'webkassa_shop' => $data['num_shop']], 'dkcp/direct.py');
        $data['forms'] = $answer['table']['colvalues'];
        $data = ['mainData' => $data, 'payParams' => DEFAULT_PAY_PARAMS];
        $data['payParams']['free_param'] = ['name' => 'free_param', 'value' => $data['mainData']['free_param']];
        $data['payParams']['keyt_shop'] = ['name' => 'keyt_shop', 'value' => $data['mainData']['keyt_shop']];
        $data['payParams']['webkassa_shop'] = ['name' => 'webkassa_shop', 'value' => $data['mainData']['num_shop']];
    }
    
    if (ONLY_CARD_FORM) {
        $data['mainData']['defaultForm'] = ONLY_CARD_FORM;
    }
} else {
    // echo 'Error: wrong pay_params!';
    // die();
    $data['error'] = ['result' => '66', 'result_text' => 'wrong pay_params!', 'full_result' => false];
}



include './data_proxy.php'; 