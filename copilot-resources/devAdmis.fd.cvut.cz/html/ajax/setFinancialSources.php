<?php
/**
 * Created by PhpStorm.
 * User: Pham Son Tung
 * Date: 09.08.2018
 * Time: 9:40
 */

require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if(isset($_POST) && $_POST['idProject'] && (isset($_POST['idFinSource']) OR isset($_POST['idFinSourcePD']))) {
    $allowedKeys = ['idProject', 'idFinSource', 'idFinSourcePD'];
    $filteredArray = array_intersect_key($_POST, array_flip($allowedKeys));
    echo Project::insertProject($filteredArray, 24);
}