<?php
/**
 * Created by PhpStorm.
 * User: petros
 * Date: 14.10.2022
 * Time: 15:44
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

function returnPercentIfNotNUll($value) {
    return ($value==0)?'':'%';
}
function returnValueIfNotNUll($value) {
    return ($value==0)?'':$value;
}

//overUzivatele($pristup_zakazan);
$dataDashboard = getDashboardStatsNonGraph();
$sumOfAllProjects = $dataDashboard['countStavebCelkem'];
$htmlMail = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="x-apple-disable-message-reformatting" />
<title>ADMIS weekly manager report</title>
<style type="text/css">
	body {
		margin: 0;
		background-color: #eee;
	}
	table {
		border-spacing: 0;
	}
	td {
		padding: 0;
	}
	img {
		border: 0;
	}
	.wrapper {
	    width: 100%;
	    table-layout: fixed;
	    background: #eee;
	    padding-bottom: 60px;
	}
	.main {
	    background: #eee;
	    margin: 0 auto;
	    width: 100%;
	    /* max-width: 600px; */
	    border-spacing: 0;
	    font-family: "Roboto Light", sans-serif;
	    color: #3C4858;
	}
	.main td {
	    padding: 0 5px;
	}
	.two-columns {
	    text-align: center;
	    font-size: 0;
	}
	.two-columns .column {
	    width: 100%;
	    max-width: 300px;
	    display: inline-block;
	    vertical-align: top;
	}
	.card {
        max-width: 500px; 
        min-width: 300px; 
        margin: 15px auto; 
        padding: 20px; 
        background-color: #fff;
        border-radius: 4px;
    }
    .table-striped tr:nth-child(even) {
        background-color: #eee;
    }
    .table-triped td{
        padding: 5px;
    }

</style>
</head>
<body>

<center class="wrapper">

<table class="main" width="100%">

<!-- TOP BORDER -->
<tr>
<td height="8" style="background-color: #d81b60"></td>
</tr>

<!-- LOGO SECTION -->
<tr style=" margin-bottom: 20px;">
<td style="padding: 14px 0 4px; background-color: #3C4858">
    <table width="100%">
    <tr>
    <td class="two-columns">
    <table class="column">
    <tr>
    <td style="padding: 0 62px 10px"><a href="https://admis.fd.cvut.cz"><img src="https://admis.fd.cvut.cz/img/mostAdmisWhiteMini.png" alt=""></a><a href="https://admis.fd.cvut.cz"><img src="https://admis.fd.cvut.cz/img/textAdmisWhiteMini.png" alt=""></a></td>
    </tr>
</table>
<table class="column">
    <tr>
    <td style="padding: 0 10px 10px; height: 38px; font-size: 16px; color: #fff;">Manažerský report '.date('d. m. Y').'</td>
    </tr>
</table>
</td>
    </tr>
</table>
</td>
</tr>';

$phasesData = getPieGraphPhase2Projects();
$phasePrice = countSumPricesForPhases();
$htmlMail .= '
<!-- POČET PROJEKTŮ A FÁZÍ -->
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">'.$sumOfAllProjects.' projektů</div>
  <br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">';

foreach (array_reverse($phasesData) as $phaseData) {
    $htmlMail .= '
    <tr>
      <td align="right" style="padding: 4px 16px 4px 0;">
        <table width="'.round(2*100*$phaseData['countProjektu']/$sumOfAllProjects).'%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
          <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">   
              <td bgcolor="'.getColorForPhase($phaseData['name']).'">&nbsp;</td>
            </table>
          </td>
        </table>
      </td>
      <td width="62%">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <td valign="baseline">'.$phaseData['name'].'</td>
          <td width="23%" valign="baseline" style="text-align: right">'.$phaseData['countProjektu'].'</td>
          <td width="23%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">'.round(100*$phaseData['countProjektu']/$sumOfAllProjects, 1).'%</td>
        </table>
      </td>
    </tr>
    ';
}

$htmlMail .= '
  </table>
  <br>
  <div style="padding-top: 16px; font-size: 14px; border-top: 1px solid #eee;">Počet změn fáze za poslední týden: '.countPhaseChangesInInterval(7).'<br>
  '.countActiveProjects(7).' projektů přidáno za poslední týden<br>
  '.countActiveProjects(30).' projektů přidáno za poslední měsíc
  </div>
