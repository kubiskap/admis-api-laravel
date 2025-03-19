<?php
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 14.05.2018
 * Time: 9:13
 */
function getBaseUrl()
{
    $baseUrl = "https://dev.admis.fd.cvut.cz";
    return $baseUrl;
}

function getConnection(){
    $connectionDb = ["server" => "localhost",
        "userDb" => "admis_vyvoj",
        "passDb" => "1JvUHaSV2fAxrVyB",
        "dbName" => "admis_new_dev3"];
    
    return $connectionDb;
}

function getConnectionApi(){
    $connectionDb = ["server" => "localhost",
        "userDb" => "admis_api",
        "passDb" => "zRSr9ppNrBWmTQsB",
        "dbName" => "admis_new_api"];

    return $connectionDb;
}

function getSalt(){
    $salt = 'admisek2019';
    
    return $salt;
}

function getVat(){
    $vat = 0.21;
    return $vat;
}

function getUploadStorage(){
    $uploadDir = "/../uploads";
    $fileSizeLimit = 50000000000;

    return ['uploadDir' => $uploadDir, 'fileSizeLimit' => $fileSizeLimit];
}

function getMailSettings(){
    $mailHost = 'mailgwfd.cvut.cz';                     //Set the SMTP server to send through
    $mailPort = 25;
    $mailSetFrom['mail'] =  'admis@fd.cvut.cz';
    $mailSetFrom['name'] =  'Služebníček Admis';

    return ['mailHost' =>$mailHost, 'mailPort'=>$mailPort, 'mailSetFrom'=> $mailSetFrom];
}

function getCroseusApiCredentials(){
    $username = 'ADMIS_verif_ws';
    $password = 'VLp3EJiazdRfMQLxtXkH';
    $passwordType = 'PasswordText';

    return ['username' => $username, 'password' => $password, 'passwordType' => $passwordType];
}

function getCroseusApiContext(){
    $typPrislibu = 'Limitovany';
    $soapClientOptions = array(
        'trace' => true,
        'connection_timeout' => 50,
        'keep_alive' => false,
        "soap_version" => SOAP_1_1, // SOAP_1_1
    );
    $croseusDataSource = 'ADMIST';
    $location = 'https://croseus.cz/STC/KSUS2/ws/v1.4/FinancniKontrolaService.svc/basic';
    $wsdl = 'https://croseus.cz/STC/KSUS2/ws/v1.4/FinancniKontrolaService.svc?WSDL';

    return ['location' => $location, 'wsdl' => $wsdl, 'soapClientOptions' => $soapClientOptions, 'dataSource' => $croseusDataSource, 'typPrislibu' => $typPrislibu];
}

function getCroseusZalozitDokladXML(){
    $xml = <<<XML
<Body>
        <ZalozitAktualizovatDoklad_PRK_PRED_VZ xmlns='http://schemas.dynatech.cz/CROSEUS/v1.4'>
            <!-- Optional -->
            <content>
                <CONTENT>
                    <NAZEV_PRUVODNIHO_DOKLADU></NAZEV_PRUVODNIHO_DOKLADU>
                    <EXTERNI_IDENTIFIKATOR></EXTERNI_IDENTIFIKATOR>
                    <ORGANIZACNI_JEDNOTKA_EID></ORGANIZACNI_JEDNOTKA_EID>
                    <ZALOZIL_EID></ZALOZIL_EID>
                    <ZADAVATEL_EID></ZADAVATEL_EID>
                    <TYP_DOKUMENTU_EID></TYP_DOKUMENTU_EID>
                    <POPIS></POPIS>
                    <CASTKA_S_DPH></CASTKA_S_DPH>
                    <DATUM_VYSTAVENI></DATUM_VYSTAVENI>
                    <TYP_PRISLIBU></TYP_PRISLIBU>
                </CONTENT>
                <DATA_SOURCE></DATA_SOURCE>
            </content>
        </ZalozitAktualizovatDoklad_PRK_PRED_VZ>
    </Body>
XML;

    return $xml;
}


