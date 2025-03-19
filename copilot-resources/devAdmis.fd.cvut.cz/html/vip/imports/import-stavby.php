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
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
overUzivatele($pristup_zakazan);


ini_set('auto_detect_line_endings', TRUE);
$handle = fopen('stavby.csv', 'r');
while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
    $hotovo = explode(":", $data[15])[1];
    if (is_numeric($hotovo)) {
        echo "Projekt: " . $data[0] . "<br>
    dokončení: " . $data[8] . "<br>
    záruka stavební: " . $data[12] . "<br>
    záruka technologická: " . $data[13] . "<br>
    ID projektu: " . explode(":", $data[15])[1];
        echo "<br>";
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