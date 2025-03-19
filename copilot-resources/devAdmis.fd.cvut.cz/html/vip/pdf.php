<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 20.06.2018
 * Time: 15:37
 */
require_once __DIR__ . "/../../conf/conf.php";
require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
require_once VENDOR . "autoload.php";
overUzivatele($pristup_zakazan);
$allowedRoles = ['editor', 'adminEditor'];
if(!in_array($_SESSION['role'], $allowedRoles)) die("Role: ". $_SESSION['role']. " -> Nedostatečná oprávnění k zobrazení.");




function getPDFZadankaPD($idRequest)
{
    if (isset($idRequest)) {
        $requestVersion = getLastRequestStatus($idRequest);
        $formData = json_decode($requestVersion['formData'], true);
        $formData = nameValueToArray($formData);
        $formData['created_at'] = $requestVersion['created_at'];
        $formData['updated_at'] = $requestVersion['updated_at'];
        $formData['linkCroseus'] = $requestVersion['linkCroseus'];
        $formData['externalIdent'] = $requestVersion['externalIdent'];
        extract($formData);

    }


    $checkJeInvestice = (isset($jeInvestice) && $jeInvestice == 'on') ? "checked='checked'" : '';
    $checkSpolufinancovanoSObci = (isset($spolufinancovanoSObci) && $spolufinancovanoSObci == 'on') ? "checked='checked'"  : '';
    $checkPlatbaPoFazich = (isset($platbaPoFazich) && $platbaPoFazich == 'on') ? "checked='checked'"  : '';
    $checkPozadavekNaAutorskyDozor = (isset($pozadavekNaAutorskyDozor) && $pozadavekNaAutorskyDozor == 'on') ? "checked='checked'"  : '';
    $checkPozadavekNaMostniListAMostniProhlidku = (isset($pozadavekNaMostniListAMostniProhlidku) && $pozadavekNaMostniListAMostniProhlidku == 'on') ? "checked='checked'" : '';
    $checkBudouVykupy = (isset($budouVykupy) && $budouVykupy == 'on') ? "checked='checked'"  : '';


    $referenceZahrnujici = implode(', ', $referenceZahrnujici);


    $user = getUserAll($editor)[0];


    $css = file_get_contents('kv-mpdf-bootstrap.css');
    $html = "
<br>
   <div class='row'>
    <h2>
        ŽÁDOST O VYHLÁŠENÍ VEŘEJNÉ ZAKÁZKY (PD)
    </h2>
    </div>
    <div class='row'>
        <div class='col-md-6'>
                        <h3 class='sectionHeader'>Základní info projektu</h3>                                
                       

                            <p class='nadpis'>Název veřejné zakázky (projektu):</p>
                            <p class='content'><b>$projectName</b></p>  
                   
                            <p class='nadpis'>Předmět díla:</p>
                            <p class='content'>$projectSubject</p>
                             <p class='nadpis'>ID projektu: <b>$idProject</b></p>
            </div>
    </div>
    
     <div class='row'>
        <div class='col-md-6'>
                        <h3 class='sectionHeader'>Finance</h3>
                      
                  <p class='nadpis'>Předpoklad ceny PD: 
                            $pricePDNoVat Kč bez DPH ($pricePDVat Kč s DPH)
                        </p>
                   
                      

                    <p class='nadpis'>Zdroj financování PD: <b>$zdrojFinancePD</b> </p>
                    <p class='nadpis'>Zdroj financování stavby: <b>$zdrojFinanceStavba</b></p>
                    <div class='checkboxy'>
                    <input type='checkbox' id='jeInvestice' name='jeInvestice' $checkJeInvestice> Jedná se o investici ? 
                    <input type='checkbox' id='spolufinancovanoSObci' name='spolufinancovanoSObci' $checkSpolufinancovanoSObci> Spolufinancováno s obcí?
                    <input type='checkbox' id='platbaPoFazich' name='platbaPoFazich' $checkPlatbaPoFazich> Platba po fázích?
                    </div>
            </div>
        </div>
      <div class='row'>
        <div class='col-md-6'>
                <h3 class='sectionHeader'> Požadavky a reference </h3>
                        <div class='checkboxy'>
                                <input type='checkbox' id='pozadavekNaAutorskyDozor' name='pozadavekNaAutorskyDozor' $checkPozadavekNaAutorskyDozor> Požadavek na autorský dozor?
                                <input type='checkbox' id='pozadavekNaMostniListAMostniProhlidku' name='pozadavekNaMostniListAMostniProhlidku' $checkPozadavekNaMostniListAMostniProhlidku> Požadavek na Mostní list a I. mostní prohlídku?
                                <input type='checkbox' id='budouVykupy' name='budouVykupy' checked='false' $checkBudouVykupy> Budou výkupy?

                        </div>
                        <p class='nadpis'>Počet požadovaných referencí: <b> $pocetReferenci</b></p>
                        <p class='nadpis'>Reference - služby v oblasti novostaveb: <b> $pocetReferenciNovostavby</b></p>  
                        <p class='nadpis'>Reference - zahrnující: </p>
                            <p class='content'>$referenceZahrnujici</p>
            </div>
        </div>
      <div class='row'>
        <div class='col-md-6'>
                        <h3 class='sectionHeader'>Další informace
                        </h3>
                    <p class='nadpis'>Poznámka:</p>
                            <p class='content'>$dalsiInfo</p>
                    <p class='nadpis'>Odpovědná osoba (editor): <b>$user[name] ($user[email])</b> </p>
                    <p class='nadpis'>Zadáno: <b>$created_at</b> (poslední změna stavu $updated_at) </p>
                    <p class='nadpis'>Croseus reference: <a href='$linkCroseus'>$linkCroseus</a> </p>
                    <p class='nadpis'>Admis ID žádanky: $externalIdent</p>



                        
        </div>
    </div>
";
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->SetHTMLHeader('<img src="../img/100AdmisPink.png" width="100"/><img style="float: right;" src="../img/logo_ksus.jpg" width="100"/>');
    $mpdf->SetFooter('KSÚS - Středočeský kraj');
    $mpdf->WriteHTML($css, 1);
    $mpdf->WriteHTML($html);
    $pdfContent = $mpdf->Output();


}

getPDFZadankaPD(68);