</div>
</td>
</tr>

<!-- HODNOTA PROJEKTŮ -->
<tr>
<td>
<div class="card">
<div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Celková hodnota projektů: '.number_format($dataDashboard['cenaStaveb'],0,","," ").' Kč</div>
<br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">';
foreach (array_reverse($phasesData) as $phaseData) {
    $price = round($phasePrice[array_search($phaseData['name'], array_column($phasePrice, 'phaseName'))]['price']);
    $htmlMail .= '
    <tr>
      <td align="right" style="padding: 4px 16px 4px 0;">
        <table width="'.round(100*$price/$dataDashboard['cenaStaveb']).'%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
          <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">   
              <td bgcolor="'.getColorForPhase($phaseData['name']).'">&nbsp;</td>
            </table>
          </td>
        </table>
      </td>
      <td width="70%">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <td valign="baseline">'.$phaseData['name'].'</td>
          <td width="60%" valign="baseline" style="text-align: right">'.number_format($price, 0, ",", " ").' Kč</td>
          <td width="15%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">'.round(100*$price/$dataDashboard['cenaStaveb']).'%</td>
        </table>
      </td>
    </tr>
    ';
}
$htmlMail .= '
  </table>
  </div>
</div>
</td>
</tr>


<!-- TERMÍNY V PŘÍŠTÍM TÝDNU -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Termíny v tomto týdnu</div>
    <br/>
                <table class="table-striped" style="font-size: smaller">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Projekt</th>
                        <th>Projekt ID</th>
                        <th>Typ termínu</th>
                        <th></th>
                    </tr>
                    </thead>
                    '.showTermsInNextWeek(7, NULL, FALSE).'
                </table>
</td>
</tr>

<!-- KONCE ZÁRUKY -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Bližící se konce záruky</div>
    <br/>
                <table class="table-striped" style="font-size: smaller">
                    <tr>
                        <th>Datum</th>
                        <th>ID</th>
                        <th>Projekt</th>
                        <th>Typ záruky</th>
                    </tr>';

                    $warranties = getUpcomingWarranties();
                    foreach ($warranties as $warranty) {
                        $htmlMail .= "<tr><td style='padding: 5px;'>".date("d. m. Y", strtotime($warranty["value"]))."</td><td style='padding: 5px;'>$warranty[idProject]</td><td style='padding: 5px;'>$warranty[projectName]</td><td style='padding: 5px;'>$warranty[deadlineName]</td></tr>";
                    }
$htmlMail .= '
                </table>
</td>
</tr>

<!-- POČET PŘIHLÁŠENÍ -->
<tr>
<td>
<div class="card">
Počet přihlášení za poslední týden: '.count(getLastLogins(1000, 7)).'.
</div>
</td>
</tr>


<!-- ROZLOŽENÍ PROJEKTŮ MEZI EDITORY -->
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Rozložení projektů mezi editory</div>
  <br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">';
$editor2Project = getStatsEditor2Projects();
  foreach ($editor2Project as $editorStats) {
      $htmlMail .= '
    <tr>
      <td align="right" style="padding: 4px 16px 4px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td style="padding: 16px 0 8px 0;">'.getUserAll($editorStats['editor'])[0]['name'].'</td>
    </tr>
    <tr>
      <td>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">   
          <td width="'.round(100*$editorStats['countProjektu']/60, 1).'%"> <!-- 6.45 / 10.572 -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0">';
      $editorPhases = countPhasesToEditor($editorStats['editor']);
      foreach ($editorPhases as $phase) {
          if ($phase['phaseCount'] != 0) $htmlMail .= '<td width="'.(100*$phase['phaseCount']/60).'%" bgcolor="'.getColorForPhase($phase['phaseName']).'" style="color: #ddd; font-size: small">'.$phase['phaseCount'].'</td>';
      }
      $htmlMail .= '</table>
          </td>
          <td style="padding-left: 8px; font-size: 12px; color: #868E96;">'.$editorStats['countProjektu'].' projektů</td>
        </table>
      </td>
    </tr>
  </table>
      </td>
    </tr>';
  }
$htmlMail .= '
  </table>
</div>
</td>
</tr>

<!-- FOOTER SECTION -->

</table>

</center>

</body>
</html>
';

