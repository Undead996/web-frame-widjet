<?php
class Message
{   
    private $msg;
    public function __construct($confData, $level, $params)
    {   
        $this->msg = [
            'version' => $confData['version'],
            'host' => $confData['host'],
            'short_message' => $params,
            'timestamp' => microtime(),
            'user_ip' => get_user_ip(),
            'url' => get_act_url(),
            'script' => $_SERVER['SCRIPT_NAME'],
            'level' => $level,
            '_processing' => $confData['processing'],
            '_facility' => $confData['facility'],
        ];
    }
    public function setAdditional($arr)
    {
        foreach ( $arr as $key => $val) {
            $this->msg['_'.$key] = $val;
        }
    }
    public function getMsg()
    {   
        return json_encode($this->msg);
    }
}

