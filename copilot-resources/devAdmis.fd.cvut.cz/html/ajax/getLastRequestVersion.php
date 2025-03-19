<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_POST['idRequest'])) {
    $requestVersion = getLastRequestStatus($_POST['idRequest']);
    $requestData = array(
        'formData' => json_decode($requestVersion['formData']),
        'idRequestType' => $requestVersion['idRequestType'],
        'idRequestStatus' => $requestVersion['idRequestStatus'],
        'idProject' => $requestVersion['idProject']
    );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($requestData, JSON_UNESCAPED_UNICODE);
}
?>