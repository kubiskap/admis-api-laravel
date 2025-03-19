<?php
/**
 * ALL MAIL REPORT FUNCTIONS ARE HERE - MANAGER AND EDITOR FOR NOW
 * Created by PhpStorm.
 * User: petros
 * Date: 14.10.2022
 * Time: 15:44
 */

function getColorForPhase($name) {
    $color = colorByPhaseName($name);
    return $color;
}

function formatDifference($thisWeek, $lastWeek) {
    if ($lastWeek) {
        if (round($thisWeek) > round($lastWeek)) {
            $procenta = round((100 * $thisWeek / $lastWeek) - 100, 1);
            return "<span style='color: darkgreen'>▲ +$procenta %</span>";
        } elseif (round($thisWeek) < round($lastWeek)) {
            $procenta = round((100*$thisWeek/$lastWeek)-100, 1);
            return "<span style='color: darkred'>▼ $procenta %</span>";
        } else {
            return "<span style='color: darkblue'>⚊ 0 %</span>";
        }
    } else {
        return "";
    }
}

function saveReportData(array $pricesPerPhases, array $projectsPerPhases, array $projectsEditorsBreakdown, int $reportConfigId, string $username = NULL){
    $pricesPerPhases = json_encode($pricesPerPhases);
    $projectsPerPhases = json_encode($projectsPerPhases);
    $projectsEditorsBreakdown = json_encode($projectsEditorsBreakdown);

    require_once SYSTEMINCLUDES . "autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('INSERT INTO `reportsHistory`(`pricesPerPhases`, `projectsPerPhases`, `projectsEditorsBreakdown`, `created`, `relatedToConfig`, `relatedToUsername`) 
                                                    VALUES (:pricesPerPhases, :projectsPerPhases,:projectsEditorsBreakdown, NOW(),:relatedToConfig, :username)');
    $stmt->bindParam(':pricesPerPhases', $pricesPerPhases, PDO::PARAM_STR);
    $stmt->bindParam(':projectsPerPhases', $projectsPerPhases, PDO::PARAM_STR);
    $stmt->bindParam(':projectsEditorsBreakdown', $projectsEditorsBreakdown, PDO::PARAM_STR);
    $stmt->bindParam(':relatedToConfig', $reportConfigId, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $lastId = $dbh->getDbLink()->lastInsertId();

    return $lastId;

}

function getHistoryOfUserProjects($username){
    $usersArr = false;
    require_once SYSTEMINCLUDES . "autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogNoHidden` WHERE idProject IN (SELECT idProject FROM `viewProjectsActive` WHERE editor = '".$username."') ORDER BY created DESC LIMIT 20" );
    $usersArr = $query->fetchAll(PDO::FETCH_ASSOC);

    return $usersArr;
}

function getListOfReports(){
    $usersArr = false;
    require_once SYSTEMINCLUDES . "autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT GROUP_CONCAT(username) as usernames, GROUP_CONCAT(users.name ) as names, GROUP_CONCAT(email) as emails, idReportConfig,ouIds as ouArr, reportType FROM users JOIN ou USING(idOU) JOIN reportConfig USING(idReportConfig) WHERE reportConfig.reportType != 'noreport' GROUP BY idReportConfig" );
    $usersArr = $query->fetchAll(PDO::FETCH_ASSOC);

    return $usersArr;
}

// Vyselectuje lidi, co mají editor report ON a zároveň nejsou blokovaní a mají alespoň jeden projekt.
function getListOfEditorReports(){
    $usersArr = false;
    require_once SYSTEMINCLUDES . "autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT username, name, email FROM users WHERE editorReport = 1 AND accessDenied = 0 AND username IN (SELECT DISTINCT editor as username FROM `viewProjectsActive` )");
    $usersArr = $query->fetchAll(PDO::FETCH_ASSOC);

    return $usersArr;
}

function getPreviousReportData($reportConfigId){
    $data = false;
    require_once SYSTEMINCLUDES . "autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT * FROM reportsHistory
                                                    WHERE relatedToConfig = $reportConfigId AND  idReportHistory = (SELECT MAX(idReportHistory) FROM reportsHistory WHERE relatedToConfig = $reportConfigId)" );
    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data;
}

function getPreviousEditorReportData($username){
    $data = false;
    require_once SYSTEMINCLUDES . "autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT * FROM reportsHistory WHERE relatedToUsername = '$username' AND idReportHistory = (SELECT MAX(idReportHistory) FROM reportsHistory WHERE relatedToUsername = '$username')" );
    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data;
}

