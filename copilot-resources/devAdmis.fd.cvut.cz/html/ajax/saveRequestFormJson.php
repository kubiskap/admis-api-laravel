<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_POST['idRequestType']) && isset($_POST['userData']) && isset($_POST['idProject']) && isset($_POST['idRequestStatus'])) {
   // print_r($_POST['userData']);
    //print_r($_POST);
    echo insertRequest($_POST);
}
?>