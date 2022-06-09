<?php

// DKCP
define('DKCP_URL'                  , 'https://dkcp-dev.paypoint.pro/');
define('DKCP_PROGRAM'              , '10080');
// define('DKCP_PROGRAM'              , '10085');
define('DKCP_USER_LOGIN'           , 'dde611ty4s');
define('DKCP_USER_PASSWORD'        , 'QWEdCVB67');
// define('DKCP_PROGRAM_SKEYS'        , 'z2xigrvDNRf3Xm3g6TNL');
define('DKCP_PROGRAM_SKEYS'        , 'hfuise%dhfidhsfuh39$8#2f2!!38dsd');
define('DKCP_PROCESSING_VERSION'   , 'LAST');

// CERT
define('ROOT_DIR'                  , __DIR__.'/../html');
define('CERT_DIR'                  , __DIR__.'/../cert/');
define('CERT_NAME'                 , 'web.crt');
define('CERT_PASSWORD'             , 'QWEdCVB67');

// WIDGET
define('ONLY_CARD_FORM'            , '19749');
// define('ONLY_CARD_FORM'            , false);
define('SHOP_PARAMS'               , ['ext_transact' => '/\D/', 'num_shop' => '/\D/', 'keyt_shop' => '/\D/', 'summ' => '/[^\d+\.\d*]/', 'payform' => '/\D/' ]);
define('TEST_POST_PARAM'           , '{"ext_transact":"","num_shop":"4010","keyt_shop":"40914810600000000004","comment":"Оплата на 192.168.100.24:8080","summ":"2","payform":"","skin":"","free_param":""}');



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
define('MERCHANT_TYPE_MERCHANT'                      , 0);
define('MERCHANT_TYPE_MERCHANT_SEMIAUTO'             , 1);
define('MERCHANT_TYPE_MERCHANT_MANUAL'               , 2);
define('MERCHANT_TYPE_MERCHANT_PURCHASE'             , 3);
define('MERCHANT_TYPE_MERCHANT_ORDER'                , 4);
define('MERCHANT_TYPE_MERCHANT_PURCHASE_PRETRANSACT' , 5);

define('DEFAULT_PAY_PARAMS'        , ['url_success' => ['name' => 'url_success', 'value' => 'http://192.168.100.24:80'],
                                      'url_decline' => ['name' => 'url_decline', 'value' => 'http://192.168.100.24:80'], 
                                      'url_callback' => ['name' => 'url_callback', 'value' => ''], 
                                    ]);
define('DEFAULT_PAY_FIELDS'        , ['card_number' => ['num_field' => '2', 'value' => ''],
                                      'card_mounth' => ['num_field' => '3', 'value' => ''],
                                      'card_year' => ['num_field' => '4', 'value' => ''],
                                      'cvc' => ['num_field' => '5', 'value' => '']]);