<?php

require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";

if(isset($_GET['idProject']) && isset($_SESSION['username']) && is_numeric($_GET['idProject']) && validateHash($_GET['token'], $_GET['idProject'])){
    if($_SESSION['role'] != 'view') {
        if(isset($_GET['currentState']) && $_GET['currentState'] ){
            echo getProjectPublishState($_GET['idProject']);
        }
        if(isset($_GET['setState'])){
            if($_GET['setState']){
                echo setProjectToPublish($_GET['idProject'], $_SESSION['username'], true);
            }
            if(!$_GET['setState']){
                echo setProjectToPublish($_GET['idProject'], $_SESSION['username'], false);
            }
        }

    }
}
