<?php
/**
 * Created by PhpStorm.
 * User: petros
 * Date: 10.08.2023
 * Time: 15:15
 */
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if(isset($_POST['idTask']) ) {
    echo deleteTask($_POST['idTask']);
}