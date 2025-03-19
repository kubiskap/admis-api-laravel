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

if (isset($_POST['reportType'])) {
    require_once SYSTEMINCLUDES . "../mail-reports/mail-reports.php";
    if ($_POST['reportType'] == 'manager' && isset($_POST['idManagerReport'])) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->query("SELECT ouIds FROM `reportConfig` WHERE idReportConfig = ".(int)$_POST['idManagerReport']);
        $ouIds = $stmt->fetch();
        $report = prepareManagerReport($_POST['reportType'], json_decode($ouIds['ouIds']), $_SESSION['email'], $_SESSION['jmeno'], FALSE, $_POST['idManagerReport']);
        print_r(sendMail('TEST Admis manažerského reportu', $report, 'Obsah dostupný pouze v HTML podobě.', $_SESSION['email'], 'noreply@fd.cvut.cz', true ));
    } elseif ($_POST['reportType'] == 'editor') {
        $report = prepareEditorReport('editor', [], $_SESSION['email'], $_SESSION['jmeno'], FALSE, 4, $_SESSION['username']);
        print_r(sendMail('TEST Admis osobního reportu', $report, 'Obsah dostupný pouze v HTML podobě.', $_SESSION['email'], 'noreply@fd.cvut.cz', true ));
    }

}