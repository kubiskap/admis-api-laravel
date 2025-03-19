<?php
//require_once "WSSoapClient.php";

/*
$username = 'ADMIS_verif_ws';
$password = 'VLp3EJiazdRfMQLxtXkH';
$passwordType = 'PasswordText';
$wsdl = 'https://croseus.cz/STC/KSUS2/ws/v1.4/FinancniKontrolaService.svc?WSDL';



$opts = array(
    'socket' => ['bindto' => '147.32.72.150:0'],
    'http' => array(
        'header' => 'Content-Type: text/xml'
    )
);
$context = stream_context_create($opts);

$soapClientOptions = array(
    'stream_context' => $context,
    'trace' => true,
    'connection_timeout' => 50,
    'keep_alive' => false,
    "soap_version" => SOAP_1_1, // SOAP_1_1
);

//$wsse_header = new WsseAuthHeader($username, $password);
//$client = new WSSoapClient($wsdl, $soapClientOptions);
$client = new SoapClient($wsdl, $soapClientOptions);

$strXML = <<<XML
<Security xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
    <UsernameToken>
        <Username>ADMIS_verif_ws</Username>
        <Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">VLp3EJiazdRfMQLxtXkH</Password>
    </UsernameToken>
</Security>
XML;
$objAuthVar = new \SoapVar($strXML, XSD_ANYXML);
$objAuthHeader = new \SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", 'Security', $objAuthVar, false);
$client->__setSoapHeaders(array($objAuthHeader));


try {

    $params = ['content' => array (
        "EXTERNI_IDENTIFIKATOR" => "ADMIST1",
        "DATA_SOURCE" => "ADMIST",
    )];

    $params2 = array (
        "message" => "ADMIST1"
    );
    $client->__setLocation("https://croseus.cz/STC/KSUS2/ws/v1.4/FinancniKontrolaService.svc/basic");
$response = $client->DejStavDokladu( $params);
}  catch (SoapFault $e) {
    print_r($client->__getFunctions());

    print_r($client->__getLastRequest());
    echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
    exit($e->getMessage());
}


print_r($client->__getLastRequest());
print_r($response);*/

function dejStavDokladuCroseusExt($extIdent)
{
    require_once __DIR__ . "/../autoLoader.php";
    require_once __DIR__ . "/../../conf/conf.php";

    $credetials = getCroseusApiCredentials();
    $context = getCroseusApiContext();
    $client = new CroseusApi($context['wsdl'], $context['soapClientOptions'], $credetials['username'], $credetials['password']);
    try {
        $params = ['content' => array(
            "EXTERNI_IDENTIFIKATOR" => "$extIdent",
            "DATA_SOURCE" => "$context[dataSource]",
        )];

        $client->__setLocation($context['location']);
        $response = $client->DejStavDokladu($params);
    } catch (CroseusApi $e) {
        echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
        exit($e->getMessage());
    }
    return json_decode(json_encode($response),true)['DejStavDokladuResult'];

}
function dejStavDokladuCroseusMulti(array $idents)
{
    require_once __DIR__ . "/../autoLoader.php";
    require_once __DIR__ . "/../../conf/conf.php";
    $obj = new StdClass();
    foreach ($idents as $item) {
        $obj->string[] = $item;
    }

    $credetials = getCroseusApiCredentials();
    $context = getCroseusApiContext();
    $client = new CroseusApi($context['wsdl'], $context['soapClientOptions'], $credetials['username'], $credetials['password']);
    try {
        $params = ['content' => array(
            "EXTERNI_IDENTIFIKATOR" => $obj,
            "DATA_SOURCE" => "$context[dataSource]",
        )];
        $client->__setLocation($context['location']);
        $response = $client->DejStavDokladuMulti($params);
    } catch (CroseusApi $e) {
        echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
        exit($e->getMessage());
    }
    //return $client->__getLastRequest();
    return json_decode(json_encode($response),true)['DejStavDokladuMultiResult']['PruvodniDokladStav'];

}


function dejStavDokladuCroseus($ident)
{
    require_once __DIR__ . "/../autoLoader.php";
    require_once __DIR__ . "/../../conf/conf.php";

    $credetials = getCroseusApiCredentials();
    $context = getCroseusApiContext();
    $client = new CroseusApi($context['wsdl'], $context['soapClientOptions'], $credetials['username'], $credetials['password']);
    try {
        $params = ['content' => array(
            "DOKLAD_ID" => "$ident",
            "DATA_SOURCE" => "$context[dataSource]",
        )];

        $client->__setLocation($context['location']);
        $response = $client->DejStavDokladu($params);
    } catch (CroseusApi $e) {
        echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
        exit($e->getMessage());
    }
    return json_decode(json_encode($response),true)['DejStavDokladuResult'];

}

