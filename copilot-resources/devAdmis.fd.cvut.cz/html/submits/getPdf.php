<?php
/**
 * Created by PhpStorm.
 * User: Ondrac
 * Date: 20.06.2018
 * Time: 15:37
 */
require_once __DIR__ . "/../../conf/conf.php";
require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
require_once SYSTEMINCLUDES . "pdf.php";

require_once VENDOR . "autoload.php";
overUzivatele($pristup_zakazan);
$allowedRoles = ['editor', 'adminEditor'];
if(!in_array($_SESSION['role'], $allowedRoles)) die("Role: ". $_SESSION['role']. " -> Nedostatečná oprávnění k zobrazení.");

if(isset($_GET['idRequest']) && is_numeric($_GET['idRequest']) && isset($_GET['externalIdent']) ) {
    $pdfString = getPDFZadankaPD($_GET['idRequest']);
    $filename = "zadanka_".$_GET['externalIdent'].".pdf";
    header('Content-Type: application/pdf');
    header("Content-Disposition: inline; filename='$filename'");
    header('Content-Length: ' . strlen($pdfString));

// Odeslání PDF do prohlížeče
    echo $pdfString;
}
else{
    echo "Chyba při získávání PDF, kontaktutje správce.";
}