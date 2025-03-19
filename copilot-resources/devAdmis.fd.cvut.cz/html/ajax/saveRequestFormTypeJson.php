<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_POST['idRequestType']) && isset($_POST['formData']) && isset($_POST['requestName']) && isset($_POST['requestCode'])) {
    echo saveRequestFormTypeJson($_POST['idRequestType'], $_POST['requestName'], $_POST['requestCode'], $_POST['formData']);
}
?>