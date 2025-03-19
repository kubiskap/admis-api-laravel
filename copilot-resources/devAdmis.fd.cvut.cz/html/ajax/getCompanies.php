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
$idCompanyType = (isset($_GET['idCompanyType']) && in_array($_GET['idCompanyType'], [1,2,3])) ? $_GET['idCompanyType'] : 1;
echo selectCompaniesJSON(($_GET['q'] ?? ''), $idCompanyType, $_GET['id'] ?? null);