// MANAZERSKY REPORT
function prepareManagerReport(string $reportType, array $ouArr, string $email, string $name, bool $saveData = FALSE, int $reportConfigId)
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT * FROM `reportConfig` WHERE idReportConfig = ".$reportConfigId);
    $reportConfig = $stmt->fetch();
    if($ouArr[0] == 'all') {
        $_SESSION['global_filtr'] = 'all';
        $dataDashboard = getDashboardStatsNonGraph();
        $phasesData = getPieGraphPhase2Projects();
        $phasePrice = countSumPricesForPhases();
        $editor2Project = getStatsEditor2Projects();
        $warranties = getUpcomingWarranties();
        $lastLogins = getLastLogins(1000, 7, NULL);
        $lastLoginsBefore = getLastLogins(1000, 7, NULL, 7);
    }
    elseif(is_array($ouArr)){
        $_SESSION['global_filtr'] = 'none';
        $dataDashboard = getDashboardStatsNonGraph($ouArr);
        $phasesData = getPieGraphPhase2Projects($ouArr);
        $phasePrice = countSumPricesForPhases($ouArr);
        $editor2Project = getStatsEditor2Projects($ouArr);
        $warranties = getUpcomingWarranties();
        $lastLogins = getLastLogins(1000, 7,$ouArr);
        $lastLoginsBefore = getLastLogins(1000, 7, $ouArr, 7);
    }
    $previousData = is_array(getPreviousReportData($reportConfigId)) ? getPreviousReportData($reportConfigId) : NULL;
    if(!is_null($previousData)){
        $phasesDataPrevious = json_decode($previousData['projectsPerPhases'],true);
        $pricesDataPrevious = json_decode($previousData['pricesPerPhases'],true);
        $editorDataPrevious = json_decode($previousData['projectsEditorsBreakdown'],true);
    }

    if($saveData){
        saveReportData($phasePrice, $phasesData, $editor2Project, $reportConfigId, null);
    }
    // print_r($previousData);

    require_once __DIR__ . "/../html/conf/config.inc";
    require_once SYSTEMINCLUDES . "function.php";
// require_once SYSTEMINCLUDES."authenticateUser.php";
    require_once SYSTEMINCLUDES . "autoLoader.php";

    // $phasesData = getPieGraphPhase2Projects();
    // $phasePrice = countSumPricesForPhases();
//overUzivatele($pristup_zakazan);
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
	    padding: 0 1px;
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
    <td style="padding: 0 62px 10px"><a href="https://admis.fd.cvut.cz"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACkAAAAmCAYAAABZNrIjAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkFCMThDMUY2RjMxRDExRThBMTgyRkJGN0JFRTY4MEZEIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkFCMThDMUY3RjMxRDExRThBMTgyRkJGN0JFRTY4MEZEIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QUIxOEMxRjRGMzFEMTFFOEExODJGQkY3QkVFNjgwRkQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QUIxOEMxRjVGMzFEMTFFOEExODJGQkY3QkVFNjgwRkQiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5QeF8tAAACcElEQVR42uyYz0tUURTH33NEC2RAXbSwKCuIwH7QIiJoUsisNhX0YxetWlghQYv+gGjlLiUioRIxSKWFRdEmqCgIKkqqTRBi5SJqEf3AsMbPgSM8Hu/NvPvuXGeGvPDhzbz7Y75z7r3nnHv9fD7vVXqp8aqgVIXI2grR0QArYBU0wwS8gdlyiZTZWwlbYRtsgbWwDDLa5hmch3H54i/QxsnCduiCDlgPdaE2IsSHd7Bx3oquLdmggo5Ap1oqrjyC6zCgYmddr0mxwnE4DMsTtP8Mu6HF9caRcfZBD7Qn9Brz0ytWmynUx1ZkPRyFs7DBoF8/rIE9Lv2kWOAQvNC1ZCLwHJyCXy79pLiOXtiRou8n6IswkOz0SeiGjzYis+q7TjqIVDLeb7hkY8ldcBlWV2Lszqj17qcU+B1uuYzdTTAEey3Gl13/BQ66sKS4h4eWAiV6XIElMfV/9Ck+8q+pJdfBXWi1nKWhGEP4+jwDN+FtQHAikZKN3NOUybYUy1wkHI6ZTncjjJRIoLPdLQ56c6UfH2aq4Yxz0SSeRpRvCdahtUjJiK+mHOeruhw/JhkJfvZt/eQF3XWmZRSextS9hymdpSfw09xPcMYJcSxvXjbB/tC7XGDMpdAMNRG/V5SoiDMI1wz+p1jnVYHI4mmGI0viXynD4mk9ViYpw64vGuIG/qGnvA9F+ku72+VM1SRTPgDTBdo81nZlzSdf62FpKqb+Toyr8dK4GpuDmAjNwcvQe0mvHoQ2h+ckghm4gizcCLiYCcgE6muhC3pgJ/hp3E0UaTqdUJEDpRJRjLQXVm16MfB8IRIMf/E6+n8SOSfAAOJw8c6Kft1qAAAAAElFTkSuQmCC" alt=""><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGoAAAAmCAYAAAAsuw6AAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkQ2NEMxMEFBRjMxRDExRThBMkVDRjVEREZBOUUxNDk2IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkQ2NEMxMEFCRjMxRDExRThBMkVDRjVEREZBOUUxNDk2Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RDY0QzEwQThGMzFEMTFFOEEyRUNGNURERkE5RTE0OTYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RDY0QzEwQTlGMzFEMTFFOEEyRUNGNURERkE5RTE0OTYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4n9TfwAAAE7klEQVR42uxaXYhVVRQ+d+5o2jXHsZoZRpuRtEQzCQLRrEbTGfNvTCNEkHkQgvRBIsLoB6KHnkYmevGhCJEhf0DB2ygilNIgDmYhqDU2/jQq6miTUzKa85O3teC7uFntc87e55x7MdgffIx3nb332Xd/e6291r6mcrmc5/DgI+WEckI5OKGcUA5OKAcnlBPKwQnlUFShniPOIQ6qY+Jvlvh7QN/5xKnEIb+5Ea8Re4ndxOsBY60kVhCHFVsp+uwN6NdAfFLMoYR4m7hb+V4TiMuU8UcQfyUe1ow5njibOIP4ONqm8A7mDeJlfKdOvCscLFQM7s/5482Qvnty5rhFPExcqhmHN9vJgH41Pu8vI1726deD5/m2DZo2OzVjNhFvWHyv9aZrXRLDm3iXzQ143pig5z9CnEfcR3xb7jXQr99in2c83kSfZ/cCxlTbSK/eBi8yxUjThnGEWkAsC3j+IsRMGp8QJ1m0f93SHhWbohw9pg1LY0xshfjM8TutvHwc8RViq8WYW3EuPUTM4AyrExtqLDZBt+GY3HYK8Zxie5RYn6BItcSZwsZnzxfEE8S/lfV+jFiOOXUWWih275eFrRXC1AoxTYXiUNNCPC3sXxHXCdtTFnMdTVxK/Fyx8TyrEhSKF/9hYTtIfCepF0QNffMwORVbiD8KW51lzNaFAl1mVWE531UFDnuxwlohhZKJwnmI9J1mp9XFnONNja08pA+nwAPKZy4hpisiL1Ce3QkpI0wwqEk+XkU0eZe4GonXZCQ4RRGqHKFDxSFl9w+GnGW2+Etjy4T0OSJCKNcyy/HvehENeIMdiznHKxqxOeSuJTYTd2JOvxDPEtuJawotFB/O1cJ2AH/P4PD0xHkwLsYi/KOxpUP68KLtF7bXlDRaxTfEWwl4/feG6Xgl8SXiduKGQgolPaQXu0WKlkc1xC0m0pobCb5FWUJ8QdRCbTb1TADeg7fY4H3TTWwr1BjiQmH7CTuyHGn10YSL35RBsSkxCp7dKWwt2NFq2OvCvOPiN5xDH8G7LhL7Q/pwwf1sIdLz2SL99jC5biH6PfG5HiL3R1gA3eF7x8CjPIS/aYp9qmiXTaDwlyH3U3AU6rUKCMLv3kh8QnPDk7hHNfp4WRUmlKccdxIyryjQhYY+w757Q86+tgKG37tIMk7gPZuJX0d1FhuhODwsijHxZYZFr8TzGluv4Tt/IP7s8+w48VSRz07deg8nHfpmEZ8Wtj9xVZLyKUrV8Rfh0B4MOIvWILaPxsaYhhpE4pzhnPlnBb7IfUbzLJuwCFxE808ml7AmfUotl8F3WavpdzVpoXQe0YSFyAivuIu0d4k4H2aJDFHiA8OQ0mEx7ywyMrmL9yUsFG+oNyz7XA/w+Eihr1Qser7Ia4cw/QpvI/7rduzyBBakBfWaKY57/70/PKaxxUWUX2D5EvqPJIViT5ghbO0+twZ5fKvJzhoj1iw51ChvET/UhMxUwPca1iQN2ZASoMQLv6uLmyny9dLHnoWnmMb6z7z71/Wceu4O6XMBIacG/UuwaBmcU3tQewwE3J9x0tCDWqfLpy2L+CXS3iHMrUOzc9PwdBZgl3i+AzXXANr1Kd81XyM1e/d/ste9oxlhvRJXVOOxvilcYQ0h4nThyu2ojRe6/9zyP4ETygnl4IRyQjk4oRycUE4oByeUgx3+FWAAXciSN87sWJ8AAAAASUVORK5CYII=" alt=""></a></td>
    </tr>
