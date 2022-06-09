<?php
function check_shop_param($data) {
    foreach($data as $key => &$param) {
        filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (isset(SHOP_PARAMS[$key])) {
            preg_match_all(SHOP_PARAMS[$key], $param, $matches);
            if (isset($matches[0][0])) {
                echo('Некоректный параметр: '.$key);
                die();
            }
        }
        if (!isset($data['payform'])) {
            $data['payform'] = "";
        }
        if (!isset($data['free_param'])) {
            $data['free_param'] = "";
        }
        if (!isset($data['ext_transact'])) {
            $data['ext_transact'] = "";
        }
    }
    return $data;
}

function set_default_data($data, $answer) {
    if (!$data['payform']) {
        $data['payform'] = ONLY_CARD_FORM;
    }
    $payParams = DEFAULT_PAY_PARAMS;
    $payParams['ext_transact'] = ['name' => 'ext_transact', 'value' => $data['ext_transact'] ];
    $payParams['form'] = ['name' => 'form', 'value' => $data['payform'] ];
    $payParams['free_param'] = ['name' => 'free_param', 'value' => $data['free_param']];
    $payParams['keyt_shop'] = ['name' => 'keyt_shop', 'value' => $data['keyt_shop']];
    $payParams['webkassa_shop'] = ['name' => 'webkassa_shop', 'value' => $data['num_shop']];
    $payFields = $answer['table']['colvalues'];
    return ['mainData' => $data, 'payParams' => $payParams, 'payFields' => $payFields];
}

function get_user_ip(){
    if ( getenv('HTTP_CF_CONNECTING_IP') ) $user_ip = getenv('HTTP_CF_CONNECTING_IP');
    elseif ( getenv('HTTP_X_REAL_IP') ) $user_ip = getenv('HTTP_X_REAL_IP');
    elseif ( getenv('HTTP_FORWARDED_FOR') ) $user_ip = getenv('HTTP_FORWARDED_FOR');
    elseif ( getenv('HTTP_X_FORWARDED_FOR') ) $user_ip = getenv('HTTP_X_FORWARDED_FOR');
    elseif ( getenv('HTTP_X_COMING_FROM') ) $user_ip = getenv('HTTP_X_COMING_FROM');
    elseif ( getenv('HTTP_VIA') ) $user_ip = getenv('HTTP_VIA');
    elseif ( getenv('HTTP_XROXY_CONNECTION') ) $user_ip = getenv('HTTP_XROXY_CONNECTION');
    elseif ( getenv('HTTP_CLIENT_IP') ) $user_ip = getenv('HTTP_CLIENT_IP');
    elseif ( getenv('REMOTE_ADDR') ) $user_ip = getenv('REMOTE_ADDR');
    return trim($user_ip);
}

