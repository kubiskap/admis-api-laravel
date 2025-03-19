<?php
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 24.07.2018
 * Time: 15:21
 */
require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
overUzivatele($pristup_zakazan);
require_once SYSTEMINCLUDES . "function.php";
if (isset($_POST['idProject'])) {
    logPost($_POST);
    try {
        $editedPost = Array();
        $editedPost['idProject'] = $_POST['idProject'];
        unset($_POST['idProject']);
        $editedPost['priorityAtts'] = json_encode($_POST);
        //print_r( json_decode($editedPost['priorityAtts']));
        $priority = new Priority($editedPost['idProject'], json_decode($editedPost['priorityAtts'],true));
        if($priority->insert()){
            $priorityScore = round($priority->getResult(),2);
            print_r(json_encode(['maxScore'=> $priority->getMaxScore(),'projectId'=> $editedPost['idProject'], 'priorityScore' => round($priority->getResult(),2), 'correctionValue' => round(($priority->getCorrectionValue() * 1000),2)]));
        }
    } catch (Exception $e) {
        $stmt = $dbh->getDbLink()->rollBack();
        $lastId = 'Chyba pri volani funkce insertProject' . $e;
        writeError2Log('submit calling insertProject while inserting priority', $_POST, $e);
    }
}

?>