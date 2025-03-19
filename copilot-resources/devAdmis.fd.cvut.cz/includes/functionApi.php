<?php

namespace api;

function authorizeAccess($token, $ipAddress)
{
    require_once __DIR__ . "/autoLoader.php";
    $result = 0;
    if (isset($token) && isset($ipAddress) && strlen($token) == 32) {
        $dbh = new DatabaseConnectorApi();
        $tokenHash = hash('SHA256', $token);
        $stmt = $dbh->getDbLink()->prepare("SELECT idUser FROM users WHERE 
                          token = :token AND IpAddresess LIKE CONCAT('%', :ipAddress, '%') 
                      AND deletedAt IS NULL AND validFrom < NOW() AND validTo > NOW()");
        $stmt->bindParam(':token', $tokenHash, \PDO::PARAM_STR);
        $stmt->bindParam(':ipAddress', $ipAddress, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn(0);
        $result = !empty($result) ? $result : 0;
    }
    return $result;
}

function getAllProjects(int $fromTimestamp = NULL, int $limit, int $offset)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnectorApi();
    if ($fromTimestamp && is_numeric($fromTimestamp)) {
        $query = $dbh->getDbLink()->query("SELECT idProjektu, stav, lastUpdated, lastUpdatedTimestamp, nazev, popis, spravceStavby, 'admis' as datasource,DATE_FORMAT(zacatekStavby, '%Y-%m') as zacatekStavby, DATE_FORMAT(konecStavby, '%Y-%m') as konecStavby, cenaStavbyRealizace FROM projects WHERE lastUpdatedTimestamp >= $fromTimestamp ORDER BY lastUpdated DESC LIMIT $limit OFFSET $offset");
    } else {
        $query = $dbh->getDbLink()->query("SELECT idProjektu, stav, lastUpdated, lastUpdatedTimestamp, nazev, popis, spravceStavby, 'admis' as datasource,DATE_FORMAT(zacatekStavby, '%Y-%m')as zacatekStavby, DATE_FORMAT(konecStavby, '%Y-%m') as konecStavby, cenaStavbyRealizace FROM projects ORDER BY lastUpdated DESC LIMIT $limit OFFSET $offset");

    }
    $result = $query->fetchAll(\PDO::FETCH_ASSOC);
    return $result;

}

function logEvent($user, $url, $ipAddress, $accessDenied)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnectorApi();
    $stmt = $dbh->getDbLink()->prepare("INSERT INTO `logs`( `idUser`, `callAt`, `url`, `ipAddress`, `accessDenied`) VALUES (:user,NOW(),:url, :ipAddress, :accessDenied)");
    $stmt->bindParam(':user', $user, \PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, \PDO::PARAM_STR);
    $stmt->bindParam(':ipAddress', $ipAddress, \PDO::PARAM_STR);
    $stmt->bindParam(':accessDenied', $accessDenied, \PDO::PARAM_INT);

    $stmt->execute();
}

function getCommunications(int $idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnectorApi();
    $result = null;
    if (is_numeric($idProject)) {
        $query = $dbh->getDbLink()->query("SELECT communicationName as komunikace, gpsLatitude1, gpsLongitude1,gpsLatitude2, gpsLongitude2, stationingFrom as staniceniOd, stationingTo as staniceniDo FROM project2communication WHERE idProject = $idProject ");
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    return $result;

}

function getAreas(int $idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnectorApi();
    $result = null;
    if (is_numeric($idProject)) {
        $query = $dbh->getDbLink()->query("SELECT areaName as okres FROM projects2area WHERE idProject = $idProject ");
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    return $result;

}

function getProject($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnectorApi();
    $result = null;
    if (is_numeric($idProject)) {
        $query = $dbh->getDbLink()->query("SELECT idProjektu, stav, lastUpdated, lastUpdatedTimestamp, nazev, popis, spravceStavby, 'admis' as datasource, DATE_FORMAT(zacatekStavby, '%Y-%m') as zacatekStavby, DATE_FORMAT(konecStavby, '%Y-%m') as konecStavby, cenaStavbyRealizace FROM projects WHERE idProjektu = $idProject");
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    return $result;
}

function getJsonOutput(array $projectsData)
{
    $returnArr = [];
    foreach ($projectsData as $projectData) {
        $communicationsArr = getCommunications($projectData['idProjektu']);
        $areasArr = getAreas($projectData['idProjektu']);
        $projectData['komunikace'] = $communicationsArr;
        $projectData['okresy'] = $areasArr;
        $returnArr[] = $projectData;
        // print_r($returnArr);

    }
    $returnArr = json_encode(array_urlencode($returnArr));
    return $returnArr;

}

function array_urlencode($data)
{
    $data_temp = array();
    if (is_array($data)) {
        foreach ($data as $k => $v) $data_temp[urlencode($k)] = array_urlencode($v);
        return $data_temp;
    } else {
        return urlencode($data);
    }
}