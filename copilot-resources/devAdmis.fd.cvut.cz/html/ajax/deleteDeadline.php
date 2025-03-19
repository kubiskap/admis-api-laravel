<?php
/**
 * Created by PhpStorm.
 * User: petros
 * Date: 29.03.2022
 * Time: 11:35
 */
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if(isset($_POST['token']) && validateHash($_POST['token'], date('H')) && isset($_POST['idProject']) && isset($_POST['idDeadlineType']) ) {
    if (deleteDeadline($_POST['idProject'], $_POST['idDeadlineType']))
        echo "";
    else
        echo "Chyba při komunikaci s databází. Např. deadline v databázi neexistuje.";
} else {
    echo "Neplatný token nebo chybějící informace k deadlinu.";
}