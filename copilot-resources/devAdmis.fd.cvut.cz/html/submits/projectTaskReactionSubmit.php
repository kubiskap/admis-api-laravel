<?php
/**
 * Created by PhpStorm.
 * User: ondra
 * Date: 13.11.2019
 * Time: 9:53
 */

require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";

overUzivatele($pristup_zakazan);
require_once SYSTEMINCLUDES . "function.php";

if(isset($_POST) && isset($_POST['idTask']) && isset($_POST['reaction'])){

    if (empty($_POST['idReaction'])) {
        echo insertTaskReaction($_POST);
    } else {
        echo updateTaskReaction($_POST);
    }

}