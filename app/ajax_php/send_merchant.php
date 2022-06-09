<?php
require './inc.php';

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
$CMD = new Use_dkcp();
$resData = [];
foreach($data['payFields'] as $val) {
    $resData[$val['num_field']] = $val['sValue'];
}

$resData['webkassa_shop'] = $data['payParams']['webkassa_shop']['value'];

if ( $data['merchantType'] == MERCHANT_TYPE_MERCHANT || $data['merchantType'] == MERCHANT_TYPE_MERCHANT_PURCHASE || $data['merchantType'] == MERCHANT_TYPE_MERCHANT_PURCHASE_PRETRANSACT) {
    foreach($data['payParams'] as $val) {
        if (isset($val['name'])) {
            $resData[$val['name']] = $val['value'];
        }
    }
    if ($resData["ext_transact"] == "") {
        unset($resData["ext_transact"]);
    }
    // pre($resData);die;
    list($res, $answer) = $CMD->DeltaCMD('merchant_purchase', $resData, 'dkcp/webkassa.py');
    //пока страшненько
} elseif ( $data['merchantType'] == MERCHANT_TYPE_MERCHANT_SEMIAUTO ) {
    $resData['how'] = $resData['webkassa_shop'];
    $resData['keyt'] = $data['payParams']['keyt_shop']['value'];
    list($res, $answer) = $CMD->DeltaCMD('merchant_payment_status', $resData, 'dkcp/webkassa.py');
} elseif ( $data['merchantType'] == MERCHANT_TYPE_MERCHANT_MANUAL ) {
    $resData['how'] = $resData['webkassa_shop'];
    $resData['keyt'] = $data['payParams']['keyt_shop']['value'];
    list($res, $answer) = $CMD->DeltaCMD('merchant_enrollment', $resData, 'dkcp/webkassa.py');
} else {
    echo(json_encode(['result' => '198', 'result_text' => 'Некорректный merchant_type', 'full_result' => false], JSON_UNESCAPED_UNICODE));
}
if ($res == '1') {
    echo(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    echo(json_encode(['result' => '199', 'result_text' => 'Неизвестная ошибка', 'full_result' => false], JSON_UNESCAPED_UNICODE));
}