<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_POST['idProject'])) {
    if (is_numeric($_POST['idProject'])) {
        echo listTasks($_POST['idProject']);
    } else {
        echo listTasks(NULL, $_SESSION['username'], TRUE, 6, 6);
    }
}
?>