</table>
<table class="column">
    <tr>
    <td style="padding: 0 10px 10px; height: 38px; font-size: 16px; color: #fff;">Manažerský report ' . date('j. n. Y') . '<br>'.$reportConfig['note'].'</td>
    </tr>
</table>
</td>
    </tr>
</table>
</td>
</tr>';

$htmlMail .= '
<!-- POČET PROJEKTŮ A FÁZÍ -->';

    $htmlPhases = '';
    $phasesPreviousSum = 0;
    $phasesSum = 0;
    foreach ($phasesData as $phaseData) {
        $phasesSum += $phaseData['countProjektu'];
    }
    foreach (array_reverse($phasesData) as $phaseData) {
        $htmlRozdil = "";
        if(!is_null($phasesDataPrevious) && is_array($phasesDataPrevious)){
            $key = array_search("$phaseData[name]", array_column($phasesDataPrevious, 'name'));
            $countProjektuPrevious = $phasesDataPrevious[$key]['countProjektu'];
            $phasesPreviousSum += $countProjektuPrevious;
            $htmlRozdil = "<td width='23%' style='font-size: 12px; color: #868E96; text-align: right' valign='baseline'> ". formatDifference($phaseData['countProjektu'], $countProjektuPrevious) ."</td>";
        }
        $htmlPhases .= '
    <tr>
      <td align="right" style="padding: 4px 6px 4px 0;">
        <table width="' . round(2 * 100 * $phaseData['countProjektu'] / $phasesSum) . '%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
          <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">   
              <td bgcolor="' . getColorForPhase($phaseData['name']) . '">&nbsp;</td>
            </table>
          </td>
        </table>
      </td>
      <td width="62%">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <td valign="baseline">' . $phaseData['name'] . '</td>
          '.$htmlRozdil.'
          <td width="15%" valign="baseline" style="text-align: right">' . $phaseData['countProjektu'] . '</td>
          <td width="15%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">' . round(100 * $phaseData['countProjektu'] / $phasesSum, 1) . '%</td>
        </table>
      </td>
    </tr>
    ';
    }
    $htmlMail .= '
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">' . $phasesSum . ' projektů <small>'.formatDifference($phasesSum, $phasesPreviousSum).'</small></div>
  <br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
  '.$htmlPhases.'
  </table>
  <br>
  <div style="padding-top: 16px; font-size: 14px; border-top: 1px solid #eee;">Počet změn fáze za poslední týden: ' . countPhaseChangesInInterval(7) . '<br>
  ' . countActiveProjects(7) . ' projektů přidáno za poslední týden<br>
  ' . countActiveProjects(30) . ' projektů přidáno za poslední měsíc
  </div>
