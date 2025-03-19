<?php

/**
 * Created by PhpStorm.
 * User: petros
 * Date: 16.12.2022
 * Time: 15:24
 */

require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
require_once SYSTEMINCLUDES . "function.php";

overUzivatele($pristup_zakazan);

if (isset($_POST['editorReports'])) {
    updateEditorReportSettings($_POST['editorReports']);
}