function get_act_url() {
    $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

function checkLength($max, $to, $str) {
    if (strlen($str) > $max) {
       return substr($str, 0, $to).'...  ['.strlen($str).' bytes]';
    } else {
       return $str;
    }
}

function to_graylog($level, $cmd, $data, $params, $script){
    // foreach($params as &$val) {
    //     $val = @iconv("windows-1251", "utf-8", $val);
    // }
    unset($val);
    if (gettype($cmd) == 'string') {
        $cmd = [
            [$cmd, $params, $script]
        ];
        $data = [$data];
    }
    foreach ($cmd as $key => $val) {
        $cmd_name = $val['0'];
        $full_req = json_encode($val['1'], JSON_UNESCAPED_UNICODE);
        $cmd_url = $val['2'];
        $dkcp_answer = $data[$key]['1'];
        $full_response = json_encode($dkcp_answer, JSON_UNESCAPED_UNICODE);
         
        if (isset($dkcp_answer['result'])) {
            if ($dkcp_answer['result'] !== '0') {
                $level = LOG_NOTICE;
            }
        }
        
        if (gettype($dkcp_answer) == 'string' ) {
            $exeption = [
                'short' => "ERROR: ".$cmd_name.' HTTP_ERROR',
                'str' => $dkcp_answer
            ];
                exeption_to_graylog(LOG_ERR, $exeption);
            continue;
        }
        
            $full_req = checkLength(GELF_CMD_REQ_MAX_SIZE, GELF_CMD_REQ_MAX_LENGTH, $full_req);
            $full_response = checkLength(GELF_CMD_RES_MAX_SIZE, GELF_CMD_RES_MAX_LENGTH, $full_response);
            
            $message = new Message(GELF_PARAMS, $level, $cmd_name." ".$data[$key]['1']['result_text']);
            $add = [
                'dkcp_cmd' => $cmd_name,
                'request_url' => DKCP_URL.$cmd_url,
                'ext_transact' => $dkcp_answer['ext_transact'],
                'transact' => $dkcp_answer['transact'],
                'dkcp_result' => $dkcp_answer['result'],
                'request_fields' => $full_req,
                'response' => $full_response,
            ];
            $message->setAdditional($add);
            $msg = $message->getMsg();
            $post = new SendMSG(GELF_URL, $msg);
            $headers = ['Content-Type: application/json'];
            $post->setHeaders($headers);
            $post->execMsg();
    }
}


function exeption_to_graylog($level, $exeption) {
    $message = new Message(GELF_PARAMS, $level, $exeption['short']);
    $add = [
        'error_text' => $exeption['str']
        ];
    $message->setAdditional($add);
    $msg = $message->getMsg();
    $post = new SendMSG(GELF_URL, $msg);
    $headers = ['Content-Type: application/json'];
    $post->setHeaders($headers);
    $post->execMsg();
}

function check_merchant_sign($CMD, $request, $skeys = false) {
	
	if ( !$skeys )
		$skeys = get_eshop_params($CMD, $request['num_shop'])['skeys'];
	
	list($sign, $sign_params, $signed_str) = calc_sign($request, $skeys);
    pre(['sign' => $sign]);
    die;
	if ( $sign !== $request['sign'] ) {
        pre(['req_sign' => $request['sign'], 'proc_sign' => $sign]);
		// return array(false, $signed_str);
		return array(false, 'Некорректная подпись запроса.');
	}
	
	return array(true, $sign_params);
}

function pre($some) {
    echo(json_encode(['result' => '102', 'result_text' => $some]));
}

function get_eshop_params($CMD, $num_eshop) {
	$getparams_eshop_request = array(
		'num_eshop'     => $num_eshop, 
		'webkassa_shop' => $num_eshop
	);
	list($res, $getparams_eshop) = $CMD->DeltaCMD("getparams_eshop", $getparams_eshop_request, 'register.py');
	if ( !$res )                      return array(false, $getparams_eshop);
	if ( $getparams_eshop['result'] ) return array(false, $getparams_eshop['result_text']);
	$skeys = $getparams_eshop['advanced'];
	
	return $skeys;
}

function calc_sign($request, $skeys) {
	
	$sign_params = array('ext_transact', 'num_shop', 'keyt_shop', 'identified', 'sum', 'payform', 'comment', 'free_param', 'original_url_success', 'original_url_decline', 'original_url_callback');
	
	$signed_str = '';
	foreach ( $sign_params as $sign_param ) {
		if ( $request['identified'] == 1 or $sign_param != 'sum' ) {// при identified=0 сумма в подписи не участвует
			
			if ( !isset($request[$sign_param]) ) {
				return array(false, "Отсутствует параметр $sign_param.");
				// return array(false, "Отсутствует параметр $sign_param in " . print_r($request, 1));
			}
			
			$signed_str .= $request[$sign_param];
		}
	}
	
	$sign = hmac($skeys, $signed_str);
	return array($sign, $sign_params, $signed_str);
}

function hmac($key, $data) {
    $b = 64; // byte length for md5
    if (strlen($key) > $b) {
        $key = pack("H*",md5($key));
    }
    $key = str_pad($key, $b, chr(0x00));
    $ipad = str_pad('', $b, chr(0x36));
    $opad = str_pad('', $b, chr(0x5c));
    $k_ipad = $key ^ $ipad ;
    $k_opad = $key ^ $opad;

    return md5($k_opad . pack("H*",md5($k_ipad . $data)));
}

function set_fingerprint() {
    $fingerprint = [
        'userAgent'=> $_SERVER['HTTP_USER_AGENT'],
        'user_ip' => $_SERVER['REMOTE_ADDR'],
        'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
    ];
    return json_encode($fingerprint, JSON_UNESCAPED_UNICODE);
}




