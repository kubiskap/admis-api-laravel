<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 20.06.2018
 * Time: 15:37
 */
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
$dataChart = getPieGraphPhase2Projects();
$editor = [];
$series = [];
$colors = [];

$peklo = [];
foreach ($dataChart as $chartColumn) {
    array_push($editor, $chartColumn['name']);
    $phaseDetails = getPhaseDetails(NULL, $chartColumn['name']);
    $color = "chart-".$phaseDetails['phaseColorClass'];
    array_push($series, array("value" => $chartColumn['countProjektu'], "className" => $color, "meta" => $chartColumn['name']));
}
$peklo = [ "series" => $series];
echo json_encode($peklo);