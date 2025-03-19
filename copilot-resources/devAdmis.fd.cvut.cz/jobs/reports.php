<?php

session_start();
$_SESSION['global_filtr'] = 'all';
require_once __DIR__."/../html/conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
// require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
require_once __DIR__."/../mail-reports/mail-reports.php";





$arrReportsToDo = getListOfReports();
print_r($arrReportsToDo);
foreach ($arrReportsToDo as $eachReport){
    $report = prepareManagerReport($eachReport['reportType'], json_decode($eachReport['ouArr'], true), $eachReport['emails'], $eachReport['names'], TRUE, $eachReport['idReportConfig']);
    $emails = explode(',', $eachReport['emails']);
    foreach ($emails as $email) {
        print_r(sendMail('Admis týdenní report', $report, 'Obsah dostupný pouze v HTML podobě.', $email, 'noreply@fd.cvut.cz', true ));
    }
        //print_r($report);
}
$editorsWithReportsOn = getListOfEditorReports();
print_r($editorsWithReportsOn);
foreach ($editorsWithReportsOn as $editor){
    $report = prepareEditorReport('editor', [], $editor['email'], $editor['name'], TRUE, 4, $editor['username']);
    print_r(sendMail('Admis týdenní report', $report, 'Obsah dostupný pouze v HTML podobě.', $editor['email'], 'noreply@fd.cvut.cz', true ));
    //print_r($report);
}