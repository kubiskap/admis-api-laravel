<?php

session_start();
$_SESSION['global_filtr'] = 'all';
require_once __DIR__."/../../conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
// require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
// require_once SYSTEMINCLUDES . "../mail-reports/manager-report.php";
require_once SYSTEMINCLUDES . "../mail-reports/mail-reports.php";

// function prepareReport(string $reportType, array $ouArr, string $email, string $name, bool $saveData = FALSE, int $reportConfigId)
 echo prepareEditorReport('editor', [], 'hnykpetr@fd.cvut.cz', 'Report', FALSE, 4, 'petr.muller');
// echo prepareManagerReport('manager', [3, 4, 5], 'hnykpetr@fd.cvut.cz', 'Report', FALSE, 2);

?>
