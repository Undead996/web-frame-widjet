<?php
class Use_dkcp{
    public function __construct()
    {
        $this->dkcp_api = new DKCP_API(array(
            'URL'                  => DKCP_URL,
            'PROGRAM'              => DKCP_PROGRAM,
            'PROGRAM_SKEYS'        => DKCP_PROGRAM_SKEYS,
            'USER_LOGIN'           => DKCP_USER_LOGIN,
            'USER_PASSWORD'        => DKCP_USER_PASSWORD,
            'PATH_CERT'            => CERT_DIR,
            'CERT'                 => CERT_DIR . CERT_NAME,
            'CERT_PASSWORD'        => CERT_PASSWORD,
            'PROTOCOL_VERSION'     => DKCP_PROCESSING_VERSION,

            'BEFORE_SEND'        => function($cmd, $params){
                $params['device_fingerprint'] = set_fingerprint();
                return $params;
            },

            'RESPONSE_BEFORE_PARSE' => function($cmd, $url, $params, $response){

            },

            'RESPONSE_AFTER_PARSE' => function($cmd, $res, $response){

                return $response;
            }

        ));
    }
    public function DeltaCMD($cmd, $params = [], $script = null, $free = False){
        $dkcp_cmd = $this->dkcp_api->cmd($cmd, $params, $script, $free);
        to_graylog(LOG_INFO, $cmd, $dkcp_cmd, $params, $script);
        if (isset($dkcp_cmd['1']['status'])) {
            if ($dkcp_cmd['1']['status'] != 2) {
                echo(json_encode(['result' => $dkcp_cmd['1']['result'] , 'result_text' => $dkcp_cmd['1']['result_text'], 'full_result' => $dkcp_cmd['1']], JSON_UNESCAPED_UNICODE));
                die;
            }
        } else {
            echo(json_encode(['result' => 'unexpected' , 'result_text' => $dkcp_cmd[1], 'full_result' => $dkcp_cmd], JSON_UNESCAPED_UNICODE));
                die;
        }
        return $dkcp_cmd;
    }
}