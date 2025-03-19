<?php
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 25.07.2018
 * Time: 10:44
 */
require_once __DIR__."/../html/conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
require_once SYSTEMINCLUDES. "/connectors/croseus.php";

//overUzivatele($pristup_zakazan);

//print_r(PridatAktualizovatPrilohu($arrValue));
print_r(updateCroseusRequests());