//$htmlMail = '
//<html>
//<head>
//    <style>
//
//    </style>
//</head>
//    <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Uvidí distribuci projektů mezi editory (graf i počet)
//                </h4>
//            </div>
//            <div class="card-body">
//                TODO
//            </div>
//        </div>
//    </div>
//
//    <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Uvidí počet staveb dle fáze + informaci o zmeně
//                </h4>
//            </div>
//            <div class="card-body">
//                '.getProjectCountsByPhase().'
//                Počet změn fází za poslední týden: '.countPhaseChangesInInterval(7).'
//            </div>
//        </div>
//    </div>
//
//        <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Počet projektů + informace kolik jich přibylo
//                </h4>
//            </div>
//            <div class="card-body">
//                Počet projektů: '.countActiveProjects().',<br>
//                '.countActiveProjects(7).' projektů přidáno za poslední týden,<br>
//                '.countActiveProjects(30).' projektů přidáno za poslední měsíc.
//            </div>
//        </div>
//    </div>
//
//    <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Seznam bližících se záruk VŠECH projektů
//                </h4>
//            </div>
//            <div class="card-body">
//                <table class="table table-striped">
//                    <thead>
//                    <tr>
//                        <th scope="col">Datum</th>
//                        <th scope="col">ID Projekt</th>
//                        <th scope="col">Projekt</th>
//                        <th scope="col">Typ záruky</th>
//                        <th scope="col">Akce</th>
//                    </tr>
//                    </thead>
//                    <tbody id="projectReportChangesTBody">.';
//
//                    $warranties = getUpcomingWarranties();
//                    foreach ($warranties as $warranty) {
//                        $htmlMail .= "<tr><td>".date("d. m. Y", strtotime($warranty["value"]))."</td><td>$warranty[idProject]</td><td>$warranty[projectName]</td><td>$warranty[deadlineName]</td><td><a href='detail.php?idProject=$warranty[idProject]'><i class=\"fa fa-sign-in-alt\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID $warranty[idProject]\"></i></td></tr>";
//                    }
//$htmlMail .= '
//                    </tbody>
//                </table>
//            </div>
//        </div>
//    </div>
//
//    <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Počet přihlášení uživatelů
//                </h4>
//            </div>
//            <div class="card-body">
//                Počet přihlášení za poslední týden: '.count(getLastLogins(1000, 7)).'. <a href="nastaveni.php?sprava=logy#logins">Zobrazit detailní přehled přihlašování</a>.
//            </div>
//        </div>
//    </div>
//
//    <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Výhled termínů pro nadcházející týden
//                </h4>
//            </div>
//            <div class="card-body">
//                <table class="table table-striped" id="termsInNextWeekOverviewTable">
//                    <thead>
//                    <tr>
//                        <th scope="col">Datum</th>
//                        <th scope="col">Projekt</th>
//                        <th scope="col">Projekt ID</th>
//                        <th scope="col">Typ termínu</th>
//                        <th scope="col">Akce</th>
//                    </tr>
//                    </thead>
//                    '.showTermsInNextWeek(7, NULL, FALSE).'
//                </table>
//            </div>
//        </div>
//    </div>
//
//    <div class="col-md-12">
//        <div class="card">
//            <div class="card-header card-header-icon card-header-rose">
//                <div class="card-icon">
//                    <i class="material-icons">edit_note</i>
//                </div>
//                <h4 class="card-title">Hodnota projektu + změna orpoti minulému období
//                </h4>
//            </div>
//            <div class="card-body">
//                Celková hodnota projektů: '.countAllProjectPricesAsOf().'<br>
//                Zvýšení hodnoty projektů za poslední týden: '.countAllProjectPricesAsOf(7).'
//            </div>
//        </div>
//    </div>
//</html>
//';

echo $htmlMail;
// echo sendMail('Admis týdenní report', $htmlMail, 'nemas html', ['hnykpetr@fd.cvut.cz'], 'noreply@fd.cvut.cz', true );
// echo sendMail('Admis týdenní report', $htmlMail, 'nemas html', ['ales.cermak@ksus.cz', 'jan.fidler@ksus.cz', 'petr.nadvornik@ksus.cz', 'hnykpetr@fd.cvut.cz', 'kocian@fd.cvut.cz', 'ondra@fd.cvut.cz'], 'noreply@fd.cvut.cz', true );