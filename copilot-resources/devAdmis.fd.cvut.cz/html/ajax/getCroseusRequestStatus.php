<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES."connectors/croseus.php";

overUzivatele($pristup_zakazan);
if (isset($_GET['idProject']) ) {
    print_r(json_encode(dejStavDokladuCroseusExt($_GET['idProject'])));
}
?>