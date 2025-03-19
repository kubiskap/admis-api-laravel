<?php
/**
 * Created by PhpStorm.
 * User: petros
 * Date: 03.11.2022
 * Time: 16:02
 */
session_start();
$_SESSION['global_filtr'] = 'all';
require_once __DIR__."/../../conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
// require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";

function getColorForPhase($name) {
    switch ($name) {
        case "Záměr":
            $color = "#f44336";
            break;
        case "V přípravě":
            $color = "#e91e63";
            break;
        case "Připraveno":
            $color = "#ff9800";
            break;
        case "V realizaci":
            $color = "#00bcd4";
            break;
        case "Zrealizováno":
            $color = "#4caf50";
            break;
    }
    return $color;
}

class Output {}
class Projekty {}
class Hodnota {}
$output = new Output();


$dataDashboard = getDashboardStatsNonGraph();
$sumOfAllProjects = $dataDashboard['countStavebCelkem'];
$phasesData = getPieGraphPhase2Projects();
$phasePrice = countSumPricesForPhases();

// POČET PROJEKTŮ A FÁZÍ
$projekty = new Projekty();
$projekty->celkem = $sumOfAllProjects;
foreach (array_reverse($phasesData) as $phaseData) {
    $projekty->{$phaseData['name']} = $phaseData['countProjektu'];
}
$output->pocetProjektu = $projekty;

// HODNOTA PROJEKTŮ
$hodnota = new Hodnota();
$hodnota->celkem = $dataDashboard['cenaStaveb'];
foreach (array_reverse($phasesData) as $phaseData) {
    $price = round($phasePrice[array_search($phaseData['name'], array_column($phasePrice, 'phaseName'))]['price']);
    $hodnota->{$phaseData['name']} = $price;
}
$output->cenaProjektu = $hodnota;

// POČET PŘIHLÁŠENÍ 
$output->pocetPrihlaseni = count(getLastLogins(1000, 7));

$jsonData = json_encode($output, JSON_UNESCAPED_UNICODE);

echo $jsonData."\n";