<?php
class SendMSG{
    private $curl;
    private $url;
    private $msg;
    private $success_http_answer = 202;
    public function __construct($url, $msg)
    {   
        $this->url = $url;
        $this->msg = $msg;
        $this->prepare();
    }

    private function prepare()
    {
        $this->curl = curl_init($this->url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->msg);
    }
    
    public function setHeaders($arr)
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $arr);
    }
    
    public function execMsg($getInfo = false)
    {
        $res = curl_exec($this->curl);
        if (curl_getinfo( $this->curl, CURLINFO_HTTP_CODE) === $this->success_http_answer) {
            // if ($getInfo === true) {
            //     $info = curl_getinfo($this->curl);
                // write_log("Graylog GELF curl_getinfo.\n".print_r($info, true));
            // }
            curl_close($this->curl);
            return $res;
        } else {
            // echo 'Http code: '.curl_getinfo( $this->curl, CURLINFO_HTTP_CODE);
            // $info = curl_getinfo($this->curl);
            // write_log("Graylog unavailable.\n".
            //            "Http code: ".$info['http_code']."\n"
            //            ."Graylog GELF curl_getinfo.\n".print_r($info, true)."\n". $this->msg);
            curl_close($this->curl);
            return false;
        }
        
    }
}