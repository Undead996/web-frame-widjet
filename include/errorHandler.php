<?php
function errors_handler($errno, $errstr, $errfile, $errline)
{ //Пользовательская функция обработчика ошибок PHP
    $errors = array( //Формирования массива констант ошибок
        E_WARNING => 'E_WARNING',
        E_NOTICE => 'E_NOTICE',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED'
    );
    if (in_array($errno, ERROR_NO_REPORTING_VAL)) {
        return;
    }
    $err_to_log = [
         'str' => "$errors[$errno]: $errstr \n в $errfile на $errline строке. <br/>",
         'short' => "Error: $errors[$errno] <br/>",
    ];
    // exeption_to_graylog(LOG_WARNING, $err_to_log);
    if (DISPLAY_ERRORS) {
        echo(json_encode(['result' => $errno, 'result_text' => $err_to_log['str'], 'full_result' => false], JSON_UNESCAPED_UNICODE));
        die;
    }
}