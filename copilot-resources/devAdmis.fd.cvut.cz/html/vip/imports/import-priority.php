<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 25.07.2018
 * Time: 10:44
 */
require_once __DIR__."/../../conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
overUzivatele($pristup_zakazan);


ini_set('auto_detect_line_endings', TRUE);
$handle = fopen('priority-kukura.csv', 'r');
$projekty = [];
while (($data = fgetcsv($handle, null, ";")) !== FALSE) {

   if (is_numeric($data[0])) {
       $idProject = (int)$data[0];
       if (in_array($data[0], $projekty)){
           echo "<b style='color: red'>DUPLICITA PROJEKTU ID </b>".$idProject."<br>";
       } else {
           $projekty[] = $data[0];
       }
       
        echo "Projekt: " . $data[0] . "<br>
    dopravni_zatizeni: " . $data[4] . "<br>
    spolufinancovani: " . $data[5] . "<br>
    dopravni_vyznam: " . $data[6] . "<br>
    technicky_stav: " . $data[7] . "<br>
    stavebni_stav: " . $data[8] . "<br>
    zivotni_prostred: " . $data[9] . "<br>
    regionalni_vyznam: " . $data[10] . "<br>
    jedina_pristupova_cesta: " . $data[11] . "<br>
    stav_pripravy: " . $data[12] . "<br>
    hromadna_doprava: " . $data[13] . "<br>
    nehodova_lokalita: " . $data[14] . "<br><br>
";
        $json = '{
        "dopravni_zatizeni":"'. $data[4] .'",
        "spolufinancovani":"'. $data[5] .'",
        "dopravni_vyznam":"'. $data[6] .'",
        "technicky_stav":"'. $data[7] .'",
        "stavebni_stav":"'. $data[8] .'",
        "zivotni_prostred":"'. $data[9] .'",
        "regionalni_vyznam":"'. $data[10] .'",
        "jedina_pristupova_cesta":"'. $data[11] .'",
        "stav_pripravy":"'. $data[12] .'",
        "hromadna_doprava":"'. $data[13] .'",
        "nehodova_lokalita":"'. $data[14] .'"
        }';
       echo "ID projektu: ".$idProject;
       echo "<br>";
       echo "Priority JSON: ". $json;
       echo "<br>";
       echo "<br>";
        
        

       $dbh = new DatabaseConnector();
       $stmt = $dbh->getDbLink()->prepare("UPDATE `projects` SET `priorityAtts`=:priorities WHERE `idProject` = :idProject ");
       $stmt->bindParam(':priorities', $json, PDO::PARAM_STR);
       $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
       // $stmt->execute();
    }

    /*
    if (is_numeric($hotovo)) {

        if (!insertDeadlines2Project(explode(":", $data[15])[1], 24, $data[8], NULL, $dbh)) {
            throw new Exception('Chyba pri vytvareni deadlinu');
        }
        if (!insertDeadlines2Project(explode(":", $data[15])[1], 25, getWarrantyDeadline(new DateTime($data[8]), $data[13]), NULL, $dbh)) {
            throw new Exception('Chyba pri vytvareni deadlinu zaruk');
        }
        if (!insertDeadlines2Project(explode(":", $data[15])[1], 26, getWarrantyDeadline(new DateTime($data[8]), $data[12]), NULL, $dbh)) {
            throw new Exception('Chyba pri vytvareni deadlinu zaruk');
        }

        addWarrantyPeriodsToProject((int)$hotovo, (int)$data[13], (int)$data[12]);
        echo "import ok<br><br>";
    }
    */


}
ini_set('auto_detect_line_endings', FALSE);


?>