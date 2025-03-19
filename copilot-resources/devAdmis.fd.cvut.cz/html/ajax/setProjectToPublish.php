<?php

require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";

if(isset($_GET['idProject']) && isset($_SESSION['username']) && is_numeric($_GET['idProject']) ){
    if($_SESSION['role'] != 'view') {
        setProjectToPublish($_GET['idProject'], $_SESSION['username']);
    }
}
