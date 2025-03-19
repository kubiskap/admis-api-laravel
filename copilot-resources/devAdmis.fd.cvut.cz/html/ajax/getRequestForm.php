<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_POST['idRequestType']) && is_numeric($_POST['idRequestType']) && file_exists('../parts/zadanky/'.$_POST['idRequestType'].'.inc')) {
   // $project = getProjectById($_POST['idProject'])[0];
    $project = new Project($_POST['idProject']);
    $price = $project->getPricesByType(13) ;
    $project = $project->baseInformation;
    echo include '../parts/zadanky/'.$_POST['idRequestType'].'.inc';
}
?>