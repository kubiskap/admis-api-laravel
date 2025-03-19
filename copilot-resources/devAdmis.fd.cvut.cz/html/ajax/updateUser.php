<?php
/**
 * Created by PhpStorm.
 * User: petros
 * Date: 06.04.2022
 * Time: 17:35
 */
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if(isset($_POST['token']) && validateHash($_POST['token'], date('H')) && isset($_POST['username']) ) {
    if ($_POST['username']!='') {
        $valuesArr = [
            'username' => $_POST['username'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'idOu' => $_POST['idOu'],
            'idRoleType' => $_POST['idRoleType'],
        ];
        return updateUsersInfo($_POST['username'], $valuesArr);
    }
//    } else {
//        echo createUser($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['idOu'], $_POST['idRoleType']);
//    }
}