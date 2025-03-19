<?php
/**
 * Created by PhpStorm.
 * User: ondra
 * Date: 13.11.2019
 * Time: 9:53
 */

require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
require_once __DIR__ . "/../mail/newTaskOnProject.php";

overUzivatele($pristup_zakazan);
require_once SYSTEMINCLUDES . "function.php";

if(isset($_POST) && isset($_POST['idTaskStatus']) && isset($_POST['idProject']) && isset($_POST['name'])){

    if (empty($_POST['idTask'])) {
        $result = insertTask($_POST);
        echo $result;
        if ($result) {
            $report = prepareNewTaskOnProjectMail($result);
            $user = getTaskDetails($result)['editor'];
            sendMail('ADMIS nový úkol', $report, 'Obsah dostupný pouze v HTML podobě.', [getUserAll($user)[0]['email']], 'noreply@fd.cvut.cz', true);
        }
    } else {
        echo updateTask($_POST);
    }

}