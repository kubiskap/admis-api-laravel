<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_POST['idRequest']) && isset($_POST['requestComment']) && isset($_POST['idNewRequestStatus'])) {
   // print_r($_POST['userData']);
    echo insertRequestReaction($_POST);
}
?>