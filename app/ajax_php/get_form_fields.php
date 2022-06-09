<?php
require './inc.php';

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
$CMD = new Use_dkcp();
list($res, $answer) = $CMD->DeltaCMD('get_form_fields', $data, 'dkcp/direct.py');
if ($res == '1') {
    echo(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    echo(json_encode(['result' => '99', 'result_text' => 'Неизвестная ошибка', 'full_result' => false], JSON_UNESCAPED_UNICODE));
}
