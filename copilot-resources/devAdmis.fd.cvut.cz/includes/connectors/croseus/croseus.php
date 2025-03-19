<?php
include_once(__DIR__.'/vendor/autoload.php');
$client = new Laminas\Soap\Client("https://croseus.cz/STC/KSUS2/ws/v1.4/FinancniKontrolaService.svc?WSDL");
print_r($client->getFunctions());
