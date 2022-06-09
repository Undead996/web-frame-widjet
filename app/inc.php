<?php
require './../config/config.php';
require './../include/functions.php';
require './../include/GELF/message.class.php';
require './../include/GELF/sendMessage.class.php';
require './../include/errorHandler.php';
require './../include/dkcp.php';
require './../include/Use_dkcp.php';

set_error_handler('errors_handler');