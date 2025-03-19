<?php
/**
 * Created by PhpStorm.
 * User: Petros
 * Date: 20.07.2022
 * Time: 18:40
 */

require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);

if(isset($_POST['filter'])){
    $_SESSION['global_filtr'] = $_POST['filter'];
}