</div>
</td>
</tr>

<!-- HODNOTA PROJEKTŮ -->';
    $htmlPrices = '';
    $pricesPreviousSum = 0;
    $pricesSum = 0;
    foreach ($phasesData as $phaseData) {
        $price = round($phasePrice[array_search($phaseData['name'], array_column($phasePrice, 'phaseName'))]['price']);
        $pricesSum += $price;
    }
    foreach (array_reverse($phasesData) as $phaseData) {
        $price = round($phasePrice[array_search($phaseData['name'], array_column($phasePrice, 'phaseName'))]['price']);
        $htmlRozdil = "";
        if(!is_null($pricesDataPrevious) && is_array($pricesDataPrevious)){
            $key = array_search("$phaseData[name]", array_column($pricesDataPrevious, 'phaseName'));
            $countProjektuPrevious = $pricesDataPrevious[$key]['price'];
            $pricesPreviousSum += $countProjektuPrevious;
            $htmlRozdil = "<td width='20%' style='font-size: 12px; color: #868E96; text-align: right' valign='baseline'> ". formatDifference($price, $countProjektuPrevious) ."</td>";
        }
        $htmlPrices .= '
    <tr>
      <td align="right" style="padding: 4px 6px 4px 0;">
        <table width="' . round(100 * $price / $pricesSum) . '%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
          <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">   
              <td bgcolor="' . getColorForPhase($phaseData['name']) . '">&nbsp;</td>
            </table>
          </td>
        </table>
      </td>
      <td width="70%">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <td valign="baseline">' . $phaseData['name'] . '</td>
          '.$htmlRozdil.'
          <td width="40%" valign="baseline" style="text-align: right">' . number_format($price, 0, ",", " ") . ' Kč</td>
          <td width="10%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">' . round(100 * $price / $pricesSum) . '%</td>
        </table>
      </td>
    </tr>
    ';
    }
    $htmlMail .= '
<tr>
<td>
<div class="card">
<div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Celková hodnota projektů: ' . number_format($pricesSum, 0, ",", " ") . ' Kč <small>'.formatDifference($pricesSum, $pricesPreviousSum).'</small></div>
<br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
  '.$htmlPrices.'
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
    <br/>';
    $deadlines = getTermsForReports(20, 7);
    if (!empty($deadlines)) {
        $htmlMail .= '
                <table class="table-striped" style="font-size: smaller">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>ID</th>
                        <th>Projekt</th>
                        <th>Typ termínu</th>
                    </tr>
                    </thead>
                    ';
        foreach ($deadlines as $deadline) {
            $htmlMail .= "<tr><td style='padding: 5px;'>" . date("d. m. Y", strtotime($deadline["value"])) . "</td><td style='padding: 5px;'>$deadline[idProject]</td><td style='padding: 5px;'>$deadline[projectName]</td><td style='padding: 5px;'>$deadline[deadlineName]</td></tr>";
        }
        $htmlMail .= '</table>';
    } else {
        $htmlMail .= "Neblíží se žádné termíny.";
    }
    $htmlMail .= '
                
</td>
</tr>

<!-- KONCE ZÁRUKY -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Bližící se konce záruky</div>
    <br/>';

    $warranties = getUpcomingWarranties();
    if (!empty($warranties)) {
        $htmlMail .= '
        <table class="table-striped" style="font-size: smaller">
                    <tr>
                        <th>Datum</th>
                        <th>ID</th>
                        <th>Projekt</th>
                        <th>Typ záruky</th>
                    </tr>
        ';
        foreach ($warranties as $warranty) {
            $htmlMail .= "<tr><td style='padding: 5px;'>" . date("d. m. Y", strtotime($warranty["value"])) . "</td><td style='padding: 5px;'>$warranty[idProject]</td><td style='padding: 5px;'>$warranty[projectName]</td><td style='padding: 5px;'>$warranty[deadlineName]</td></tr>";
        }
        $htmlMail .= '</table>';
    } else {
        $htmlMail .= "Neblíží se žádné konce záruky.";
    }

    $htmlMail .= '
</td>
</tr>

<!-- POČET PŘIHLÁŠENÍ -->
<tr>
<td>
<div class="card">
Počet přihlášení za poslední týden: ' . count($lastLogins) . ' <small>'.formatDifference(count($lastLogins), count($lastLoginsBefore)).'</small>
</div>
</td>
</tr>


