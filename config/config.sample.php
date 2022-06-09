<?php

// DKCP
define('DKCP_URL'                  , 'https://dkcp-demo.paypoint.pro/');
define('DKCP_PROGRAM'              , '15020');
define('DKCP_USER_LOGIN'           , '79001234567');
define('DKCP_USER_PASSWORD'        , '123456');
define('DKCP_PROGRAM_SKEYS'        , 'ee2yg7nUSGggvp0DAkbd');
define('DKCP_PROCESSING_VERSION'   , 'LAST');

// CERT
define('ROOT_DIR'                  , __DIR__.'/../html');
define('CERT_DIR'                  , __DIR__.'/../cert/');
define('CERT_NAME'                 , '120643000439049.crt');
define('CERT_PASSWORD'             , '123456');

// WIDGET
define('ONLY_CARD_FORM'            , '14931');
define('TEMPLATE_CARD_FORM'        , '19749');
// define('ONLY_CARD_FORM'            , false);
define('SHOP_PARAMS'               , ['ext_transact' => '/\D/', 'num_shop' => '/\D/', 'keyt_shop' => '/\D/', 'summ' => '/[^\d+\.\d*]/', 'payform' => '/\D/' ]);
define('TEST_POST_PARAM'           , '{"ext_transact":"","num_shop":"00000001","keyt_shop":"40903810700100494065","comment":"Оплата на 192.168.100.24:8080","summ":"26.11","payform":"","accountId":"testShop","skin":"","free_param":""}');

//GRAYLOG
define('GELF_PARAMS'               , ['version' => '1.1', 'host' => 'replica-vm-alex', 'processing' => 'processing_demo', 'facility' => 'widget',]);
define('GELF_URL'                  , 'http://192.168.130.8:12201/gelf');
define('GELF_CMD_RES_MAX_SIZE'     , 20000);
define('GELF_CMD_RES_MAX_LENGTH'   , 4000);
define('GELF_CMD_REQ_MAX_SIZE'     , 10000);
define('GELF_CMD_REQ_MAX_LENGTH'   , 100);

//ERRORS
define('DISPLAY_ERRORS'            , true);
define('ERROR_NO_REPORTING_VAL'    , [E_USER_DEPRECATED, E_DEPRECATED]);

//MERCHANT

define('DEFAULT_PAY_PARAMS'        , ['url_success' => ['name' => 'url_success', 'value' => 'http://192.168.100.24:80'],
                                      'url_decline' => ['name' => 'url_decline', 'value' => 'http://192.168.100.24:80'], 
                                      'url_callback' => ['name' => 'url_callback', 'value' => ''], 
                                    ]);
define('DEFAULT_PAY_FIELDS'        , ['card_number' => ['num_field' => '2', 'value' => ''],
                                      'card_mounth' => ['num_field' => '3', 'value' => ''],
                                      'card_year' => ['num_field' => '4', 'value' => ''],
                                      'cvc' => ['num_field' => '5', 'value' => '']]);