function zalozitDokladCroseus($arrValue){
    require_once __DIR__ . "/../autoLoader.php";
    require_once __DIR__ . "/../../conf/conf.php";

    $credetials = getCroseusApiCredentials();
    $context = getCroseusApiContext();
    $client = new CroseusApi($context['wsdl'], $context['soapClientOptions'], $credetials['username'], $credetials['password']);
    try {
        $params = array ( 'content' => //jo vazne je tam 2x content, zeptej se v Brne proc
            array ('CONTENT' =>
                            array(
                        "NAZEV_PRUVODNIHO_DOKLADU" => "$arrValue[NAZEV_PRUVODNIHO_DOKLADU]_$arrValue[TYP_ZADANKY]",
                        "EXTERNI_IDENTIFIKATOR" => "$arrValue[EXTERNI_IDENTIFIKATOR]_$arrValue[CISLO_ZADANKY]",
                        "ORGANIZACNI_JEDNOTKA_EID" => "$arrValue[ORGANIZACNI_JEDNOTKA_EID]",
                        "ZALOZIL_EID" => "$arrValue[ZALOZIL_EID]",
                        "ZADAVATEL_EID" => "$arrValue[ZALOZIL_EID]",
                        "TYP_DOKUMENTU_EID" => "VZnad150proadmis",
                        "POPIS" => "$arrValue[POPIS]",
                        "CASTKA_S_DPH" => "$arrValue[CASTKA_S_DPH]",
                        "DATUM_VYSTAVENI" => (new DateTime())->format('Y-m-d\TH:i:s.v\Z'),
                        "TYP_PRISLIBU" => "$context[typPrislibu]"
                            ),
                "DATA_SOURCE" => "$context[dataSource]"
            )
        );

        $client->__setLocation($context['location']);
        $response = $client->ZalozitAktualizovatDoklad_PRK_PRED_VZ($params);
    } catch (CroseusApi $e) {
        echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
        echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
        exit($e->getMessage());
    }
    return json_decode(json_encode($response),true)['ZalozitAktualizovatDoklad_PRK_PRED_VZResult'];
}

function PridatAktualizovatPrilohu($arrValue){
    require_once __DIR__ . "/../autoLoader.php";
    require_once __DIR__ . "/../../conf/conf.php";

    $credetials = getCroseusApiCredentials();
    $context = getCroseusApiContext();
    $client = new CroseusApi($context['wsdl'], $context['soapClientOptions'], $credetials['username'], $credetials['password']);
    try {
        $params = array ( 'content' => //jo vazne je tam 2x content, zeptej se v Brne proc
            array ('CONTENT' =>
                array(
                    "DOKLAD_EID" => "$arrValue[DOKLAD_EID]",
                    "NAZEV" => "$arrValue[NAZEV]",
                    "DATA" => "$arrValue[DATA]",
                    "TYP_EID" => "admisfile" //konstnze
                ),
                "DATA_SOURCE" => "$context[dataSource]"
            )
        );

        $client->__setLocation($context['location']);
        $response = $client->PridatAktualizovatPrilohu($params);
    } catch (CroseusApi $e) {
        echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
        echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
        exit($e->getMessage());
    }
    return json_decode(json_encode($response),true)['PridatAktualizovatPrilohuResult'];
}
/*$arr =[
    "NAZEV_PRUVODNIHO_DOKLADU" => "Zadanka Admis Test",
    "EXTERNI_IDENTIFIKATOR" => 18,
    "ORGANIZACNI_JEDNOTKA_EID" => "OBLAST-SPC-PRAHA-EU",
    "ZALOZIL_EID" => "petr.nadvornik",
    "ZADAVATEL_EID" => "petr.nadvornik",
    "TYP_DOKUMENTU_EID" => "VZnad150proadmis",
    "POPIS" => "test3",
    "CASTKA_S_DPH" => "100000.58",
    "DATUM_VYSTAVENI" => "2024-07-02T06:40:11.894Z"
];*/
//print_r(zalozitDokladCroseus($arr));
//dejStavDokladuCroseus("TADMIS_0002");
//print_r(dejStavDokladuCroseusMulti(['331_71','163_72']));