<!-- ROZLOŽENÍ PROJEKTŮ MEZI EDITORY -->
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">
  Rozložení projektů mezi editory
  <div style="font-size: 12px; padding-top: 4px">
  <b style="color: '.getColorForPhase("Námět").'">⬤</b> Námět
  <b style="color: '.getColorForPhase("Záměr").'">⬤</b> Záměr 
  <b style="color: '.getColorForPhase("V přípravě").'">⬤</b> V přípravě 
  <b style="color: '.getColorForPhase("Připraveno").'">⬤</b> Připraveno 
  <b style="color: '.getColorForPhase("V realizaci").'">⬤</b> V realizaci 
  <b style="color: '.getColorForPhase("Zrealizováno").'">⬤</b> Zrealizováno 
  </div>
  </div>
  <br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">';
    foreach ($editor2Project as $editorStats) {
        $htmlRozdil = "";
        if(!is_null($editorDataPrevious) && is_array($editorDataPrevious)){
            $key = array_search("$editorStats[editor]", array_column($editorDataPrevious, 'editor'));
            $countProjektuPrevious = $editorDataPrevious[$key]['countProjektu'];
            $htmlRozdil = formatDifference($editorStats['countProjektu'], $countProjektuPrevious);
        }
        $htmlMail .= '
    <tr>
      <td align="right" style="padding: 4px 16px 4px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td style="padding: 16px 0 8px 0;">' . getUserAll($editorStats['editor'])[0]['name'] . '</td>
    </tr>
    <tr>
      <td>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">   
          <td width="' . round(100 * $editorStats['countProjektu'] / 60, 1) . '%"> <!-- 6.45 / 10.572 -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0">';
        $editorPhases = countPhasesToEditor($editorStats['editor']);
        foreach ($editorPhases as $phase) {
            if ($phase['phaseCount'] != 0) $htmlMail .= '<td width="' . (100 * $phase['phaseCount'] / 60) . '%" bgcolor="' . getColorForPhase($phase['phaseName']) . '" style="color: #ddd; font-size: small">' . $phase['phaseCount'] . '</td>';
        }
        $htmlMail .= '</table>
          </td>
          <td style="padding-left: 8px; font-size: 12px; color: #868E96;">' . $editorStats['countProjektu'] . ' projektů '.$htmlRozdil.'</td>
         
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

<!-- POČET PŘIHLÁŠENÍ A AKCÍ EDITORŮ -->
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">
  Počet přihlášení a provedených akcí editorů za poslední týden
  </div>
  <br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">';
    foreach ($editor2Project as $editorStats) {
        $htmlRozdil = "";
        if(!is_null($editorDataPrevious) && is_array($editorDataPrevious)){
            $key = array_search("$editorStats[editor]", array_column($editorDataPrevious, 'editor'));
            $countProjektuPrevious = $editorDataPrevious[$key]['countProjektu'];
            $htmlRozdil = formatDifference($editorStats['countProjektu'], $countProjektuPrevious);
        }
        $htmlMail .= '
    <tr>
      <td style="padding: 16px 0 8px 0;"><b>' . getUserAll($editorStats['editor'])[0]['name'] . '</b></td>
    </tr>
    <tr>
      <td>Počet přihlášení: '.count(array_keys(array_column($lastLogins, 'username'), $editorStats["editor"])).'</td><td>Počet provedených akcí: '.countUserActionsInInterval($editorStats["editor"], 7).'</td>
    </tr>
    ';
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
    return $htmlMail;
}

// EDITORSKY REPORT
function prepareEditorReport(string $reportType, array $ouArr, string $email, string $name, bool $saveData = FALSE, int $reportConfigId, $username)
{
    // session_start();
    $_SESSION['username'] = $username;
    $_SESSION['global_filtr'] = 'my';
    $lastChanges = getHistoryOfUserProjects($username);
    $dataDashboard = getDashboardStatsNonGraph();
    $phasesData = getPieGraphPhase2Projects();
    $phasePrice = countSumPricesForPhases(NULL, $username);
    $editor2Project = getStatsEditor2Projects();
    $lastLogins = getLastLogins(1000, 7, NULL);
    $lastLoginsBefore = getLastLogins(1000, 7, NULL, 7);

    $previousData = is_array(getPreviousEditorReportData($username)) ? getPreviousEditorReportData($username) : NULL;
    if(!is_null($previousData)){
        $phasesDataPrevious = json_decode($previousData['projectsPerPhases'],true);
        $pricesDataPrevious = json_decode($previousData['pricesPerPhases'],true);
        $editorDataPrevious = json_decode($previousData['projectsEditorsBreakdown'],true);
    }
    if($saveData){
        saveReportData($phasePrice, $phasesData, $editor2Project, $reportConfigId, $username);
    }

    require_once __DIR__ . "/../html/conf/config.inc";
    require_once SYSTEMINCLUDES . "function.php";
// require_once SYSTEMINCLUDES."authenticateUser.php";
    require_once SYSTEMINCLUDES . "autoLoader.php";

//overUzivatele($pristup_zakazan);
    $htmlMail = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="x-apple-disable-message-reformatting" />
<title>ADMIS weekly editor report</title>
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
	    padding: 0 1px;
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
        overflow: hidden;
    }
    .task-card {
        box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.14);
        border: 0;
        float: left;
        padding: 10px;
        margin: 10px;
        border-radius: 6px;
        color: #333333;
        background: #fff;
        max-width: 240px; 
        min-width: 200px;
        position: relative;
        word-wrap: break-word;
        font-size: .875rem;
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
    <td style="padding: 0 62px 10px"><a href="https://admis.fd.cvut.cz"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACkAAAAmCAYAAABZNrIjAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkFCMThDMUY2RjMxRDExRThBMTgyRkJGN0JFRTY4MEZEIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkFCMThDMUY3RjMxRDExRThBMTgyRkJGN0JFRTY4MEZEIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QUIxOEMxRjRGMzFEMTFFOEExODJGQkY3QkVFNjgwRkQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QUIxOEMxRjVGMzFEMTFFOEExODJGQkY3QkVFNjgwRkQiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5QeF8tAAACcElEQVR42uyYz0tUURTH33NEC2RAXbSwKCuIwH7QIiJoUsisNhX0YxetWlghQYv+gGjlLiUioRIxSKWFRdEmqCgIKkqqTRBi5SJqEf3AsMbPgSM8Hu/NvPvuXGeGvPDhzbz7Y75z7r3nnHv9fD7vVXqp8aqgVIXI2grR0QArYBU0wwS8gdlyiZTZWwlbYRtsgbWwDDLa5hmch3H54i/QxsnCduiCDlgPdaE2IsSHd7Bx3oquLdmggo5Ap1oqrjyC6zCgYmddr0mxwnE4DMsTtP8Mu6HF9caRcfZBD7Qn9Brz0ytWmynUx1ZkPRyFs7DBoF8/rIE9Lv2kWOAQvNC1ZCLwHJyCXy79pLiOXtiRou8n6IswkOz0SeiGjzYis+q7TjqIVDLeb7hkY8ldcBlWV2Lszqj17qcU+B1uuYzdTTAEey3Gl13/BQ66sKS4h4eWAiV6XIElMfV/9Ck+8q+pJdfBXWi1nKWhGEP4+jwDN+FtQHAikZKN3NOUybYUy1wkHI6ZTncjjJRIoLPdLQ56c6UfH2aq4Yxz0SSeRpRvCdahtUjJiK+mHOeruhw/JhkJfvZt/eQF3XWmZRSextS9hymdpSfw09xPcMYJcSxvXjbB/tC7XGDMpdAMNRG/V5SoiDMI1wz+p1jnVYHI4mmGI0viXynD4mk9ViYpw64vGuIG/qGnvA9F+ku72+VM1SRTPgDTBdo81nZlzSdf62FpKqb+Toyr8dK4GpuDmAjNwcvQe0mvHoQ2h+ckghm4gizcCLiYCcgE6muhC3pgJ/hp3E0UaTqdUJEDpRJRjLQXVm16MfB8IRIMf/E6+n8SOSfAAOJw8c6Kft1qAAAAAElFTkSuQmCC" alt=""><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGoAAAAmCAYAAAAsuw6AAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkQ2NEMxMEFBRjMxRDExRThBMkVDRjVEREZBOUUxNDk2IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkQ2NEMxMEFCRjMxRDExRThBMkVDRjVEREZBOUUxNDk2Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RDY0QzEwQThGMzFEMTFFOEEyRUNGNURERkE5RTE0OTYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RDY0QzEwQTlGMzFEMTFFOEEyRUNGNURERkE5RTE0OTYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4n9TfwAAAE7klEQVR42uxaXYhVVRQ+d+5o2jXHsZoZRpuRtEQzCQLRrEbTGfNvTCNEkHkQgvRBIsLoB6KHnkYmevGhCJEhf0DB2ygilNIgDmYhqDU2/jQq6miTUzKa85O3teC7uFntc87e55x7MdgffIx3nb332Xd/e6291r6mcrmc5/DgI+WEckI5OKGcUA5OKAcnlBPKwQnlUFShniPOIQ6qY+Jvlvh7QN/5xKnEIb+5Ea8Re4ndxOsBY60kVhCHFVsp+uwN6NdAfFLMoYR4m7hb+V4TiMuU8UcQfyUe1ow5njibOIP4ONqm8A7mDeJlfKdOvCscLFQM7s/5482Qvnty5rhFPExcqhmHN9vJgH41Pu8vI1726deD5/m2DZo2OzVjNhFvWHyv9aZrXRLDm3iXzQ143pig5z9CnEfcR3xb7jXQr99in2c83kSfZ/cCxlTbSK/eBi8yxUjThnGEWkAsC3j+IsRMGp8QJ1m0f93SHhWbohw9pg1LY0xshfjM8TutvHwc8RViq8WYW3EuPUTM4AyrExtqLDZBt+GY3HYK8Zxie5RYn6BItcSZwsZnzxfEE8S/lfV+jFiOOXUWWih275eFrRXC1AoxTYXiUNNCPC3sXxHXCdtTFnMdTVxK/Fyx8TyrEhSKF/9hYTtIfCepF0QNffMwORVbiD8KW51lzNaFAl1mVWE531UFDnuxwlohhZKJwnmI9J1mp9XFnONNja08pA+nwAPKZy4hpisiL1Ce3QkpI0wwqEk+XkU0eZe4GonXZCQ4RRGqHKFDxSFl9w+GnGW2+Etjy4T0OSJCKNcyy/HvehENeIMdiznHKxqxOeSuJTYTd2JOvxDPEtuJawotFB/O1cJ2AH/P4PD0xHkwLsYi/KOxpUP68KLtF7bXlDRaxTfEWwl4/feG6Xgl8SXiduKGQgolPaQXu0WKlkc1xC0m0pobCb5FWUJ8QdRCbTb1TADeg7fY4H3TTWwr1BjiQmH7CTuyHGn10YSL35RBsSkxCp7dKWwt2NFq2OvCvOPiN5xDH8G7LhL7Q/pwwf1sIdLz2SL99jC5biH6PfG5HiL3R1gA3eF7x8CjPIS/aYp9qmiXTaDwlyH3U3AU6rUKCMLv3kh8QnPDk7hHNfp4WRUmlKccdxIyryjQhYY+w757Q86+tgKG37tIMk7gPZuJX0d1FhuhODwsijHxZYZFr8TzGluv4Tt/IP7s8+w48VSRz07deg8nHfpmEZ8Wtj9xVZLyKUrV8Rfh0B4MOIvWILaPxsaYhhpE4pzhnPlnBb7IfUbzLJuwCFxE808ml7AmfUotl8F3WavpdzVpoXQe0YSFyAivuIu0d4k4H2aJDFHiA8OQ0mEx7ywyMrmL9yUsFG+oNyz7XA/w+Eihr1Qser7Ia4cw/QpvI/7rduzyBBakBfWaKY57/70/PKaxxUWUX2D5EvqPJIViT5ghbO0+twZ5fKvJzhoj1iw51ChvET/UhMxUwPca1iQN2ZASoMQLv6uLmyny9dLHnoWnmMb6z7z71/Wceu4O6XMBIacG/UuwaBmcU3tQewwE3J9x0tCDWqfLpy2L+CXS3iHMrUOzc9PwdBZgl3i+AzXXANr1Kd81XyM1e/d/ste9oxlhvRJXVOOxvilcYQ0h4nThyu2ojRe6/9zyP4ETygnl4IRyQjk4oRycUE4oByeUgx3+FWAAXciSN87sWJ8AAAAASUVORK5CYII=" alt=""></a></td>
    </tr>
</table>
<table class="column">
    <tr>
    <td style="padding: 0 10px 10px; height: 38px; font-size: 16px; color: #fff;">Editorský report ' . date('j. n. Y') . '<br>'.getUserAll($username)[0]['name'].'</td>
    </tr>
</table>
</td>
    </tr>
</table>
</td>
</tr>
';

    $htmlMail .= '
<!-- POČET PROJEKTŮ A FÁZÍ -->';

    $htmlPhases = '';
    $phasesPreviousSum = 0;
    $sumOfAllProjects = 0;
    foreach ($phasesData as $phaseData) {
        $sumOfAllProjects += $phaseData['countProjektu'];
    }
    foreach (array_reverse($phasesData) as $phaseData) {
        $htmlRozdil = "";
        if (isset($phasesDataPrevious)) {
            if (!is_null($phasesDataPrevious) && is_array($phasesDataPrevious) && is_numeric(array_search("$phaseData[name]", array_column($phasesDataPrevious, 'name')))) {
                $key = array_search("$phaseData[name]", array_column($phasesDataPrevious, 'name'));
                $countProjektuPrevious = $phasesDataPrevious[$key]['countProjektu'];
                $phasesPreviousSum += $countProjektuPrevious;
                $htmlRozdil = "<td width='23%' style='font-size: 12px; color: #868E96; text-align: right' valign='baseline'> " . formatDifference($phaseData['countProjektu'], $countProjektuPrevious) . "</td>";
            } else {
                $htmlRozdil = "<td width='20%' style='font-size: 12px; color: #868E96; text-align: right' valign='baseline'>-</td>";
            }
        } else {
            $htmlRozdil = "";
        }
        $htmlPhases .= '
    <tr>
      <td align="right" style="padding: 4px 6px 4px 0;">
        <table width="' . round(100 * $phaseData['countProjektu'] / $sumOfAllProjects) . '%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
          <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">   
              <td bgcolor="' . getColorForPhase($phaseData['name']) . '">&nbsp;</td>
            </table>
          </td>
        </table>
      </td>
      <td width="62%">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <td valign="baseline">' . $phaseData['name'] . '</td>
          '.$htmlRozdil.'
          <td width="15%" valign="baseline" style="text-align: right">' . $phaseData['countProjektu'] . '</td>
          <td width="15%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">' . round(100 * $phaseData['countProjektu'] / $sumOfAllProjects, 1) . '%</td>
        </table>
      </td>
    </tr>
    ';
    }
    $htmlMail .= '
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">' . $sumOfAllProjects . ' projektů <small>'.formatDifference($sumOfAllProjects, $phasesPreviousSum).'</small></div>
  <br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
  '.$htmlPhases.'
  </table>
</div>
</td>
</tr>

<!-- HODNOTA PROJEKTŮ -->';
    $htmlPrices = '';
    $pricesPreviousSum = 0;
    $pricesOfAllProjects = 0;
    foreach ($phasePrice as $phase) {
        $pricesOfAllProjects += $phase['price'];
    }
    foreach ($phasePrice as $phaseData) {
        $price = round($phaseData['price']);
        $htmlRozdil = "";
        if (isset($pricesDataPrevious)) {
            if (!is_null($pricesDataPrevious) && is_array($pricesDataPrevious) && is_numeric(array_search("$phaseData[phaseName]", array_column($pricesDataPrevious, 'phaseName')))) {
                $key = array_search("$phaseData[phaseName]", array_column($pricesDataPrevious, 'phaseName'));
                $countProjektuPrevious = $pricesDataPrevious[$key]['price'];
                $pricesPreviousSum += $countProjektuPrevious;
                $htmlRozdil = "<td width='20%' style='font-size: 12px; color: #868E96; text-align: right' valign='baseline'> " . formatDifference($price, $countProjektuPrevious) . "</td>";
            } else {
                $htmlRozdil = "<td width='20%' style='font-size: 12px; color: #868E96; text-align: right' valign='baseline'>-</td>";
            }
        } else {
            $htmlRozdil = "";
        }
        $htmlPrices .= '
    <tr>
      <td align="right" style="padding: 4px 6px 4px 0;">
        <table width="' . round(100 * $price / $pricesOfAllProjects) . '%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
          <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">   
              <td bgcolor="' . getColorForPhase($phaseData['phaseName']) . '">&nbsp;</td>
            </table>
          </td>
        </table>
      </td>
      <td width="70%">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <td valign="baseline">' . $phaseData['phaseName'] . '</td>
          '.$htmlRozdil.'
          <td width="40%" valign="baseline" style="text-align: right">' . number_format($price, 0, ",", " ") . ' Kč</td>
          <td width="10%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">' . round(100 * $price / $pricesOfAllProjects) . '%</td>
        </table>
      </td>
    </tr>
    ';
    }
    $htmlMail .= '
<tr>
<td>
<div class="card">
<div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Celková hodnota projektů: ' . number_format($pricesOfAllProjects, 0, ",", " ") . ' Kč <small>'.formatDifference($pricesOfAllProjects, $pricesPreviousSum).'</small></div>
<br/>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
  '.$htmlPrices.'
  </table>
  </div>
</div>
</td>
</tr>


<!-- TERMÍNY V PŘÍŠTÍM TÝDNU -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Termíny v tomto týdnu na mých projektech</div>
    <br/>';
    $deadlines = getTermsForReports(20, 7);
    if (!empty($deadlines)) {
        $htmlMail .= '
                <table class="table-striped" style="font-size: smaller">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>ID</th>
                        <th>Projekt</th>
                        <th>Typ termínu</th>
                    </tr>
                    </thead>
                    ';
        foreach ($deadlines as $deadline) {
            $htmlMail .= "<tr><td style='padding: 5px;'>" . date("d. m. Y", strtotime($deadline["value"])) . "</td><td style='padding: 5px;'>$deadline[idProject]</td><td style='padding: 5px;'>$deadline[projectName]</td><td style='padding: 5px;'>$deadline[deadlineName]</td></tr>";
        }
        $htmlMail .= '</table>';
    } else {
        $htmlMail .= "Neblíží se žádné termíny.";
    }
    $htmlMail .= '
                
</td>
</tr>

<!-- KONCE ZÁRUKY -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Bližící se konce záruky na mých projektech</div>
    <br/>';

    $warranties = getUpcomingWarranties(50, 30, $username);
    if (!empty($warranties)) {
        $htmlMail .= '
        <table class="table-striped" style="font-size: smaller">
                    <tr>
                        <th>Datum</th>
                        <th>ID</th>
                        <th>Projekt</th>
                        <th>Typ záruky</th>
                    </tr>
        ';
        foreach ($warranties as $warranty) {
            $htmlMail .= "<tr><td style='padding: 5px;'>" . date("d. m. Y", strtotime($warranty["value"])) . "</td><td style='padding: 5px;'>$warranty[idProject]</td><td style='padding: 5px;'>$warranty[projectName]</td><td style='padding: 5px;'>$warranty[deadlineName]</td></tr>";
        }
        $htmlMail .= '</table>';
    } else {
        $htmlMail .= "Neblíží se žádné konce záruky.";
    }
    $htmlMail .= '
</td>
</tr>

<!-- MOJE UKOLY -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Aktivní úkoly na mých projektech</div>
    <br/>';

    $tasks = getTasksForUser($username);
    if (!empty($tasks)) {
        foreach ($tasks as $task) {
            if ($task['deadline']) {
                $deadline =  "<br>Termín: ".date("d.m.Y", strtotime($task['deadline']));
            } else {
                $deadline = "";
            }
            $htmlMail .= '
            <div class="task-card" style="font-size: smaller">
                <b>'.$task['name'].'</b><br>
                '.$task['description'].'<hr>
                '.$task['projectName'].' (ID '.$task['idProject'].')
                '.$deadline.'<br>
                Stav řešení: <span style="color: '.$task['statusColor'].'">⬤</span> '.$task['status'].'
            </div>
        ';
        }
    } else {
        $htmlMail .= "Žádné aktivní úkoly na projektech.";
    }
    $htmlMail .= '
</td>
</tr>

<!-- POSLEDNÍ ZMĚNY NA PROJEKTECH -->
<tr>
<td>
<div class="card">
    <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Poslední změny na mých projektech</div>
    <br/>';
    if (!empty($lastChanges)) {
        $htmlMail .= '
                <table class="table-striped" style="font-size: smaller">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>ID</th>
                        <th>Projekt</th>
                        <th>Typ změny</th>
                        <th>Uživatel</th>
                    </tr>
                    </thead>
                    ';
        foreach ($lastChanges as $change) {
            $htmlMail .= "<tr><td style='padding: 5px;'>" . date("d. m. Y", strtotime($change["created"])) . "</td><td style='padding: 5px;'>$change[idProject]</td><td style='padding: 5px;'>$change[projectName]</td><td style='padding: 5px;'>$change[actionName]</td><td style='padding: 5px;'>$change[nameUser]</td></tr>";
        }
        $htmlMail .= '</table>';
    } else {
        $htmlMail .= "Žádné změny nebyly nalezeny.";
    }
    $htmlMail .= '
                
</td>
</tr>


<!-- FOOTER SECTION -->

</table>

</center>

</body>
</html>
';
    return $htmlMail;
}

// echo $htmlMail;
//$htmlMail = prepareReport()
//echo sendMail('Admis týdenní report', $htmlMail, 'nemas html', ['hnykpetr@fd.cvut.cz'], 'noreply@fd.cvut.cz', true );
// echo sendMail('Admis týdenní report', $htmlMail, 'nemas html', ['ales.cermak@ksus.cz', 'jan.fidler@ksus.cz', 'petr.nadvornik@ksus.cz', 'hnykpetr@fd.cvut.cz', 'kocian@fd.cvut.cz', 'ondra@fd.cvut.cz'], 'noreply@fd.cvut.cz', true );