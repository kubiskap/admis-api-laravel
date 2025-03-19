<?php
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 14.05.2018
 * Time: 8:25
 */

function validatePageUrl($url)
{
    require_once __DIR__ . "/autoLoader.php";
    $url = strtolower($url);
    if ($url != '') {
        $db = new DatabaseConnector();
        $query = $db->getDbLink()->query('SELECT url FROM pages');
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
        if (in_array($url, $result)) {
            return true;
        } else {
            return false;
        }
    }
}

function getObjectsByProjectSubtype($idProjectSubtype)
{
    require_once __DIR__ . "/autoLoader.php";
    $result = false;
    if (is_numeric($idProjectSubtype)) {
        $db = new DatabaseConnector();
        $query = $db->getDbLink()->query("SELECT * FROM projectSubtypes2ObjectTypes JOIN rangeObjectTypes USING (idObjectType) WHERE projectSubtypes2ObjectTypes.idProjectSubtype = $idProjectSubtype AND rangeObjectTypes.hidden IS FALSE");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $result;
}


function getProjectArea($idProject)
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT p2a.idArea, ra.name FROM project2area p2a JOIN rangeAreas ra USING (idArea) WHERE idProject =:idProject AND ra.hidden = 0");
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


function getProjectCommunication($idProject)
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT p2c.idCommunication, rc.name, stationingFrom, stationingTo, gpsN1, gpsN2, gpsE1, gpsE2, comment	FROM project2communication p2c JOIN rangeCommunications rc USING (idCommunication) WHERE idProject =:idProject AND rc.hidden = 0");
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


function setTeammate($collaborator, $expiry = null, $begin = null)
{
    $lastId = false;

    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('INSERT INTO `collaborator` (username,collaborator,created,begin,expiry) VALUES (:username, :collaborator,NOW(),:begin,:expiry)');
    $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->bindParam(':collaborator', $collaborator, PDO::PARAM_STR);
    $stmt->bindParam(':begin', $begin, PDO::PARAM_STR);
    $stmt->bindParam(':expiry', $expiry, PDO::PARAM_STR);
    $stmt->execute();
    $lastId = $dbh->getDbLink()->lastInsertId();

    return $lastId;

}

function selectTeammates($username)
{
    $db = new DatabaseConnector();
    $stmt = $db->getDbLink()->prepare("SELECT username, name FROM users WHERE accessDenied is FALSE AND idRoleType = 1 AND username  != :username ORDER BY name");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $teammates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($teammates);
    $html = '';
    foreach ($teammates as $mate) {
        $html .= "<option value='$mate[username]' >$mate[name]</option>";
    }
    return $html;
}

function getTeammates($username)
{
    $db = new DatabaseConnector();
    $stmt = $db->getDbLink()->prepare("SELECT collaborator.username as collaborator, users.name as nameTeammate FROM collaborator JOIN users ON collaborator.username = users.username WHERE collaborator.collaborator = :username AND active = 1 AND (begin is NULL OR begin <= CURDATE()) AND (expiry is NULL OR expiry >= CURDATE())");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $teammates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $teammates;
}

function setSessionTeammates($getTeammatesArr)
{
    $returnArr = array();

    foreach ($getTeammatesArr as $teammate) {
        array_push($returnArr, $teammate['collaborator']);
    }
    $_SESSION['teammates'] = $returnArr;
}

function getEditorReportStatus()
{
    $db = new DatabaseConnector();
    $stmt = $db->getDbLink()->prepare("SELECT editorReport FROM users WHERE username=:username");
    $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $editorReport = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editorReport['editorReport']) {
        $editorReport = 'checked';
    } else {
        $editorReport = '';
    }
    return $editorReport;
}

/**
 * @return mixed|string
 */
function getPageFromUri()
{
    require_once __DIR__ . "/autoLoader.php";
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $url = strtolower(basename($url));
    if ($url != '' && $url != 'index.php') {
        $db = new DatabaseConnector();
        $query = $db->getDbLink()->query('SELECT url FROM pages WHERE aktivni = 1');
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
        if (in_array($url, $result)) {
            return $url;
        } else {
            return 'error';
        }
    } else {
        return 'home';
    }

}

/*
 * Pocita jen se jmeny, jmeno a prijmeni, teda jen dve
 * 
 */
/**
 * @param $name
 * @return string
 */
function getInitialsFromName($name)
{
    $name = escapeDiacritics($name);
    $nameArr = explode(" ", $name);
    $firstInitials = substr($nameArr[0], 0, 1);
    if (isset($nameArr[1])) {
        $secondInitials = substr($nameArr[1], 0, 1);
    } else {
        $secondInitials = "";
    }
    $initials = strtoupper($firstInitials . $secondInitials);
    return $initials;

}

function cesky_mesic($mesic)
{
    static $nazvy = array(
        1 => 'leden',
        'únor',
        'březen',
        'duben',
        'květen',
        'červen',
        'červenec',
        'srpen',
        'září',
        'říjen',
        'listopad',
        'prosinec'
    );
    return $nazvy[$mesic];
}

function escapeDiacritics($string)
{
    $string = strtolower($string);
    $chars2Escape = array(
        'ä' => 'a',
        'Ä' => 'A',
        'á' => 'a',
        'Á' => 'A',
        'à' => 'a',
        'À' => 'A',
        'ã' => 'a',
        'Ã' => 'A',
        'â' => 'a',
        'Â' => 'A',
        'č' => 'c',
        'Č' => 'C',
        'ć' => 'c',
        'Ć' => 'C',
        'ď' => 'd',
        'Ď' => 'D',
        'ě' => 'e',
        'Ě' => 'E',
        'é' => 'e',
        'É' => 'E',
        'ë' => 'e',
        'Ë' => 'E',
        'è' => 'e',
        'È' => 'E',
        'ê' => 'e',
        'Ê' => 'E',
        'í' => 'i',
        'Í' => 'I',
        'ï' => 'i',
        'Ï' => 'I',
        'ì' => 'i',
        'Ì' => 'I',
        'î' => 'i',
        'Î' => 'I',
        'ľ' => 'l',
        'Ľ' => 'L',
        'ĺ' => 'l',
        'Ĺ' => 'L',
        'ń' => 'n',
        'Ń' => 'N',
        'ň' => 'n',
        'Ň' => 'N',
        'ñ' => 'n',
        'Ñ' => 'N',
        'ó' => 'o',
        'Ó' => 'O',
        'ö' => 'o',
        'Ö' => 'O',
        'ô' => 'o',
        'Ô' => 'O',
        'ò' => 'o',
        'Ò' => 'O',
        'õ' => 'o',
        'Õ' => 'O',
        'ő' => 'o',
        'Ő' => 'O',
        'ř' => 'r',
        'Ř' => 'R',
        'ŕ' => 'r',
        'Ŕ' => 'R',
        'š' => 's',
        'Š' => 'S',
        'ś' => 's',
        'Ś' => 'S',
        'ť' => 't',
        'Ť' => 'T',
        'ú' => 'u',
        'Ú' => 'U',
        'ů' => 'u',
        'Ů' => 'U',
        'ü' => 'u',
        'Ü' => 'U',
        'ù' => 'u',
        'Ù' => 'U',
        'ũ' => 'u',
        'Ũ' => 'U',
        'û' => 'u',
        'Û' => 'U',
        'ý' => 'y',
        'Ý' => 'Y',
        'ž' => 'z',
        'Ž' => 'Z',
        'ź' => 'z',
        'Ź' => 'Z'
    );

    $string = strtr($string, $chars2Escape);
    return $string;
}

/**
 * @param $text
 * @return string
 */
function createAvatar($text)
{
    if (strlen($text) != 2) {
        $text = 'AD';

    }
    $im = imagecreatetruecolor(100, 100);
    $bg = imagecolorallocate($im, 240, 240, 240);
    $textColor = imagecolorallocate($im, 64, 64, 64);
    imagefilledrectangle($im, 0, 0, 100, 100, $bg);
    $font = 'arial.ttf';
    imagettftext($im, 33, 0, 18, 66, $textColor, $font, $text);
    ob_start();
    imagepng($im);
    $imgData = ob_get_contents();
    ob_end_clean();
    $base64 = base64_encode($imgData);
    return $base64;
}


/**
 * @param $username
 * @param $vysledek
 * @param $ip_adresa
 * @param $agent
 */
function zapis_log_login($username, $vysledek, $ip_adresa, $agent)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    //mysql_query("SET NAMES utf8");
    $stmt = $dbh->getDbLink()->prepare("INSERT INTO logins (username,result,loginTime,ipAddress,agent) VALUES (:username,:vysledek,NOW(),:ip_adresa, :agent)");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':vysledek', $vysledek, PDO::PARAM_STR);
    $stmt->bindValue(':ip_adresa', $ip_adresa, PDO::PARAM_STR);
    $stmt->bindValue(':agent', sha1($agent), PDO::PARAM_STR);
    $stmt->execute();
}

/**
 * @param $username
 * @return bool|int
 */
function fail2ban($username)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT username,count(username) pokusy FROM logins WHERE username LIKE :username
                  AND DATE(loginTime) = CURDATE() AND TIME_TO_SEC(TIME(loginTime)) > (TIME_TO_SEC(CURTIME()) - 60)");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $blok = false;
    $stmt->execute();
    $vys = $stmt->fetchAll();
    $pokusy = $vys[0]['pokusy'];
    if ($pokusy > 3 && $pokusy < 5) {
        $blok = 0;
    } elseif ($pokusy > 5) {
        $stmt = $dbh->getDbLink()->prepare("UPDATE users SET accessDenied='1' WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $blok = true;

    }
    return $blok;
}

/**
 * @param $poleVstup
 * @return array
 */
function htmlspecialcharsArr($poleVstup)
{
    $poleVystup = array_map('htmlspecialchars', $poleVstup);

    foreach ($poleVystup as $index => $item) {
        if ($item == '') {
            $poleVystup[$index] = null;
        }
    }

    return $poleVystup;
}

/**
 * @return string
 */
function lastProjectId()
{

    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query('SELECT IFNULL(MAX(idProject), 0) AS max FROM projects');
    $idProjectLast = $stmt->fetchColumn();
    return $idProjectLast;

}

/**
 * @param $name
 * @return bool
 */
function findTypeProject($name)
{
    $id = false;
    $name = htmlspecialchars($name);
    $name = strtolower(preg_replace('/\s+/', '', $name));
    $name = escapeDiacritics($name);
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query('SELECT * FROM typeProject');
    $typesArr = $stmt->fetchAll();
    foreach ($typesArr as $eachType) {
        $typeName = escapeDiacritics(strtolower(preg_replace('/\s+/', '', $eachType['name'])));
        if ($name == $typeName) {
            $id = $eachType['idProjectType'];
        }
    }

    return $id;
}

function findCompanyByName($companyName)
{
    $companyId = false;
    $companyName = htmlspecialchars($companyName);
    $companyName = strtolower(preg_replace('/\s+/', '', $companyName));
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $companiesArr = $dbh->select('rangeCompanies', '1');
    foreach ($companiesArr as $company) {
        if (htmlspecialchars(strtolower(preg_replace('/\s+/', '', $company['name']))) == $companyName) {
            $companyId = $company['idCompany'];
        }
    }
    return $companyId;
}

function findCompanyByIdContact($idContact)
{
    $companyInfoArr = false;
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idContact)) {
        $stmt = $dbh->getDbLink()->query("SELECT idCompany, rangeCompanies.name  FROM `rangeCompanies` JOIN rangeContacts USING(idCompany) WHERE rangeContacts.idContact = $idContact ");
        $companyInfoArr = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $companyInfoArr;
}

/* 4DELETE od 09/2022 je kontakt oddelen od firmy
function findContactsByCompanyName($companyName)
{
    $return = array();
    $companyName = htmlspecialchars(escapeDiacritics($companyName));
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $companiesArr = $dbh->select('rangeCompanies', '1');
    foreach ($companiesArr as $company) {
        if (htmlspecialchars(escapeDiacritics($company['name'])) == $companyName) {
            $companyId = $company['idCompany'];
        }

    }
    if (isset($companyId)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT idContact, name, phone, email   FROM rangeContacts WHERE idCompany = :idCompany");
        $stmt->bindParam(':idCompany', $companyId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return $return;
}

function findContactsByCompanyId($companyId)
{
    $return = array();
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($companyId)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT idContact, name, phone, email   FROM rangeContacts WHERE idCompany = :idCompany AND active = 1");
        $stmt->bindParam(':idCompany', $companyId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return $return;
}
*/
function findContactById($idContact)
{
    $return = array(0 => array('name' => null, 'phone' => null, 'mail' => null));
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idContact)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT name, phone, email   FROM rangeContacts WHERE idContact = :idContact");
        $stmt->bindParam(':idContact', $idContact, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return $return;

}


function findContact($name, $mail)
{

    $id = false;
    $name = htmlspecialchars($name);
    $name = strtolower(preg_replace('/\s+/', '', $name));
    $mail = strtolower(preg_replace('/\s+/', '', $mail));
    $name = escapeDiacritics($name);
    $searchString = $name . $mail;
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT idContact, CONCAT(name, email) as identString FROM rangeContacts WHERE email = '$mail' AND active = 1");
    $contactsArr = $stmt->fetchAll();
    foreach ($contactsArr as $contact) {
        $typeName = escapeDiacritics(strtolower(preg_replace('/\s+/', '', $contact['identString'])));
        if ($searchString == $typeName) {
            $id = $contact['idContact'];
        }
    }

    return $id;
}

function createContact($name, $email, $phone)
{

    $name = htmlspecialchars($name);
    $email = htmlspecialchars($email);
    $lastId = findContact($name, $email);
    $phone = htmlspecialchars(preg_replace('/\s+/', '', $phone));
    if ($lastId == false) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
        try {
            $stmt = $dbh->getDbLink()->prepare('INSERT INTO `rangeContacts` (`name`,email,phone) VALUES (:name, :mail,:phone)');
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->execute();
            $lastId = $dbh->getDbLink()->lastInsertId();
            // aktualne neloguje, funkce neumi null a ani nedava v db smysl - nema idLocalProject
            insertActionLog(null, 10, $dbh);
            $stmt = $dbh->getDbLink()->commit();
        } catch (Exception $e) {
            $stmt = $dbh->getDbLink()->rollBack();
            $lastId = 'Chyba transakce, vracím změny zpět. Chyba: ' . $e;
            writeError2Log(__FUNCTION__, 'fucntions - createContact', $e);
        }
    }
    return $lastId;
}

function updateContactPhone($idContact, $phone)
{
    if (is_numeric($idContact)) {
        $phone = htmlspecialchars(preg_replace('/\s+/', '', $phone));
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `rangeContacts` SET phone=:phone, updated = NOW(), updated_by = :username WHERE idContact = :idContact AND phone != :phone ");
        $stmt->bindParam(':idContact', $idContact, PDO::PARAM_INT);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        return true;
    } else {
        return false;
    }
}

function editContact($idContact, $name, $email, $phone)
{
    if (is_numeric($idContact)) {
        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);
        $phone = htmlspecialchars($phone);
        $idCompany = $firma;
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `rangeContacts` SET `name`=:name,email=:mail,phone=:phone, updated = NOW(), updated_by = :username WHERE idContact = :idContact");
        $stmt->bindParam(':idContact', $idContact, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':mail', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function editTeammate($id, $collaborator, $expiry, $begin)
{
    if (is_numeric($id)) {
        $collaborator = htmlspecialchars($collaborator);
        $expiry = htmlspecialchars($expiry);
        $begin = htmlspecialchars($begin);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `collaborator` SET `username`=:username,collaborator=:collaborator,begin=:begin,expiry=:expiry WHERE id = :id");
        $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':collaborator', $collaborator, PDO::PARAM_STR);
        $stmt->bindParam(':begin', $begin, PDO::PARAM_STR);
        $stmt->bindParam(':expiry', $expiry, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * @return string
 */
function selectRoads($selectedId, $idCommunicationType)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT idCommunication, name FROM rangeCommunications WHERE idCommunicationType IN ($idCommunicationType)");
    $stmt->execute();
    $roadsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte komunikaci</option>";
    foreach ($roadsArr as $road) {
        $selected = ($selectedId == $road['idCommunication']) ? "selected" : "";
        $html .= "<option $selected value='$road[idCommunication]' >$road[name]</option>";
    }
    return $html;
}

/**
 * @return string
 */
function selectRoleTypes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idRoleType, name FROM rangeRoleTypes');
    $stmt->execute();
    $roadsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte roli</option>";
    foreach ($roadsArr as $road) {
        $html .= "<option value='$road[idRoleType]' >$road[name]</option>";
    }
    return $html;
}

function includeFilesFromDirectory($path, $require = FALSE)
{
    if ($require) {
        foreach (glob($path) as $filename) {
            require $filename;
        }
    }
    if (!$require) {
        foreach (glob($path) as $filename) {
            include $filename;
        }
    }
}

function changeQueryActive($active)
{

    parse_str($_SERVER['QUERY_STRING'], $params);
    $params['active'] = $active;
    return http_build_query($params, '', '&amp;');
}

function numberOfPages($totalPages, $numberProjectsPage)
{
    return (int)($totalPages / $numberProjectsPage) + (($totalPages % $numberProjectsPage > 0) ? 1 : 0);
}

function strankovac($maxRozsah, $aktivniStranka, $celkemStranek)
{
    $html = '';
    $rosahLeva = (int)$maxRozsah / 2;
    $rosahPrava = (int)$maxRozsah / 2;

    $zacatek = ($aktivniStranka - $rosahLeva < 1) ? 1 : $aktivniStranka - $rosahLeva;
    $konec = ($aktivniStranka + $rosahPrava < $celkemStranek) ? $aktivniStranka + $rosahLeva : $celkemStranek;
    $rozdilLeva = ($aktivniStranka - 1 <= $rosahLeva) ? $rosahLeva - ($aktivniStranka - 1) : 0;
    $rozdilPrava = ($celkemStranek - $aktivniStranka <= $rosahPrava) ? $rosahPrava - ($celkemStranek - $aktivniStranka) : 0;
    $dalsi = ($aktivniStranka + 1 < $celkemStranek) ? $aktivniStranka + 1 : $celkemStranek;
    $predchozi = ($aktivniStranka - 1 > 1) ? $aktivniStranka - 1 : 1;
    $zacatek = ($zacatek - $rozdilPrava < 1) ? 1 : $zacatek - $rozdilPrava;
    $konec = ($konec + $rozdilLeva > $celkemStranek) ? $celkemStranek : $konec + $rozdilLeva;
    $html .= "<li class='page-item'>
                <a class='page-link' href='vypis.php?" . changeQueryActive(1) . "'>
                    <span>První</span>
                </a>
            </li>
            <li class='page-item'>
                <a class='page-link' href='vypis.php?" . changeQueryActive($predchozi) . "'>
                     <span>&laquo;</span>
                </a>
            </li>";
    for ($i = $zacatek; $i <= $konec; $i++) {
        ($i == $aktivniStranka) ? $aktivni = "class='active page-item'" : $aktivni = "class='page-item'";
        $html .= "<li " . $aktivni . "><a class='page-link' href='vypis.php?" . changeQueryActive($i) . "'>" . $i . "</a></li>";
    }
    $html .= "<li class='page-item'>
                <a class='page-link' href='vypis.php?" . changeQueryActive($dalsi) . "'>
                    <span>&raquo;</span>
                </a>
              </li>
              <li class='page-item'>
                <a class='page-link' href='vypis.php?" . changeQueryActive($celkemStranek) . "'>
                    <span>" . $celkemStranek . "</span>
                </a>
              </li>";
    return $html;
}

function selectSuspensionReasons()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idSuspensionReason, name FROM rangeSuspensionReasons WHERE hidden = 0');
    $stmt->execute();
    $html = "<option disabled='disabled' value=''>Vyberte důvod přerušení stavby</option>";
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $reason) {
        $html .= "<option value='$reason[idSuspensionReason]' >$reason[name]</option>";
    }
    return $html;
}

function selectTaskStatuses()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idTaskStatus, name FROM rangeTaskStatuses WHERE isEnabled = 1');
    $stmt->execute();
    $tasksArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte stav úkolu</option>";
    foreach ($tasksArr as $taskStatus) {
        $html .= "<option value='$taskStatus[idTaskStatus]' >$taskStatus[name]</option>";
    }
    return $html;
}

function selectRequetsStatuses(int $rank = 6)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idRequestStatus, name FROM rangeRequestStatuses WHERE isEnabled = 1 AND croseus != 1 AND rank <= :rank');
    $stmt->bindValue(':rank', $rank, PDO::PARAM_STR);
    $stmt->execute();
    $tasksArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte stav žádanky</option>";
    foreach ($tasksArr as $taskStatus) {
        $html .= "<option value='$taskStatus[idRequestStatus]' >$taskStatus[name]</option>";
    }
    if(count($tasksArr) == 0){
        $html = "<option disabled='disabled' value=''>Není možné žádance v tomto stavu přidat jiný stav. </option>";
    }
    return $html;
}

function selectRequestTypes(int $idPhase)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT idRequestType, name, formCode FROM rangeRequestTypes WHERE isEnabled = 1 AND JSON_SEARCH(availableInPhases, 'one', :idPhase) IS NOT NULL");
    $stmt->bindValue(':idPhase', $idPhase, PDO::PARAM_STR);
    $stmt->execute();
    $requestsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte typ žádosti</option>";
    foreach ($requestsArr as $requestType) {
        $html .= "<option value='$requestType[idRequestType]' >$requestType[name]</option>";
    }
    if(count($requestsArr) == 0){
        $html = "<option disabled='disabled' value=''>Pro projekt v této fázi nejsou dostupné žádné žádanky. </option>";
    }
    return $html;
}

function getCroseusRequestsForUpdate()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT externalIdent,idRequest, idRequestStatus FROM viewCroseusRequests');
    $stmt->execute();
    $requestsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $requestsArr;
}

function getCroseusRequestsIdByName($name)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idRequestStatus  FROM rangeRequestStatuses WHERE name LIKE :name ');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $requestsArr = $stmt->fetchColumn();
    return $requestsArr;
}

function updateCroseusRequests()
{
    require_once __DIR__ . "/connectors/croseus.php";
    require_once __DIR__ . "/pdf.php";
    require_once __DIR__ . "/autoLoader.php";
    $affectedRequests = [];
    $arrOfRequests = getCroseusRequestsForUpdate();
    $arrWS = array_column($arrOfRequests, 'externalIdent');
    $arrResults = dejStavDokladuCroseusMulti($arrWS);
    foreach ($arrResults as $request){
        $postArr['requestComment'] = $request['STAV_DETAIL'];
        $originalArr = $arrOfRequests[array_search($request['DOKLAD_EID'], array_column($arrOfRequests, 'externalIdent'))];
        $postArr['idRequest'] = $originalArr['idRequest'];
        $postArr['createdBy'] = 'system';
        $postArr['idNewRequestStatus'] = getCroseusRequestsIdByName($request['STAV_POPIS']);
        if($postArr['idNewRequestStatus'] != $originalArr['idRequestStatus']) {
            insertRequestReaction($postArr);
            array_push($affectedRequests, $postArr['idRequest']);
        }
    }
 return $affectedRequests;
}

function getRequestType($idRequestType)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT * FROM rangeRequestTypes WHERE idRequestType = :idRequestType');
    $stmt->bindValue(':idRequestType', $idRequestType, PDO::PARAM_INT);
    $stmt->execute();
    $requestsArr = $stmt->fetch(PDO::FETCH_ASSOC);
    return $requestsArr;
}

function saveRequestFormTypeJson($idRequestType, $name, $code, $json)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idRequestType)) {
        $stmt = $dbh->getDbLink()->prepare('UPDATE `rangeRequestTypes` SET `name`=:name, formCode = :code, configJson=:json WHERE idRequestType = :idRequestType');
        $stmt->bindValue(':idRequestType', $idRequestType, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':json', $json, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } elseif ($idRequestType === 'new') {
        $stmt = $dbh->getDbLink()->prepare('INSERT INTO `rangeRequestTypes` (`name`,formCode,configJson) VALUES (:name, :code,:json)');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':json', $json, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
            return $lastId;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function nameValueToArray($array) {
    if (!is_array($array)) {
        return false;
    }
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            if(str_contains($value['name'], '[]')){
                $result[str_replace('[]','',$value['name'])][] = $value['value'];
            }
            else{
                $result[$value['name']] = $value['value'];
            }
        }
    }
    return $result;
}

function insertRequest($postArr)
{
    require_once __DIR__ . "/connectors/croseus.php";
    require_once __DIR__ . "/pdf.php";

    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `requestsProject`(`createdBy`, `relatedToProjectId`, `idRequestType`,formData) VALUES (:createdBy,:relatedToProjectId,:idRequestType,:formData)");
        $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':relatedToProjectId', $escapesValuesArr['idProject'], PDO::PARAM_INT);
        $stmt->bindParam(':idRequestType', $escapesValuesArr['idRequestType'], PDO::PARAM_INT);
        $stmt->bindParam(':formData', $postArr['userData'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
            $stmt = $dbh->getDbLink()->prepare("INSERT INTO `requestStatuses`(`idRequest`,`createdBy`,`idRequestStatus`) VALUES (:idRequest,:createdBy,:idRequestStatus)");
            $stmt->bindParam(':idRequest', $lastId, PDO::PARAM_INT);
            $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
            $stmt->bindParam(':idRequestStatus', $escapesValuesArr['idRequestStatus'], PDO::PARAM_INT);
            if ($stmt->execute()) {
                $data = json_decode($postArr['userData'], true);
                $result = [];
                foreach ($data as $item) {
                    $result[$item['name']] = $item['value'];
                }

                $price = str_replace(",", ".", str_replace(" ", "", $result['pricePDVat']));

                if ($escapesValuesArr['croseus'] && $escapesValuesArr['croseus'] === 'true') {
                    $arr =[
                        "NAZEV_PRUVODNIHO_DOKLADU" => $result['requestName'],
                        "TYP_ZADANKY" => "PD",
                        "EXTERNI_IDENTIFIKATOR" => $result['idProject'],
                        "CISLO_ZADANKY" => $lastId,
                        "ORGANIZACNI_JEDNOTKA_EID" => "OBLAST-SPC-PRAHA-EU",
                        "ZALOZIL_EID" => "petr.nadvornik",
                        "ZADAVATEL_EID" => "petr.nadvornik",
                        "TYP_DOKUMENTU_EID" => "VZnad150proadmis",
                        "POPIS" => $result['projectSubject'],
                        "CASTKA_S_DPH" => "$price"
                    ];
                    $croseus = zalozitDokladCroseus($arr);
                    $croseusId = $croseus['DOKLAD_ID'];
                    $croseuslink = $croseus['LINK'];
                    $stmt = $dbh->getDbLink()->prepare('UPDATE `requestsProject` SET externalIdent = :extIdent, `idCroseus`=:idCroseus, linkCroseus = :linkCroseus WHERE idRequest = :idRequest');
                    $stmt->bindValue(':extIdent', $croseus['DOKLAD_EID'], PDO::PARAM_STR);
                    $stmt->bindValue(':idRequest', $lastId, PDO::PARAM_INT);
                    $stmt->bindParam(':idCroseus', $croseusId, PDO::PARAM_STR);
                    $stmt->bindParam(':linkCroseus', $croseuslink, PDO::PARAM_STR);
                    if($stmt->execute()){
                        $pdfContent = getPDFZadankaPD($lastId);
                        $arrValue = [
                            'DOKLAD_EID' => "$croseus[DOKLAD_EID]",
                            'NAZEV'=>"$croseus[DOKLAD_EID].pdf",
                            'DATA'=>$pdfContent
                        ];
                        PridatAktualizovatPrilohu($arrValue);
                    }
                }
                // TODO: add log for requests: insertActionLog(getLastProjectLocalFromProjectId($escapesValuesArr['idProject']), 24, $dbh);
                // TODO: add id and link from CROSEUS response to DB
                return $lastId;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function insertRequestReaction($postArr)
{
    if(!isset($postArr['createdBy'])){
        $postArr['createdBy'] = $_SESSION['username'];
    }
    require_once __DIR__ . "/connectors/croseus.php";
    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `requestStatuses`(`idRequest`,`note`,`createdBy`,`idRequestStatus`) VALUES (:idRequest,:comment,:createdBy,:idRequestStatus)");
        $stmt->bindParam(':idRequest', $postArr['idRequest'], PDO::PARAM_INT);
        $stmt->bindParam(':comment', $postArr['requestComment'], PDO::PARAM_STR);
        $stmt->bindParam(':createdBy', $postArr['createdBy'], PDO::PARAM_STR);
        $stmt->bindParam(':idRequestStatus', $escapesValuesArr['idNewRequestStatus'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/* BYLO PRO DYNAMICKE FORMULARE JQUERY FORM BUILDER, KTERE SE TED NEPOUZIVAJI
function getRequestFormJson($idRequestType)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT configJson FROM rangeRequestTypes WHERE idRequestType = :idRequestType');
    $stmt->bindValue(':idRequestType', $idRequestType, PDO::PARAM_INT);
    $stmt->execute();
    $requestsArr = $stmt->fetch(PDO::FETCH_ASSOC);
    $json = $requestsArr['configJson'];
    return $json;
}
*/

function selectRequestStatuses()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idRequestStatus, name FROM rangeRequestStatuses WHERE isEnabled = 1');
    $stmt->execute();
    $requestsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte stav žádosti</option>";
    foreach ($requestsArr as $requestStatus) {
        $html .= "<option value='$requestStatus[idRequestStatus]' >$requestStatus[name]</option>";
    }
    return $html;
}

function selectSuspensionSources()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idSuspensionSource, name FROM rangeSuspensionSources WHERE hidden = 0');
    $stmt->execute();
    $roadsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "<option disabled='disabled' value=''>Vyberte původce přerušení stavby</option>";
    foreach ($roadsArr as $road) {
        $html .= "<option value='$road[idSuspensionSource]' >$road[name]</option>";
    }
    return $html;
}


function selectFilterCompaniesByType($type = null)
{
    $html = "";
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (!isset($type)) {

        $companiesArr = $dbh->select('rangeCompanies', "1 ");

        foreach ($companiesArr as $key => $company) {
            $html .= "<option value='$company[name]' data-value='$company[idCompany]'>$company[name]</option>";
        }

    } elseif (isset($type) && $type == 'build') {
        $stmt = $dbh->getDbLink()->query("select DISTINCT p2c.idCompany, rangeCompanies.name as name from project2company as p2c JOIN rangeCompanies ON p2c.idCompany = rangeCompanies.idCompany WHERE p2c.idCompanyType = 2");
        $buildCompaniesArr = $stmt->fetchAll();
        foreach ($buildCompaniesArr as $eachCompany) {
            $html .= "<option value='$eachCompany[idCompany]' >$eachCompany[name]</option>";
        }
    } elseif (isset($type) && $type == 'project') {
        $stmt = $dbh->getDbLink()->query("select DISTINCT p2c.idCompany, rangeCompanies.name as name from project2company as p2c JOIN rangeCompanies ON p2c.idCompany = rangeCompanies.idCompany WHERE p2c.idCompanyType = 1");
        $projectCompaniesArr = $stmt->fetchAll();
        foreach ($projectCompaniesArr as $eachCompany) {
            $html .= "<option value='$eachCompany[idCompany]' >$eachCompany[name]</option>";
        }
    } elseif (isset($type) && $type == 'supervisor') {
        $stmt = $dbh->getDbLink()->query("select DISTINCT p2c.idCompany, rangeCompanies.name as name from project2company as p2c JOIN rangeCompanies ON p2c.idCompany = rangeCompanies.idCompany WHERE p2c.idCompanyType = 3");
        $superVisorCompaniesArr = $stmt->fetchAll();
        foreach ($superVisorCompaniesArr as $eachCompany) {
            $html .= "<option value='$eachCompany[idCompany]' >$eachCompany[name]</option>";
        }
    }
    return $html;
}

function arrActiveEditors()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query('SELECT DISTINCT editor, users.name as editorName FROM viewProjectsActive JOIN users ON editor = username');
    $editorsArr = $stmt->fetchAll();
    if (count($editorsArr) > 0) {
        return $editorsArr;
    } else {
        return FALSE;
    }

}

function selectActiveEditors()
{
    $editorsArr = arrActiveEditors();
    $html = "";
    foreach ($editorsArr as $editor) {
        $html .= "<option value='$editor[editor]' data-value='$editor[editor]'>$editor[editorName]</option>";
    }
    return $html;
}

function selectCompanies($selectedId = null)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $companiesArr = $dbh->select('rangeCompanies', "1 ");
    $html = "";
    foreach ($companiesArr as $key => $company) {
        $selected = ($selectedId == $company['idCompany']) ? "selected" : "";
        $html .= "<option $selected value='$company[name]' data-value='$company[idCompany]'>$company[name]</option>";
    }
    return $html;
}



/**
 * @return string
 */
function selectProjects($selectedIds = array())
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query('SELECT DISTINCT idProject, name FROM viewProjectsActive');
    $projectsArr = $stmt->fetchAll();
    $html = "";
    foreach ($projectsArr as $project) {
        $selected = (in_array($project['idProject'], $selectedIds)) ? "selected" : "";
        $title = substr($project['name'], 0, 35);
        $html .= "<option $selected  value='$project[idProject]' >" . $title . " (ID:$project[idProject])</option>";
    }
    return $html;
}

function selectProjectsJSON( $name, $id)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if(is_string($name) && !isset($id)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT DISTINCT idProject as id, CONCAT('[',idProject,'] ', name) as text FROM viewProjectsActive WHERE CONCAT(idProject,' ', name) LIKE  CONCAT('%', :name, '%') ");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    }
    if(isset($id)){
        $stmt = $dbh->getDbLink()->prepare("SELECT DISTINCT idProject as id, CONCAT('[',idProject,'] ', name) as text FROM viewProjectsActive WHERE idProject = :id ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }
    $stmt->execute();
    $projectsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($projectsArr);
}

function selectCompaniesJSON(string $name = '', int $idCompanyType = 1, $id)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if(!isset($id)) {
        $stmt = $dbh->getDbLink()->prepare("select DISTINCT p2c.idCompany as id, rangeCompanies.name as text from project2company as p2c JOIN rangeCompanies ON p2c.idCompany = rangeCompanies.idCompany 
                                                                 WHERE p2c.idCompanyType = :idCompanyType AND CONCAT(rangeCompanies.ic,' ', rangeCompanies.name) LIKE  CONCAT('%', :name, '%')
 ");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':idCompanyType', $idCompanyType, PDO::PARAM_INT);
    }
    if(isset($id)){
        $stmt = $dbh->getDbLink()->prepare("select DISTINCT idCompany as id, name as text from rangeCompanies 
                                                                 WHERE idCompany = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $projectsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($projectsArr);
}

function selectUserProjects()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectsActive WHERE editor = :username ORDER BY idPhase, idProject');
    $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $projectsArr = $stmt->fetchAll();
    return $projectsArr;
}

/**
 * @return string
 */
function selectArea($selectedId)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $areaArr = $dbh->select('rangeAreas', "hidden = FALSE ");
    $html = "<option value=''>Vyberte okres</option>";

    foreach ($areaArr as $area) {
        $selected = ($selectedId == $area['idArea']) ? "selected" : "";
        $html .= "<option $selected value='$area[idArea]'>$area[name]</option>";
    }
    return $html;
}

/**
 * @return string
 */
function selectFinancialSources($selectedId, bool $pd = false /*pokud true, jendá se o financování PD, jinak stavby */)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $sourcesArr = $dbh->select('rangeFinancialSources', "hidden = FALSE ");
    $html = "<option value=''>Vyberte zdroj financování stavby</option>";
    if($pd){
        $html = "<option value=''>Vyberte zdroj financování PD</option>";
    }
    foreach ($sourcesArr as $source) {
        $selected = ($selectedId == $source['idFinSource']) ? "selected" : "";
        $html .= "<option $selected value='$source[idFinSource]'>$source[name]</option>";
    }
    return $html;
}


/**
 * @return string
 */
function selectPhase()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $phasesArr = $dbh->select('rangePhase', "hidden = FALSE ");
    $html = "";
    foreach ($phasesArr as $phase) {
        $html .= "<option value='$phase[idPhase]'>$phase[name]</option>";
    }
    return $html;
}

/**
 * @return string
 */
function selectDocumentsTypes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $docsArr = $dbh->select('rangeDocumentTypes', "hidden = FALSE ");
    $html = "";
    foreach ($docsArr as $doc) {
        $html .= "<option value='$doc[idDocType]'>$doc[name]</option>";
    }
    return $html;
}

/**
 * @return string
 */
function selectProjectTypes($selectedId)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $typesArr = $dbh->select('rangeProjectTypes', " 1");
    $html = "<option disabled='disabled' value=''>Vyberte typ projektu</option>";;
    foreach ($typesArr as $type) {
        $name = ucfirst($type['name']);
        $selected = ($selectedId == $type['idProjectType']) ? "selected" : "";
        $html .= "<option  $selected value='$type[idProjectType]'>$name</option>";
    }
    return $html;
}


function selectProjectSubtypes($supertypeId, $selectedId)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT sp.idProjectSubtype,sp.name FROM rangeProjectSubtypes sp JOIN type2subtype t2s ON sp.idProjectSubtype = t2s.idProjectSubtype WHERE t2s.idProjectType = :idProjectType');
    $stmt->bindParam(':idProjectType', $supertypeId, PDO::PARAM_INT);
    $stmt->execute();
    $subtypes = $stmt->fetchAll();
    $html = "<option disabled='disabled' value=''>Vyberte podtyp projektu</option>";;
    foreach ($subtypes as $type) {
        $selected = ($selectedId == $type['idProjectSubtype']) ? "selected" : "";
        $html .= "<option $selected value='$type[idProjectSubtype]'>$type[name]</option>";
    }
    return $html;
}


function selectObjects()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT idObjectType, name FROM rangeObjectTypes');
    $stmt->execute();
    $objectTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "";
    foreach ($objectTypes as $objectType) {
        $html .= "<option value='$objectType[idObjectType]' >$objectType[name]</option>";
    }
    return $html;
}

function getGlobalFilterName()
{
    if (is_numeric($_SESSION['global_filtr']))
        $name = getOuNameById((int)$_SESSION['global_filtr']);
    elseif ($_SESSION['global_filtr'] === "my")
        $name = "moje projekty";
    else
        $name = "všechny projekty";
    return $name;
}

function dropdownOu()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $ouArr = $dbh->select('ou', ' hidden = FALSE ORDER BY orderNum ASC');
    $html = "";
    foreach ($ouArr as $ou) {
        if ($_SESSION['global_filtr'] == $ou['idOu']) {
            $html .= "<li><a href='#' id-ou='$ou[idOu]' class='dropdown-item global-filter-select active'>$ou[name]</a></li>";

        } else {
            $html .= "<li><a href='#' id-ou='$ou[idOu]' class='dropdown-item global-filter-select'>$ou[name]</a></li>";
        }
    }
    return $html;
}


function getStatsEditor2Projects(array $ouArr = null)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($_SESSION['global_filtr']))
        $where = " WHERE idOu = " . $_SESSION['global_filtr'];
    elseif ($_SESSION['global_filtr'] === "my")
        $where = " WHERE author = '" . $_SESSION['username'] . "' OR editor = '" . $_SESSION['username'] . "'";
    else
        $where = "";
    if (is_array($ouArr)) {
        $where = " WHERE idOu IN (" . implode(",", $ouArr) . ") ";
    }
    $query = $dbh->getDbLink()->query('SELECT DISTINCT editor, COUNT(idProject) as countProjektu
FROM viewProjectsActive' . $where . ' 
GROUP BY (editor)
');
    $graphProjektNaOsobu = $query->fetchAll(PDO::FETCH_ASSOC);
    return $graphProjektNaOsobu;
}

function getPieGraphPhase2Projects(array $ouArr = null)
{


    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($_SESSION['global_filtr']))
        $where = " WHERE viewProjectsActive.idOu = " . $_SESSION['global_filtr'];
    elseif ($_SESSION['global_filtr'] === "my")
        $where = " WHERE viewProjectsActive.author = '" . $_SESSION['username'] . "' OR viewProjectsActive.editor = '" . $_SESSION['username'] . "'";
    else
        $where = "";

    if (is_array($ouArr)) {
        $where = " WHERE viewProjectsActive.idOu IN (" . implode(",", $ouArr) . ")";
    }

    $query = $dbh->getDbLink()->query('SELECT rangePhases.name, COUNT(idProject) as countProjektu
FROM viewProjectsActive 
JOIN rangePhases USING(idPhase) ' . $where . '
GROUP BY idPhase
');
    $graphData = $query->fetchAll(PDO::FETCH_ASSOC);
    return $graphData;

}

function getPieGraphRequestsPerState()
{


    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query('SELECT count(*) as pocet, requestStatus, statusClass  FROM `viewRequests` GROUP BY requestStatus, statusClass;
');
    $graphData = $query->fetchAll(PDO::FETCH_ASSOC);
    return $graphData;

}


function createSelect(array $values, $selectName, $userFriendlySelectName, $selectedValue = null, $multiple = TRUE, $class, $disabled = false)
{
    $disabledHtml = '';
    if ($disabled) {
        $disabledHtml = 'disabled';
    }
    $options = '';
    $html = '';
    foreach ($values as $key => $item) {
        $selected = ($selectedValue === $item) ? "selected" : "";
        $options .= "<option $selected value='$item' >$key</option>";

    }
    if ($multiple) {
        $multipleText = "multiple=''";
    } else {
        $multipleText = "";
    }
    $html =
        "<div class='input-group form-control-lg'>
            <div class='form-group col'>
                <div class='dropdown bootstrap-select show-tick dropup'>
                    <label for='$selectName'>$userFriendlySelectName</label>
                    <select data-msg='Vyberte možnost'  class='selectpicker $class' id='$selectName' $multipleText data-style='select-with-transition' name='$selectName' title='$userFriendlySelectName' tabindex='-98' $disabledHtml>
                        $options
                    </select>
                </div>
            </div>
        </div>";
    return $html;
}

function relationSelects()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeRelationTypes WHERE hidden is not true");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = '';
    foreach ($result as $i => $v) {
        $html .= "
        $v[name]
        <div class='input-group form-control-lg'>
            <div class='form-group col'>
                <div class='dropdown bootstrap-select show-tick dropup'>
                    <select class='selectpicker' multiple='' data-style='select-with-transition' id='relationTypeSelect$v[idRelationType]' name='relationType$v[idRelationType][]' data-live-search='true' title='Projekt' tabindex='-98'>
                        " . selectProjects() . "
                    </select>
                </div>
            </div>
        </div>";
    }
    return $html;
}

//TODO eliminate an apply relationSelectsNew
function selectRelations($selectedId)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $relationsArr = $dbh->select('rangeRelationTypes', "hidden = FALSE ");
    if ($selectedId == 0 || $selectedId == null) {
        $html = "<option value='0' selected >Bez relace</option>";
    } else {
        $html = "<option value='0' >Bez relace</option>";
    }

    foreach ($relationsArr as $relation) {
        $selected = ($selectedId == $relation['idRelationType']) ? "selected" : "";
        $html .= "<option $selected value='$relation[idRelationType]' >$relation[name]</option>";
    }
    return $html;
}

function getRlationTypes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeRelationTypes WHERE hidden is not true");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function relationSelectsNew($relationType, $order, $selecetedIds = array())
{
    return "<div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                        <i class='material-icons'>compare_arrows</i>
                    </span>
                </div>
                <div class='form-group col bmd-form-group'>
                    <div class='form-group'>
                        <span style='font-size:1rem'>" . $relationType['name'] . "</span>
                        <div class='dropdown bootstrap-select show-tick dropup'>
                            <select class='selectpicker' multiple='' data-style='select-with-transition' name='relation[$order][idProject][]' data-live-search='true' title='Projekt' tabindex='-98'>
                                " . selectProjects($selecetedIds) . "
                            </select>
                            <input type='hidden' name='relation[$order][idRelationType]' value=" . $relationType['idRelationType'] . ">
                        </div>
                    </div>
                </div>
            </div>";

}

/**
 * @return string
 */
function selectProjectTypesForm($selectedId)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $typesArr = $dbh->select('rangeProjectTypes', " 1");
    $html = "<option value=''>Vyberte typ projektu</option>>";
    foreach ($typesArr as $type) {
        $selected = ($selectedId == $type['idProjectType']) ? "selected" : "";
        $html .= "<option $selected value='$type[name]'></option>";
    }
    return $html;
}

/**
 * @return string
 */
function selectEditors($defaultEditor = null)
{
    require_once __DIR__ . "/autoLoader.php";
    $html = "<option value=''>Vyberte editora</option>";
    if (!isset($defaultEditor) or $_SESSION['role'] == 'adminEditor') {
        if ($_SESSION['role'] == 'adminEditor') {
            $dbh = new DatabaseConnector();
            $editorsArr = $dbh->select('users', "idRoleType = 1 OR idRoleType = 2 ");

            foreach ($editorsArr as $eachEditor) {

                if ($_SESSION['username'] == $eachEditor['username'] && !isset($defaultEditor)) {
                    $html .= "<option value='$eachEditor[username]' selected>$eachEditor[name]</option>";
                } elseif (isset($defaultEditor) && $defaultEditor == $eachEditor['username']) {
                    $html .= "<option value='$defaultEditor' selected>$eachEditor[name]</option>";
                } else {
                    $html .= "<option value=$eachEditor[username]>$eachEditor[name]</option>";
                }
            }
        }
        if ($_SESSION['role'] == 'editor') {
            $html = "<option value=''>Vyberte editora</option><option value='$_SESSION[username]' selected>$_SESSION[jmeno]</option>";
        }
    }
    if (isset($defaultEditor) && $_SESSION['role'] != 'adminEditor') {
        $html = "<option value=''>Vyberte editora</option><option value='$defaultEditor' selected>$_SESSION[jmeno]</option>";
    }

    return $html;
}


/**
 * @param $role
 * @return string
 */
function selectContacts($role)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT DISTINCT rangeContacts.idContact, CONCAT(contacts.name, ' (', rangeCompanies.name,')') as optionName FROM projects JOIN rangeContacts ON projects.$role = rangeContacts.idContact JOIN rangeCompanies USING(idCompany) WHERE rangeContacts.active = 1");
    $contactsArr = $stmt->fetchAll();
    $html = "";
    foreach ($contactsArr as $contact) {
        $html .= "<option value='$contact[idContact]' >$contact[optionName]</option>";
    }
    return $html;

}

function selectContactsAll($selectedName)
{
    $return = array();
    $html = null;
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT idContact, name, phone, email FROM rangeContacts WHERE active = 1");
    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $contact) {
            $selected = ($selectedName == $contact['name']) ? "selected" : "";
            $html .= "<option value='$contact[name]' $selected data-value='$contact[idContact]'>$contact[name]</option>";
        }
    }

    return $html;

}

function selectContactsAllJSON($name, $idContact)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if($name != '') {
        $stmt = $dbh->getDbLink()->prepare("SELECT idContact as id, CONCAT(name, ', tel: ', phone,' (', email,')') as text FROM rangeContacts WHERE active = 1 AND CONCAT(name, ', tel: ', phone,' (', email,')') LIKE  CONCAT('%', :name, '%') ");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    }
    if(isset($idContact)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT idContact as id, CONCAT(name, ', tel: ', phone,' (', email,')') as text FROM rangeContacts WHERE active = 1 AND idContact = :idContact");
        $stmt->bindParam(':idContact', $idContact, PDO::PARAM_INT);
    }
    if($name == '') {
        $stmt = $dbh->getDbLink()->prepare("SELECT idContact as id, CONCAT(name, ', tel: ', phone,' (', email,')') as text FROM rangeContacts WHERE active = 1");
    }

    if ($stmt->execute()) {$projectsArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($projectsArr);
    }
}

/**
 * @return string
 */
function selectFilterAreas()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT DISTINCT idArea, rangeAreas.name as optionName FROM project2area JOIN rangeAreas USING(idArea)");
    $areasArr = $stmt->fetchAll();
    $html = "";
    foreach ($areasArr as $area) {
        $html .= "<option value='$area[idArea]' >$area[optionName]</option>";
    }
    return $html;

}

/**
 * @return string
 */
function selectFilterProjectTypes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT DISTINCT idProjectType,  rangeProjectTypes.name as optionName FROM projects JOIN rangeProjectTypes USING(idProjectType)");
    $typesArr = $stmt->fetchAll();
    $html = "";
    foreach ($typesArr as $type) {
        $html .= "<option value='$type[idProjectType]' >$type[optionName]</option>";
    }
    return $html;

}

function selectFilterProjectSubtypes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT DISTINCT idProjectSubtype,  rangeProjectSubtypes.name as optionName FROM projects JOIN rangeProjectSubtypes USING(idProjectSubtype)");
    $typesArr = $stmt->fetchAll();
    $html = "";
    foreach ($typesArr as $type) {
        $html .= "<option value='$type[idProjectSubtype]' >$type[optionName]</option>";
    }
    return $html;

}

/**
 * @return string
 */
function selectFilterFinSource()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT DISTINCT idFinSource, rangeFinancialSources.name as optionName FROM projects JOIN  rangeFinancialSources USING(idFinSource)");
    $typesArr = $stmt->fetchAll();
    $html = "";
    foreach ($typesArr as $type) {
        $html .= "<option value='$type[idFinSource]' >$type[optionName]</option>";
    }
    return $html;

}

/**
 * @return string
 */
function selectFilterRoads()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT DISTINCT idCommunication,  rangeCommunications.name as optionName FROM project2communication JOIN rangeCommunications USING(idCommunication)");
    $roadsArr = $stmt->fetchAll();
    $html = "";
    foreach ($roadsArr as $road) {
        $html .= "<option value='$road[idCommunication]' >$road[optionName]</option>";
    }
    return $html;

}

/**
 * @return string
 */
function generateCheckboxes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $phasesArr = $dbh->select('rangePhases', "hidden = FALSE ORDER BY level ASC");
    $count = count($phasesArr);
    $html = "";
    $pom = 0;

    foreach ($phasesArr as $key => $value) {
        if ((($key) % 2) == 0) {
            if ($key > 1) {
                $html .= "</div>";
            }
            $html .= "<div class=\"col checkbox-radios\">";
            $pom++;

        }
        if ($value['idPhase'] == 6) {
            $html .= "<div class=\"form-check\">
                        <label class=\"form-check-label\">
                            <input class=\"form-check-input phaseCheckbox\" type=\"checkbox\"  value='$value[idPhase]'> $value[name]
                            <span class=\"form-check-sign\">
                              <span class=\"check\"></span>
                            </span>
                        </label>
                    </div>";
        } else {
            $html .= "<div class=\"form-check\">
                        <label class=\"form-check-label\">
                            <input class=\"form-check-input phaseCheckbox\" type=\"checkbox\" checked value='$value[idPhase]'> $value[name]
                            <span class=\"form-check-sign\">
                              <span class=\"check\"></span>
                            </span>
                        </label>
                    </div>";
        }
        if ($count == $key + 1) {
            $html .= "</div>";
        }
    }
    return $html;

}

function selectPhases()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $phasesArr = $dbh->select('rangePhases', "hidden = FALSE ORDER BY level ASC");
    $count = count($phasesArr);
    $html = "";
    foreach ($phasesArr as $value) {
        if ($value['idPhase'] == 6) {
            $html .= "<option  value='$value[idPhase]'>$value[name]</option>";
        } else {
            $html .= "<option selected value='$value[idPhase]'>$value[name]</option>";
        }

    }
    return $html;

}

/**
 * @param $name
 * @return bool|string
 */
function insertTypeProject($name)
{
    $lastId = false;
    $name = htmlspecialchars($name);
    if (!findTypeProject($name)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('INSERT INTO `typeProject`(`name`) VALUES (:name)');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
        }
    }
    return $lastId;
}

function deleteCompany($idCompany)
{
    if (is_numeric($idCompany)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("DELETE FROM `rangeCompanies` WHERE idCompany = :idCompany");
        $stmt->bindParam(':idCompany', $idCompany, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function deleteContact($idContact)
{
    if (is_numeric($idContact)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("DELETE FROM `rangeContacts` WHERE idContact = :idContact");
        $stmt->bindParam(':idContact', $idContact, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function deleteDeadline($idProject, $idDeadlineType)
{
    if (is_numeric($idProject) && is_numeric($idDeadlineType)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("DELETE FROM `deadlines` WHERE idProject = :idProject AND idDeadlineType = :idDeadlineType");
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindParam(':idDeadlineType', $idDeadlineType, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function deleteFileType($idFileType)
{
    if (is_numeric($idFileType)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("DELETE `rangeDocumentTypes` WHERE idFileType = :idFileType");
        $stmt->bindParam(':idFileType', $idFileType, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function deleteCollaboration($id)
{
    if (is_numeric($id)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `collaborator` SET active = 0 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function findCompanyById($idCompany)
{
    if (is_numeric($idCompany)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("SELECT *, MD5(CONCAT(idCompany, name,address,ic,dic,www)) as hash FROM rangeCompanies WHERE idCompany = :idCompany");
        $stmt->bindParam(':idCompany', $idCompany, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $returnArr = $stmt->fetch();
            return $returnArr;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// HASH byl zakomentovan protoze nefunguje, kdyz neni vyplneny nektery sloupec - napr firma bez www nesla updatnout...
function editCompany($postArr)
{
    $affectedCount = false;
    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        if (isset($postArr['idCompany']) && is_numeric($postArr['idCompany'])) {
            $originCompany = findCompanyById($escapesValuesArr['idCompany']);
            if (is_array($originCompany)) {  //&& isset($originCompany['hash'])
                require_once __DIR__ . "/autoLoader.php";
//                $updatedHash = md5(implode('', $escapesValuesArr));
//                if ($updatedHash != $originCompany['hash']) {
                $dbh = new DatabaseConnector();
                $stmt = $dbh->getDbLink()->prepare("UPDATE `rangeCompanies` SET `name`=:name, `address` = :address, `ic` = :ic, `dic` = :dic, `www` = :www WHERE idCompany = :idCompany");
                $stmt->bindParam(':idCompany', $escapesValuesArr['idCompany'], PDO::PARAM_INT);
                $stmt->bindParam(':name', $escapesValuesArr['name'], PDO::PARAM_STR);
                $stmt->bindParam(':address', $escapesValuesArr['address'], PDO::PARAM_STR);
                $stmt->bindParam(':ic', $escapesValuesArr['ic'], PDO::PARAM_INT);
                $stmt->bindParam(':dic', $escapesValuesArr['dic'], PDO::PARAM_STR);
                $stmt->bindParam(':www', $escapesValuesArr['www'], PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $affectedCount = $stmt->rowCount();
                }
//                }
            }
        }
    }
    return $affectedCount;
}

function insertCompany($postArr)
{
    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();

        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `rangeCompanies`(`name`, `address`, `ic`, `dic`, `www`) VALUES (:name,:address,:ic,:dic,:www)");
        $stmt->bindParam(':name', $escapesValuesArr['name'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $escapesValuesArr['address'], PDO::PARAM_STR);
        $stmt->bindParam(':ic', $escapesValuesArr['ic'], PDO::PARAM_INT);
        $stmt->bindParam(':dic', $escapesValuesArr['dic'], PDO::PARAM_STR);
        $stmt->bindParam(':www', $escapesValuesArr['www'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
            return $lastId;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/* TASKS FUNCTIONS */
function insertTask($postArr)
{
    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `tasksProject`(`createdBy`, `relatedToProjectId`) VALUES (:createdBy,:relatedToProjectId)");
        $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':relatedToProjectId', $escapesValuesArr['idProject'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
            if (!empty($escapesValuesArr['deadline'])) {
                $deadline = date("Y-m-d H:i:s", strtotime($escapesValuesArr['deadline']));
                $stmt = $dbh->getDbLink()->prepare("INSERT INTO `taskVersions`(`idTask`,`name`, `description`,`createdBy`,`idTaskStatus`,`deadlineTo`) VALUES (:idTask,:name,:description,:createdBy,:idTaskStatus,:deadlineTo)");
                $stmt->bindParam(':idTask', $lastId, PDO::PARAM_INT);
                $stmt->bindParam(':name', $escapesValuesArr['name'], PDO::PARAM_STR);
                $stmt->bindParam(':description', $escapesValuesArr['description'], PDO::PARAM_STR);
                $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
                $stmt->bindParam(':idTaskStatus', $escapesValuesArr['idTaskStatus'], PDO::PARAM_INT);
                $stmt->bindParam(':deadlineTo', $deadline, PDO::PARAM_STR);
            } else {
                $stmt = $dbh->getDbLink()->prepare("INSERT INTO `taskVersions`(`idTask`,`name`, `description`,`createdBy`,`idTaskStatus`) VALUES (:idTask,:name,:description,:createdBy,:idTaskStatus)");
                $stmt->bindParam(':idTask', $lastId, PDO::PARAM_INT);
                $stmt->bindParam(':name', $escapesValuesArr['name'], PDO::PARAM_STR);
                $stmt->bindParam(':description', $escapesValuesArr['description'], PDO::PARAM_STR);
                $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
                $stmt->bindParam(':idTaskStatus', $escapesValuesArr['idTaskStatus'], PDO::PARAM_INT);
            }
            if ($stmt->execute()) {
                insertActionLog(getLastProjectLocalFromProjectId($escapesValuesArr['idProject']), 24, $dbh);
                return $lastId;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function updateTask($postArr)
{
    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $deadline = date("Y-m-d H:i:s", strtotime($escapesValuesArr['deadline']));
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `taskVersions`(`idTask`,`name`, `description`,`createdBy`,`idTaskStatus`,`deadlineTo`) VALUES (:idTask,:name,:description,:createdBy,:idTaskStatus,:deadlineTo)");
        $stmt->bindParam(':idTask', $escapesValuesArr['idTask'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $escapesValuesArr['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $escapesValuesArr['description'], PDO::PARAM_STR);
        $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':idTaskStatus', $escapesValuesArr['idTaskStatus'], PDO::PARAM_INT);
        $stmt->bindParam(':deadlineTo', $deadline, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
            insertActionLog(getLastProjectLocalFromProjectId($escapesValuesArr['idProject']), 6, $dbh);
            return $lastId;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function insertTaskReaction($postArr)
{
    if (is_array($postArr)) {
        $escapesValuesArr = htmlspecialcharsArr($postArr);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `taskReactions`(`createdBy`, `reaction`, `idTask`) VALUES (:createdBy, :reaction, :idTask)");
        $stmt->bindParam(':createdBy', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':idTask', $escapesValuesArr['idTask'], PDO::PARAM_INT);
        $stmt->bindParam(':reaction', $escapesValuesArr['reaction'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
            return $lastId;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getRequests()
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare(
        "SELECT * FROM `viewRequests` ORDER BY updated DESC");
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getRequestsTable(array $data){
    $hashToken = generateHash(date('H'));
    $html = "";
    foreach ($data as $row){
        $disabledReaction = '';
        if($row['isTerminal']){
            $disabledReaction = 'disabled';
        }
        if($row['croseus'] && !$row['isTerminal'] && !$row['croseusTerminal']){
            $disabledReaction = 'disabled';
        }
        $croseus = $row['idCroseus'] ? "<td>
<a href='$row[linkCroseus]' target='_blank' data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"Zobrazit žádanku v Croseusu\"><img alt='croseus icon' src='https://croseus.cz/MSK/SOS/favicon.ico'></a></td>
<td>
    <i class='fa fa-question-circle croseus_status' data-toggle=\"tooltip\" data-placement=\"left\" extIdent='$row[externalIdent]' data-original-title=\"Status v croseu\"></i>
 </td>" : "<td></td><td></td>";
        $html .= "<tr id='idRequest$row[idRequest]'>
<td>$row[externalIdent]</td>              
                     $croseus
                    <td>[$row[relatedToProjectId]] $row[projectName]</td>
                    <td>$row[requestName]</td>
                      <td>$row[createdBy]</td>
                     <td>$row[created]</td>
                      <td>$row[requestStatus]</td>
                    <td>$row[updated]</td>
                    <td>$row[versionsCount]</td>
                     <td>
                      
    <button class='btn btn-sm btn-success add-request-reaction float-right' $disabledReaction token='$hashToken' id-request='$row[idRequest]' request-rank='$row[rank]' data-toggle='modal'  data-original-title=\"Změnit status nebo přidat komentář\">
    <i class='fa fa-plus-square'></i></button>
    <a href='/submits/getPdf.php?idRequest=$row[idRequest]&externalIdent=$row[externalIdent]'> <button class='btn btn-sm btn-info float-right'  data-original-title=\"Zobraz PDF\">
    <i class='fa fa-print'></i></button></a>
    <button class='btn btn-sm btn-primary float-right show-request-form' id-request='$row[idRequest]' token='$hashToken' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Zobraz formulář\">
    <i class='fa fa-eye'></i>
    </button>
    
    <a href='detail.php?idProject=$row[relatedToProjectId]'>
    <button class='btn btn-sm btn-info float-right show-form' >
    <i class='fa fa-search' data-toggle=\"tooltip\" data-placement=\"left\" id-project='$row[relatedToProjectId]' data-original-title=\"Zobraz projekt\"></i>
    </button>
    </a>
    

    
                     </td>
                    
                </tr>";
    }
    return $html;
}

function deleteTask($idTask)
{
    if (is_numeric($idTask)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `tasksProject` SET `deletedBy` = :deletedBy, `deleted` = NOW() WHERE `tasksProject`.`idTask` = :idTask");
        $stmt->bindParam(':idTask', $idTask, PDO::PARAM_INT);
        $stmt->bindParam(':deletedBy', $_SESSION['username'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function deleteTaskReaction($idTask, $created)
{
    if (is_numeric($idTask)) {
        $created = date('Y-m-d H:i:s', strtotime($created));
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `taskReactions` SET `deletedBy` = :deletedBy, `deleted` = NOW() WHERE idTask = :idTask AND created = :created");
        $stmt->bindParam(':idTask', $idTask, PDO::PARAM_INT);
        $stmt->bindParam(':deletedBy', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':created', $created, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function geteZakStatus(){
    require_once SYSTEMINCLUDES . "/simple_html_dom.php";
    $html = file_get_html('https://zakazky.kr-stredocesky.cz/contract_display_15860.html');
    // Najdi element s ID "constract-status"
    //$element = $html->find('#contract-status', 0);
    $element2 = $html->find('#body_info');
// Zkontroluj, zda element existuje
    print_r($element2);
    $foo = $element2->find('[plaintext^=Datum zahájení:]');
    $dateElement = $foo->next_sibling()->find('b', 0);
    print_r($dateElement);


   /* if ($element) {
        // Najdi <b> uvnitř tohoto elementu
        $boldElement = $element->find('b', 0);

        // Zkontroluj, zda <b> existuje, a získej jeho text
        if ($boldElement) {
            echo $boldElement->plaintext;
        } else {
            echo '<b> tag uvnitř nebyl nalezen.';
        }
    } else {
        echo 'Element s ID "constract-status" nebyl nalezen.';
    }*/
}

function getTasksForProject($idProject)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT tasksProject.idTask, taskVersions.name, taskVersions.description, tasksProject.created AS created, taskVersions.created as lastUpdate, tasksProject.createdBy as createdBy, taskVersions.createdBy as updatedBy, tasksProject.relatedToProjectId as idProject, taskVersions.deadlineTo as deadline, rangeTaskStatuses.name as status, rangeTaskStatuses.idTaskStatus, rangeTaskStatuses.statusColor, rangeTaskStatuses.statusClass FROM taskVersions JOIN tasksProject USING (idTask) JOIN rangeTaskStatuses USING (idTaskStatus) WHERE taskVersions.created IN (SELECT MAX(created) as lastVersion from taskVersions GROUP BY idTask) AND tasksProject.deleted IS NULL AND tasksProject.relatedToProjectId = :idProject");
    $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getRequestsForProject($idProject)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `viewRequests` WHERE relatedToProjectId = :idProject");
    $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}


function getLastRequestStatus($idRequest)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT  requestsProject.formData, requestStatuses.idRequestStatus, requestsProject.idRequestType, requestsProject.relatedToProjectId as idProject, requestsProject.idCroseus, requestsProject.linkCroseus,requestsProject.created as created_at, requestStatuses.created as updated_at, externalIdent
        
FROM `requestStatuses` 
    JOIN requestsProject ON requestStatuses.idRequest = requestsProject.idRequest 
WHERE requestStatuses.idRequest = :idRequest AND requestStatuses.created = (select max(created) from requestStatuses where idRequest = :idRequest)");
    $stmt->bindValue(':idRequest', $idRequest, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetch(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getTasksForUser($username = NULL, $showTerminatedTasks = FALSE)
{
    if (!$username) {
        $username = $_SESSION['username'];
    }
    if ($showTerminatedTasks) {
        $terminated = "";
    } else {
        $terminated = "AND tasksProject.deleted IS NULL";
    }
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT tasksProject.idTask, taskVersions.name, viewProjectsActive.name as projectName, taskVersions.description, tasksProject.created AS created, taskVersions.created as lastUpdate, tasksProject.createdBy as createdBy, taskVersions.createdBy as updatedBy, tasksProject.relatedToProjectId as idProject, taskVersions.deadlineTo as deadline, rangeTaskStatuses.name as status, rangeTaskStatuses.idTaskStatus, rangeTaskStatuses.statusColor, rangeTaskStatuses.statusClass FROM `viewProjectsActive` JOIN tasksProject ON viewProjectsActive.idProject=tasksProject.relatedToProjectId JOIN taskVersions ON tasksProject.idTask=taskVersions.idTask JOIN rangeTaskStatuses ON taskVersions.idTaskStatus=rangeTaskStatuses.idTaskStatus WHERE viewProjectsActive.editor = :username $terminated AND rangeTaskStatuses.isTerminal = 0 AND taskVersions.created IN (SELECT MAX(created) as lastVersion from taskVersions GROUP BY idTask)");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getTaskDetails($idTask)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT tasksProject.idTask, taskVersions.name, taskVersions.description, tasksProject.created AS created, taskVersions.created as lastUpdate, tasksProject.createdBy as createdBy, taskVersions.createdBy as updatedBy, viewProjectsActive.editor, tasksProject.relatedToProjectId as idProject, taskVersions.deadlineTo as deadline, rangeTaskStatuses.name as status, rangeTaskStatuses.idTaskStatus, rangeTaskStatuses.statusColor, rangeTaskStatuses.statusClass FROM `viewProjectsActive` JOIN tasksProject ON viewProjectsActive.idProject=tasksProject.relatedToProjectId JOIN taskVersions ON tasksProject.idTask=taskVersions.idTask JOIN rangeTaskStatuses ON taskVersions.idTaskStatus=rangeTaskStatuses.idTaskStatus WHERE tasksProject.idTask = :idTask AND tasksProject.deleted IS NULL AND rangeTaskStatuses.isTerminal = 0 AND taskVersions.created IN (SELECT MAX(created) as lastVersion from taskVersions GROUP BY idTask)");
    $stmt->bindValue(':idTask', $idTask, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetch(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getTaskReactions($idTask)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `taskReactions` WHERE idTask = :idTask AND deleted IS NULL ORDER BY created ASC");
    $stmt->bindValue(':idTask', $idTask, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getRequestReactions($idRequest)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `requestStatuses` JOIN rangeRequestStatuses ON requestStatuses.idRequestStatus = rangeRequestStatuses.idRequestStatus WHERE requestStatuses.idRequest = :idRequest  ORDER BY requestStatuses.created ASC");
    $stmt->bindValue(':idRequest', $idRequest, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getTaskReactionsForVersionHistory($idTask, $created)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `taskReactions` WHERE idTask = :idTask AND created < :created AND (deleted IS NULL OR deleted > :created) ORDER BY created ASC");
    $stmt->bindValue(':idTask', $idTask, PDO::PARAM_INT);
    $stmt->bindParam(':created', $created, PDO::PARAM_STR);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function listTasks($idProject = NULL, $username = NULL, $showProject = FALSE, $sizeLg = 4, $sizeMd = 6, $showControlButtons = TRUE)
{
    if ($idProject) {
        $tasks = getTasksForProject($idProject);
    } elseif ($username) {
        $tasks = getTasksForUser($username);
    }
    if ($tasks) {
        $html = "";
        foreach ($tasks as $task) {
            $reactions = getTaskReactions($task['idTask']);
            $reactionsHtml = "";
            foreach ($reactions as $reaction) {
                if ($reaction['createdBy'] === $_SESSION['username'] || $_SESSION['role'] === "adminEditor") {
                    $deleteReaction = "<span class='float-right delete-reaction card-category' task-id='" . $task['idTask'] . "' project-id='" . ($idProject ? $idProject : $username) . "' created='" . $reaction['created'] . "'><i class='material-icons ' style='font-size: small' task-id='" . $task['idTask'] . "' project-id='" . ($idProject ? $idProject : $username) . "' created='" . $reaction['created'] . "'>close</i></span>";
                } else {
                    $deleteReaction = "";
                }
                $reactionsHtml .= "
                <div class='card mt-1 mb-1'><div class='card-body p-2'><div class='media'>
            <img alt='sidebar bacground' class='align-self-start mr-3' style='width: 30px' data-toggle='tooltip' data-placement='top' title='Vložil " . getUserAll($reaction['createdBy'])[0]['name'] . " " . date("d.m.Y H:i", strtotime($reaction['created'])) . "' src='data:image/png;base64," . createAvatar(getInitialsFromName(getUserAll($reaction['createdBy'])[0]['name'])) . "'/>
        <div class='media-body reaction-text'>" . $reaction['reaction'] . "</div>
        $deleteReaction
        </div></div></div>
                ";
            }
            if ($task['deadline']) {
                $deadline = "<span data-toggle='tooltip' data-placement='top' title='Termín'><i class='fa fa-bell'></i> " . date("d.m.Y", strtotime($task['deadline'])) . "</span><br>";
            } else {
                $deadline = "";
            }
            if ($showProject) {
                $projectRow = "<div class='row'><div class='col col-12'><a href='detail.php?idProject=" . $task['idProject'] . "#assignments" . $task['idProject'] . "' data-toggle='tooltip' data-placement='top' data-original-title='Přejít na detail projektu ID " . $task['idProject'] . "'><i class='fa fa-sign-in-alt'></i> " . $task['projectName'] . " (ID: " . $task['idProject'] . ")</a></div></div>";
            } else {
                $projectRow = "";
            }
            $html .= "
        <div class='col col-12 col-md-" . $sizeMd . " col-lg-" . $sizeLg . "'>
            <div class='card card-chart'>
                <div class='card-header card-header-text card-header-" . $task['statusClass'] . "'>
                    <div class='card-text'>
                        <h4 class='card-title' id='taskName" . $task['idTask'] . "'>" . $task['name'] . "</h4>
                    </div>
                </div>
                <div class='card-body'>
                    <h5 class='card-title' id='taskDescription" . $task['idTask'] . "'>" . $task['description'] . "</h5>
                    <span id='taskReactions" . $task['idTask'] . "'>$reactionsHtml</span>
                </div>
                <div class='card-footer'>
                    <div class='w-100'>
                        $projectRow
                        <div class='row'>
                            <div class='col col-6'>
                                <p class='card-category'>
                                    $deadline
                                    <span data-toggle='tooltip' data-placement='top' title='Stav řešení'><i class='fa fa-circle' style='color: " . $task['statusColor'] . "'></i> " . $task['status'] . "</span><br>
                                    <span data-toggle='tooltip' data-placement='top' title='Autor úkolu'><i class='fa fa-user'></i> " . getUserAll($task['createdBy'])[0]['name'] . "</span><br>
                                </p>
                            </div>
                            <div class='col col-6'>";
            if (in_array($_SESSION['role'], ['editor','adminEditor','admin']) && $showControlButtons) $html .= "
                                <button class='btn btn-link btn-danger btn-just-icon remove-task float-right' task-id='" . $task['idTask'] . "' project-id='" . ($idProject ? $idProject : $username) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Smazat úkol'>close</i></button>
                                <button class='btn btn-link btn-primary btn-just-icon edit-task float-right' task-id='" . $task['idTask'] . "' project-id='" . ($idProject ? $idProject : $username) . "' status-id='" . $task['idTaskStatus'] . "' deadline='" . ($task['deadline'] ? date("d.m.Y", strtotime($task['deadline'])) : $task['deadline']) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Upravit úkol'>edit</i></button>
                                <button class='btn btn-link btn-primary btn-just-icon add-task-comment float-right' task-id='" . $task['idTask'] . "' project-id='" . ($idProject ? $idProject : $username) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Přidat reakci'>add_comment</i></button>";
            if ($showControlButtons) $html .= "<button class='btn btn-link btn-primary btn-just-icon task-history float-right' task-id='" . $task['idTask'] . "' project-id='" . ($idProject ? $idProject : $username) . "' status-id='" . $task['idTaskStatus'] . "' deadline='" . ($task['deadline'] ? date("d.m.Y", strtotime($task['deadline'])) : $task['deadline']) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Historie stavů úkolu'>youtube_searched_for</i></button>";
            $html .= "                </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
        ";
        }
        return $html;
    } else {
        return "<div class='col col-12'>Žádné úkoly k zobrazení.</div>";
    }
}

function getTaskHistory($idTask)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT taskVersions.idTask, taskVersions.name, taskVersions.description, taskVersions.created as created, taskVersions.createdBy as createdBy, taskVersions.deadlineTo as deadline, rangeTaskStatuses.name as status, rangeTaskStatuses.idTaskStatus, rangeTaskStatuses.statusColor, rangeTaskStatuses.statusClass FROM `taskVersions` JOIN rangeTaskStatuses USING (idTaskStatus) WHERE taskVersions.idTask = :idTask ORDER BY created ASC");
    $stmt->bindValue(':idTask', $idTask, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataArr;
}

function listTaskHistory($idTask)
{
    $taskHistory = getTaskHistory($idTask);
    if ($taskHistory) {
        $html = "";
        foreach ($taskHistory as $key => $task) {
            $reactions = getTaskReactions($task['idTask']);
            $reactionsHtml = "";
            foreach ($reactions as $reaction) {
                if ((strtotime($reaction['created']) < strtotime($task['created'])) || ((strtotime($reaction['created']) > strtotime($task['created'])) && (strtotime($reaction['created']) < strtotime($taskHistory[$key + 1]['created']))) || ($key + 1 === count($taskHistory))) {
                    $reactionsHtml .= "
                <div class='card mt-1 mb-1'><div class='card-body p-2'><div class='media'>
            <img alt='sidebar bacground' class='align-self-start mr-3' style='width: 30px' data-toggle='tooltip' data-placement='top' title='Vložil " . getUserAll($reaction['createdBy'])[0]['name'] . " " . date("d.m.Y H:i", strtotime($reaction['created'])) . "' src='data:image/png;base64," . createAvatar(getInitialsFromName(getUserAll($reaction['createdBy'])[0]['name'])) . "'/>
        <div class='media-body'>" . $reaction['reaction'] . "</div></div></div></div>
                ";
                }
            }
            if (++$key === count($taskHistory)) {
                $arrowRight = "";
            } else {
                $arrowRight = "<div style='float: right; position: absolute; top: 47%; right: -5px;'><i class='fa fa-arrow-right text-gray'></i></div>";
            }
            if ($task['deadline']) {
                $deadline = "<span data-toggle='tooltip' data-placement='top' title='Termín'><i class='fa fa-bell'></i> " . date("d.m.Y", strtotime($task['deadline'])) . "</span><br>";
            } else {
                $deadline = "";
            }
            $html .= "
        <div class='col col-12 col-md-6 col-lg-4 col-xl-3'>
            $arrowRight
            <div class='card card-chart'>
                <div class='card-header card-header-text card-header-" . $task['statusClass'] . "'>
                    <div class='card-text'>
                        <h4 class='card-title'>" . $task['name'] . "</h4>
                    </div>
                </div>
                <div class='card-body'>
                    <h5 class='card-title'>" . $task['description'] . "</h5>
                    <span>$reactionsHtml</span>
                </div>
                <div class='card-footer'>
                    <div class='w-100'>
                        <div class='row'>
                            <div class='col col-6'>
                                <p class='card-category'>
                                    $deadline
                                    <span data-toggle='tooltip' data-placement='top' title='Stav řešení'><i class='fa fa-circle' style='color: " . $task['statusColor'] . "'></i> " . $task['status'] . "</span><br>
                                </p>
                            </div>
                            <div class='col col-6'>
                                <span data-toggle='tooltip' data-placement='top' title='Datum změny'><i class='fa fa-clock'></i> " . date("d.m.Y H:i", strtotime($task['created'])) . "</span><br>
                                <span data-toggle='tooltip' data-placement='top' title='Autor změny'><i class='fa fa-user'></i> " . getUserAll($task['createdBy'])[0]['name'] . "</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
        ";
        }
        return $html;
    } else {
        return "Chyba: nepovedlo se načíst verze úkolu.";
    }
}

function projectTasksNotification()
{
    $counter = count(getTasksForUser());
    if ($counter > 0) {
        echo "<span class=\"notification\" id='taskNotificationNumber'>$counter</span>";
    }
}

function projectTasksNotificationText()
{
    $counter = count(getTasksForUser());
    echo tasksCzech($counter) . " na projektech.";
}

function tasksCzech($counter)
{
    if ($counter == 0) {
        return "Nemáte žádné aktivní úkoly";
    } elseif ($counter == 1) {
        return "Máte 1 aktivní úkol";
    } elseif ($counter < 5) {
        return "Máte " . $counter . " aktivní úkoly";
    } else {
        return "Máte " . $counter . " aktivních úkolů";
    }
}

/* REQUESTS FUNCTIONS */
function listRequests($idProject = NULL, $username = NULL, $showProject = FALSE, $sizeLg = 6, $sizeMd = 6, $showControlButtons = TRUE)
{
   // return "Tady budou žádosti.";

        $requests = getRequestsForProject($idProject);
    if ($requests) {
        $html = "";
        foreach ($requests as $request) {
            $disabledReaction = '';
            if($request['isTerminal']){
                $disabledReaction = 'disabled';
            }
            if($request['croseus'] && !$request['isTerminal'] && !$request['croseusTerminal']){
                $disabledReaction = 'disabled';
            }

            $reactions = getRequestReactions($request['idRequest']);
            $reactionsHtml = "<ul class='timeline timeline-simple'>";
            foreach ($reactions as $reaction) {
                $reactionsHtml .= "
                <li class='timeline-inverted'>
                  <div class='timeline-badge ".$reaction['statusClass']."'>
                         
                  </div>
                  <div class='timeline-panel'>
                    <div class='timeline-heading'>
                      <span class='badge badge-pill badge-".$reaction['statusClass']."'>".$reaction['name']."</span>
                    </div>
                    <div class='timeline-body'>
                      <p>" . $reaction['note'] . "</p>
                    </div>
                    <h6>
                      <i class='ti-time'></i> " . date("d.m.Y H:i", strtotime($reaction['created'])) . ", ".getUserAll($reaction['createdBy'])[0]['name']."
                    </h6>
                  </div>
                </li>
                ";
            }
            $reactionsHtml .= "</ul>";

            $html .= "
        <div class='col col-12 col-md-" . $sizeMd . " col-lg-" . $sizeLg . "'>
            <div class='card card-chart'>
                <div class='card-header card-header-text card-header-" . $request['statusClass'] . "'>
                    <div class='card-text'>
                        <h4 class='card-title' id='requestName" . $request['idRequest'] . "'>" . $request['requestName'] . " <small>(ID ".$request['idRequest'].")</small></h4>
                    </div>
                </div>
                <div class='card-body'>
                    <h5 class='card-title' id='requestDescription" . $request['idRequest'] . "'>" . $request['note'] . "</h5>
                    <span id='requestReactions" . $request['idRequest'] . "'>$reactionsHtml</span>
                </div>
                <div class='card-footer'>
                    <div class='w-100'>
                        $projectRow
                        <div class='row'>
                            <div class='col col-6'>
                                <p class='card-category'>
                                    $deadline
                                    <span data-toggle='tooltip' data-placement='top' title='Stav žádosti'><i class='fa fa-circle' style='color: " . $request['statusColor'] . "'></i> " . $request['requestStatus'] . "</span><br>
                                    <span data-toggle='tooltip' data-placement='top' title='Autor žádosti'><i class='fa fa-user'></i> " . getUserAll($request['createdBy'])[0]['name'] . "</span><br>
                                </p>
                            </div>
                            <div class='col col-6'>";
            if (in_array($_SESSION['role'], ['editor','adminEditor','admin']) && $showControlButtons) $html .= "
                                <button class='btn btn-link btn-danger btn-just-icon remove-request float-right' request-id='" . $request['idRequest'] . "' project-id='" . ($idProject ? $idProject : $username) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Smazat žádost'>close</i></button>
                                <button class='btn btn-link btn-primary btn-just-icon edit-request float-right' request-id='" . $request['idRequest'] . "' request-type='" . $request['idRequestType'] . "' project-id='" . ($idProject ? $idProject : $username) . "' status-id='" . $request['idTaskStatus'] . "' deadline='" . ($request['deadline'] ? date("d.m.Y", strtotime($request['deadline'])) : $request['deadline']) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Zobrazit žádost'>remove_red_eye</i></button>
                                <button class='btn btn-link btn-primary btn-just-icon add-request-reaction float-right' $disabledReaction request-rank='".$request['rank']."' request-id='" . $request['idRequest'] . "' project-id='" . ($idProject ? $idProject : $username) . "'><i class='material-icons' data-toggle='tooltip'  data-placement='top' title='Přidat reakci'>add_comment</i></button>";
           //asi neni treba ne zobrazi grafika ? if ($showControlButtons) $html .= "<button class='btn btn-link btn-primary btn-just-icon request-history float-right' request-id='" . $task['idRequest'] . "' project-id='" . ($idProject ? $idProject : $username) . "' status-id='" . $request['idTaskStatus'] . "' deadline='" . ($request['deadline'] ? date("d.m.Y", strtotime($request['deadline'])) : $request['deadline']) . "'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Historie stavů žádosti'>youtube_searched_for</i></button>";
            if ($showControlButtons && !is_null($request['idCroseus'])) $html .= "<button class='btn btn-link btn-primary btn-just-icon croseus_status float-right' extIdent='$request[externalIdent]'><i class='material-icons' data-toggle='tooltip' data-placement='top' title='Zjistit stav v CROSEUS'>question_mark</i></button>";

            $html .= "                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
        }
        return $html;
    } else {
        return  "<div class='col col-12'>Žádné žádanky k zobrazení.</div>";
    }

}


function getCompanyName($idCompany)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT name FROM rangeCompanies WHERE idCompany = :idCompany");
    $stmt->bindValue(':idCompany', $idCompany, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetch(PDO::FETCH_ASSOC);
    return $dataArr['name'];
}

function getCompanyAll($idCompany)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeCompanies WHERE idCompany = :idCompany");
    $stmt->bindValue(':idCompany', $idCompany, PDO::PARAM_INT);
    $stmt->execute();
    $dataArr = $stmt->fetch(PDO::FETCH_ASSOC);
    return $dataArr;
}

function getContactsTable()
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeContacts WHERE active = 1 ORDER by name DESC");
    $stmt->execute();
    $hashToken = generateHash(date('H'));
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($dataArr)) {
        $tableContactsBody = '';
        foreach ($dataArr as $key => $row) {
            $tableContactsBody .= "<tr id='idContact$row[idContact]'>
                    <td>$row[name]</td>
                    <td>$row[email]</td>
                    <td>$row[phone]</td>
                    <td><button class='btn btn-sm btn-danger remove-contact float-right' token='$hashToken' id-contact='$row[idContact]'  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Odstranit tento kontakt\"><i class='fa fa-trash'></i></button><button class='btn btn-sm btn-primary float-right edit-contact' contact-name='$row[name]' contact-email='$row[email]' contact-phone='$row[phone]' contact-id='$row[idContact]' token='$hashToken' data-toggle=\"modal\" data-target=\"#contact\"><i class='fa fa-pencil-alt' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit údaje tohoto kontaktu\"></i></button></td>
                </tr>";
        }
        $html = " <div class=\"material-datatables\">
            <table id=\"datatableContacts\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>Jméno</th>
                    <th>E-mail</th>
                    <th>Telefon</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Jméno</th>
                    <th>E-mail</th>
                    <th>Telefon</th>
                    <th>Akce</th>
                </tr>
                </tfoot>
                <tbody>
               $tableContactsBody
                </tbody>
            </table>
        </div>
        ";
    } else {
        $html = "<h4>V DB není žádný kontakt.</h4>";
    }
    echo $html;
}

function getZadankyTable()
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeRequestTypes");
    $stmt->execute();
    $hashToken = generateHash(date('H'));
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($dataArr)) {
        $tableZadankyBody = '';
        foreach ($dataArr as $key => $row) {
            $tableZadankyBody .= "<tr id='idContact$row[idRequestType]'>
                    <td>$row[idRequestType]</td>
                    <td>$row[name]</td>
                    <td>$row[formCode]</td>
                    <td><button class='btn btn-sm btn-danger remove-zadanky float-right' token='$hashToken' id-zadanky='$row[idRequestType]'  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Odstranit tento typ žádanky\"><i class='fa fa-trash'></i></button><a class='btn btn-sm btn-primary float-right edit-zadanky' href='nastaveni.php?sprava=zadanky&zadanka=$row[idRequestType]'><i class='fa fa-pencil-alt' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit údaje tohoto typu žádanky\"></i></a></td>
                </tr>";
        }
        $html = "
         <div class=\"material-datatables\">
            <table id=\"datatableZadanky\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>ID žádanky</th>
                    <th>Název</th>
                    <th>Kód</th>
                    <th>Stav</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID žádanky</th>
                    <th>Název</th>
                    <th>Kód</th>
                    <th>Stav</th>
                </tr>
                </tfoot>
                <tbody>
               $tableZadankyBody
                </tbody>
            </table>
        </div>
        ";
    } else {
        $html = "<h4>V DB není žádný typ žádanky.</h4>";
    }
    echo $html;
}

function getFileTypesTable()
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT * FROM rangeDocumentTypes");
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tableFilesBody = '';
    $hashToken = generateHash(date('H'));
    foreach ($dataArr as $key => $row) {
        $tableFilesBody .= "<tr id='fileType$row[idDocType]' id-file-type='$row[idDocType]'>
                    <td class='ext'>.$row[extension]</td>
                    <td class='desc'>$row[description]</td>
                    <td class='mime'>$row[name]</td>
                    <td><button class='btn btn-sm btn-primary edit-file-type' href='nastaveni-firma.php?firma=$row[idDocType]' token='$hashToken' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit tento typ souborů\"><i class='fa fa-pencil-alt'></i></button><button class='btn btn-sm btn-danger remove-file-type' token='$hashToken' id-file-type='$row[idDocType]'  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Zakázat nahrávání tohoto typu souborů\"><i class='fa fa-trash'></i></button></td>
                </tr>";
    }
    $html = " <div class=\"material-datatables\">
            <table id=\"datatableFiles\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>Přípona</th>
                    <th>Popis</th>
                    <th>Název MIME</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Přípona</th>
                    <th>Popis</th>
                    <th>Název MIME</th>
                    <th>Akce</th>
                </tr>
                </tfoot>
                <tbody>
               $tableFilesBody
                </tbody>
            </table>
        </div>
        ";
    echo $html;
}

function findFileType($name, $extension)
{
    $id = false;
    $name = htmlspecialchars($name);
    $name = strtolower(preg_replace('/\s+/', '', $name));
    $extension = strtolower(preg_replace('/\s+/', '', $extension));
    $extension = escapeDiacritics($extension);
    $searchString = $name . $extension;
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query('SELECT idDocType, CONCAT(name, extension) as identString FROM rangeDocumentTypes');
    $fileTypeArr = $stmt->fetchAll();
    foreach ($fileTypeArr as $fileType) {
        $typeName = escapeDiacritics(strtolower(preg_replace('/\s+/', '', $fileType['identString'])));
        if ($searchString == $typeName) {
            $id = $fileType['idDocType'];
        }
    }
    return $id;
}

function editDocType($idDocType, $name, $description, $extension)
{
    if (is_numeric($idDocType)) {
        $name = htmlspecialchars($name);
        $description = htmlspecialchars($description);
        $extension = htmlspecialchars($extension);
        $idDocType = (int)$idDocType;
        require_once SYSTEMINCLUDES . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE `rangeDocumentTypes` SET `name`=:name, description=:description, extension=:extension WHERE idDocType = :idDocType");
        $stmt->bindValue(':idDocType', $idDocType, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function createDocType($name, $description, $extension)
{

    $name = htmlspecialchars($name);
    $description = htmlspecialchars($description);
    $lastId = findFileType($name, $extension);
    $extension = htmlspecialchars($extension);
    if ($lastId == false) {
        require_once SYSTEMINCLUDES . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
        try {
            $stmt = $dbh->getDbLink()->prepare('INSERT INTO `rangeDocumentTypes` (`name`,description,extension) VALUES (:name,:description,:extension)');
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
            $stmt->execute();
            $lastId = $dbh->getDbLink()->lastInsertId();
            // aktualne neloguje, funkce neumi null a ani nedava v db smysl - nema idLocalProject
            insertActionLog(null, 20, $dbh);
            $stmt = $dbh->getDbLink()->commit();
        } catch (Exception $e) {
            $stmt = $dbh->getDbLink()->rollBack();
            $lastId = 'Chyba transakce, vracím změny zpět. Chyba: ' . $e;
            writeError2Log(__FUNCTION__, 'functions - createDocType', $e);
        }
    }
    return $lastId;
}

function getCompaniesTable()
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT * FROM rangeCompanies ORDER by name DESC ");
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tableCompaniesBody = '';
    $hashToken = generateHash(date('H'));
    foreach ($dataArr as $key => $row) {
        $tableCompaniesBody .= "<tr id='company$row[idCompany]'>
                    <td>$row[name]</td>
                    <td>$row[address]</td>
                    <td>$row[ic]</td>
                    <td>$row[dic]</td>
                    <td>$row[www]</td>
                    <td><a class='btn btn-sm btn-primary' href='nastaveni-firma.php?firma=$row[idCompany]' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit údaje této firmy\"><i class='fa fa-pencil-alt'></i></a><button class='btn btn-sm btn-danger remove-company' token='$hashToken' id-company='$row[idCompany]'  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Odstranit tuto firmu\"><i class='fa fa-trash'></i></button></td>
                </tr>";
    }
    $html = " <div class=\"material-datatables\">
            <table id=\"datatableCompanies\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>Název</th>
                    <th>Adresa</th>
                    <th>IČ</th>
                    <th>DIČ</th>
                    <th>Web</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Název</th>
                    <th>Adresa</th>
                    <th>IČ</th>
                    <th>DIČ</th>
                    <th>Web</th>
                    <th>Akce</th>
                </tr>
                </tfoot>
                <tbody>
               $tableCompaniesBody
                </tbody>
            </table>
        </div>
        ";
    echo $html;
}

function getPrioritiesWeights()
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT rangeProjectTypes.name as typProjektu, rangeProjectSubtypes.name as subtypProjektu, rangePriorityScaleConfig.configJson as nastaveni FROM `type2subtype` JOIN rangeProjectTypes USING(idProjectType) JOIN rangeProjectSubtypes USING(idProjectSubtype) JOIN rangePriorityScaleConfig USING(idPriorityConfig) WHERE 1");
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tableBody = '';
    $hashToken = generateHash(date('H'));
    foreach ($dataArr as $radek) {
        $zahlaviTable = "";
        $nastaveniPart = "";
        $nastaveniArr = json_decode($radek['nastaveni'], true);
        $zahlavi = array_keys($nastaveniArr);
        foreach ($zahlavi as $sloupec) {
            $zahlaviTable .= "<th>$sloupec</th>";
        }
        foreach ($nastaveniArr as $item) {
            $nastaveniPart .= "<td>$item</td>";
        }
        $tableBody .= "<tr><td>$radek[typProjektu]</td>
                    <td>$radek[subtypProjektu]</td>
                    $nastaveniPart</tr>";
    }
    $tableCompaniesBody = '';
    foreach ($dataArr as $key => $row) {
        $tableCompaniesBody .= "<tr>
                </tr>";
    }
    $html = " <div class=\"material-datatables\">
            <table id=\"datatableCompanies\" class=\"table table-responsive table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>Typ stavby</th>
                    <th>Subtyp stavby</th>
                    $zahlaviTable
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Typ stavby</th>
                    <th>Subtyp stavby</th>
                    $zahlaviTable
                </tr>
                </tfoot>
                <tbody>
               $tableBody
                </tbody>
            </table>
        </div>
        ";
    echo $html;
}

function getUserStatus($user)
{
    switch ($user) {
        case 0:
            return "aktivní";
            break;
        case 1:
            return "zablokován";
            break;
    }
}

function blockAllowUserButton($username, $accessDenied)
{
    $hashToken = generateHash(date('H'));
    switch ($accessDenied) {
        case 0:
            $buttonText = "Zablokovat uživatele";
            $buttonIcon = "fa fa-user-times";
            $buttonStyle = "btn-danger";
            break;
        case 1:
            $buttonText = "Odblokovat uživatele";
            $buttonIcon = "fa fa-user-check";
            $buttonStyle = "btn-success";
            break;
    }
    $button = "<button class='btn btn-sm $buttonStyle allow-user' token='$hashToken' username='$username' access-denied='$accessDenied' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"$buttonText\"><i class='$buttonIcon'></i></button>";
    return $button;
}

function getUsersOverviewTable($users)
{
    $hashToken = generateHash(date('H'));
    $html = "<table id='tableUsersOverview' class='table table-striped table-no-bordered table-hover dtr-inline'>
                <thead class=>
                    <tr>
                        <th>
                            Jméno
                        </th>
                        <th>
                            Login
                        </th>
                        <th>
                            E-mail
                        </th>
                         <th>
                            Organizační jednotka
                        </th>
                        <th>
                            Role
                        </th>
                        <th>
                            Stav
                        </th>
                        <th>
                            Poslední přihlášení
                        </th>
                        <th>
                            Poslední změna
                        </th>
                        <th>
                            Datum založení
                        </th>
                        <th>
                            Akce
                        </th>
                    </tr>
                </thead>";
    $html .= "<tbody>";
    foreach ($users as $key => $row) {
        $html .= "<tr>
                    <td>$row[name]</td>
                    <td>$row[username]</td>
                    <td>$row[email]</td>
                    <td>$row[ou]</td>
                    <td>$row[role]</td>
                    <td>" . getUserStatus($row["accessDenied"]) . "</td>
                    <td>$row[lastLogin]</td>
                    <td>$row[updated]</td>
                    <td>$row[created]</td>
                    <td>" . blockAllowUserButton($row["username"], $row["accessDenied"]) . "<button class='btn btn-sm btn-primary edit-user' token='$hashToken' user-name='$row[name]' username='$row[username]' user-email='$row[email]' ou='$row[idOu]' role='$row[idRoleType]' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit uživatele\"><i class='fa fa-pencil-alt'></i></button></td>
                </tr>";
    }
    $html .= "</tbody>
             </table>";
    return $html;
}

function getLoginLogsTable($logy)
{
    $html = "<table id='tableLoginLogs' class='table table-striped table-no-bordered table-hover dtr-inline'>
                <thead class=>
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            Login
                        </th>
                         <th>
                            Datum a čas
                        </th>
                        <th>
                            IP adresa
                        </th>
                        <th>
                            Výsledek pokusu o přihlášení
                        </th>
                    </tr>
                </thead>";
    $html .= "<tbody>";
    foreach ($logy as $key => $row) {
        $html .= "<tr>
                    <td>$row[idLogin]</td>
                    <td>$row[username]</td>
                    <td>$row[loginTime]</td>
                    <td>$row[ipAddress]</td>
                    <td>$row[result]</td>
                </tr>";
    }
    $html .= "</tbody>
             </table>";
    return $html;
}

function getCollaboratorsTable($username)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT collaborator.id, collaborator.collaborator, users.name as nameTeammate, begin, expiry FROM collaborator JOIN users ON collaborator.collaborator = users.username WHERE collaborator.username = :username AND active = 1");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $hashToken = generateHash(date('H'));
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($dataArr)) {
        $tableContactsBody = '';
        foreach ($dataArr as $key => $row) {
            if ((strtotime($row["begin"]) < time()) && (strtotime($row["expiry"]) > time())) {
                $status = "<i class=\"fa fa-circle text-success\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Aktivní spolupráce\"></i>";
            } else {
                $status = "<i class=\"fa fa-circle text-danger\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Neaktivní spolupráce\"></i>";
            }
            $tableContactsBody .= "<tr id='idCollaboration$row[id]'>
                    <td>$status</td>
                    <td>$row[nameTeammate]</td>
                    <td>$row[collaborator]</td>
                    <td>$row[begin]</td>
                    <td>$row[expiry]</td>
                    <td><button class='btn btn-sm btn-danger remove-collaboration float-right' token='$hashToken' id-collaboration='$row[id]'  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Odstranit tuto spolupráci\"><i class='fa fa-trash'></i></button><button class='btn btn-sm btn-primary float-right edit-collaboration' collaborator-username='$row[collaborator]' collaboration-begin='$row[begin]' collaboration-expiry='$row[expiry]' id-collaboration='$row[id]' token='$hashToken' data-toggle=\"modal\" data-target=\"#collaborator\"><i class='fa fa-pencil-alt' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit tuto spolupráci\"></i></button></td>
                </tr>";
        }
        $html = " <div class=\"material-datatables\">
            <table id=\"datatableCollabolators\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>Stav</th>
                    <th>Jméno</th>
                    <th>Username</th>
                    <th>Od</th>
                    <th>Do</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Stav</th>
                    <th>Jméno</th>
                    <th>Username</th>
                    <th>Od</th>
                    <th>Do</th>
                    <th>Akce</th>
                </tr>
                </tfoot>
                <tbody>
               $tableContactsBody
                </tbody>
            </table>
        </div>
        ";
    } else {
        $html = "<h4>Nemáte nastavené žádné spolupracovníky.</h4>";
    }
    echo $html;
}

function getCollaboratorsTableForMe($username)
{
    require_once SYSTEMINCLUDES . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT collaborator.id, collaborator.username, users.name as nameTeammate, begin, expiry FROM collaborator JOIN users ON collaborator.username = users.username WHERE collaborator.collaborator = :username AND active = 1");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $hashToken = generateHash(date('H'));
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($dataArr)) {
        $tableContactsBody = '';
        foreach ($dataArr as $key => $row) {
            if ((strtotime($row["begin"]) < time()) && (strtotime($row["expiry"]) > time())) {
                $status = "<i class=\"fa fa-circle text-success\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Aktivní spolupráce\"></i>";
            } else {
                $status = "<i class=\"fa fa-circle text-danger\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Neaktivní spolupráce\"></i>";
            }
            $tableContactsBody .= "<tr id='idCollaboration$row[id]'>
                    <td>$status</td>
                    <td>$row[nameTeammate]</td>
                    <td>$row[username]</td>
                    <td>$row[begin]</td>
                    <td>$row[expiry]</td>
                    <td><a href='vypis.php?editor=$row[username]&idPhase=1%2C2%2C3%2C4%2C5' class='btn btn-sm btn-outline btn-outline-primary float-right'>Zobrazit projekty uživatele $row[nameTeammate] <i class='fa fa-sign-in-alt' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Upravit tuto spolupráci\"></i></a></td>
                </tr>";
        }
        $html = " <div class=\"material-datatables\">
            <table id=\"datatableCollabolatorsForMe\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
                    <th>Stav</th>
                    <th>Jméno</th>
                    <th>Username</th>
                    <th>Od</th>
                    <th>Do</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Stav</th>
                    <th>Jméno</th>
                    <th>Username</th>
                    <th>Od</th>
                    <th>Do</th>
                    <th>Akce</th>
                </tr>
                </tfoot>
                <tbody>
               $tableContactsBody
                </tbody>
            </table>
        </div>
        ";
    } else {
        $html = "<h4>Nikdo si vás nenastavil jako svého spolupracovníka.</h4>";
    }
    echo $html;
}

/**
 * @param $idProject
 * @return bool|string
 */
function findProjectVersions($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $countVersions = false;
    if (is_numeric($idProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->query("SELECT count(idLocalProject) FROM projects WHERE idProject = $idProject");
        $countVersions = $stmt->fetchColumn();
    }

    return $countVersions;
}

/**
 * @return string
 */
function selectOu()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $ouArr = $dbh->select('ou', ' hidden = FALSE ORDER BY orderNum ASC');
    $html = "";
    foreach ($ouArr as $ou) {
        if ($_SESSION['ou'] == $ou['name']) {
            $html .= "<option value='$ou[idOu]' selected>$ou[name]</option>";

        } else {
            $html .= "<option value='$ou[idOu]' >$ou[name]</option>";
        }
    }
    return $html;
}

function managerReportSelector()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT * FROM `reportConfig` ");
    $reportConfigs = $stmt->fetchAll();
    $stmt2 = $dbh->getDbLink()->query("SELECT idReportConfig FROM `users` WHERE username = '" . $_SESSION['username'] . "'");
    $userReportConfig = $stmt2->fetch();
    $html = '
    <div class="form-group">
        <label for="idManagerReport">Manažerský report:</label>
        <select name="idManagerReport" id="idManagerReport" class="selectpicker" data-style="select-with-transition"
                                            data-live-search="true"
                                            title="Vyberte manažerský report" tabindex="-98">';
    foreach ($reportConfigs as $reportConfig) {
        if ($reportConfig['idReportConfig'] == $userReportConfig['idReportConfig']) {
            $html .= "<option value='$reportConfig[idReportConfig]' selected>$reportConfig[note]</option>";
        } else {
            $html .= "<option value='$reportConfig[idReportConfig]' >$reportConfig[note]</option>";
        }
    }
    $html .= '</select><button class="btn btn-sm btn-primary" style="float: right" id="testManagerReport"><i class="material-icons">play_arrow</i> Odeslat testovací email</button></div>';
    return $html;
}

/**
 * @param $username
 * @return array
 */
function getUserDetails($username)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $userDetails = $dbh->select('users', " username LIKE '$username' ");
    $stmt = $dbh->getDbLink()->query("
    SELECT  COUNT(author) as count FROM viewProjectsActive WHERE author = '$username' 
    UNION ALL SELECT COUNT(editor) as count FROM viewProjectsActive WHERE editor = '$username' 
    UNION ALL SELECT COUNT(username) as count FROM logins WHERE username = '$username' 
    UNION ALL SELECT COUNT(username) as count FROM logins WHERE username = '$username' AND result LIKE 'Prihlaseni probehlo, presmerovavam' ");
    $countArr = $stmt->fetchAll();
    $lastLoginDateTime = getLastLogin($username);

    $returnArr = array(
        $userDetails[0]['created'],
        $userDetails[0]['updated'],
        $countArr[0]['count'],
        $countArr[1]['count'],
        $countArr[2]['count'],
        $countArr[3]['count'],
        $lastLoginDateTime
    );
    return $returnArr;

}


function initMailer(bool $exceptions = false)
{
    require_once __DIR__ . "/../conf/conf.php";
    $mail = new PHPMailer\PHPMailer($exceptions);
    $confArr = getMailSettings();
    try {
        //Server settings
        // $mail->SMTPDebug = PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = $confArr['mailHost'];                     //Set the SMTP server to send through
        $mail->SMTPAuth = false;                                   //Enable SMTP authentication     //SMTP username
        $mail->SMTPSecure = PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port = $confArr['mailPort'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($confArr['mailSetFrom']['mail'], $confArr['mailSetFrom']['name']);
        return $mail;
    } catch (PHPMailer\Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendMail($subject, $bodyHtml, $altBody, $recipients, $replyTo, bool $isHtml)
{
    $mail = initMailer();
    try {
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
        } else {
            $mail->addAddress($recipients);
        }
        $mail->addReplyTo($replyTo, 'NO-REPLY');
        $mail->isHTML($isHtml);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        $mail->AltBody = $altBody;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

}

function getUserAll($username)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT `name`, email FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $returnArr = $stmt->fetchAll();
    return $returnArr;
}

/**
 * @param $username
 * @param $valuesArr
 * @return bool
 */
function updateUsersInfo($username, $valuesArr)
{

    $escapesValuesArr = htmlspecialcharsArr($valuesArr);
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("UPDATE `users` SET `name`=:nameUser,`email`=:email,`updated`= NOW() ,`idOu`=:idOu,`idRoleType`=:idRoleType WHERE `username` = :username ");
    $stmt->bindParam(':username', $escapesValuesArr['username'], PDO::PARAM_STR);
    $stmt->bindParam(':nameUser', $escapesValuesArr['name'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $escapesValuesArr['email'], PDO::PARAM_STR);
    $stmt->bindParam(':idOu', $escapesValuesArr['idOu'], PDO::PARAM_INT);
    $stmt->bindParam(':idRoleType', $escapesValuesArr['idRoleType'], PDO::PARAM_INT);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

}

/**
 * @param $username
 * @param $oldPass
 * @param $newPass
 * @return bool|int
 */
function updateUserPass($username, $oldPass, $newPass)
{
    $updatedRows = false;
    $username = htmlspecialchars($username);
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $userPass = $dbh->select('users', "username = '$username'");
    //print_r(json_encode($userPass));
    //die();
    if (password_verify($oldPass, $userPass[0]['password'])) {
        $newPass = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $dbh->getDbLink()->prepare("UPDATE users SET updated= NOW(), password=:pass WHERE username = :username ");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $newPass, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $updatedRows = $stmt->rowCount();
        }
    }
    return $updatedRows;
}

/**
 * @param $username
 * @return string
 */
function getLastLogin($username)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query("SELECT `loginTime` FROM `logins` WHERE `username` = '$username' ORDER BY idLogin DESC LIMIT 1 OFFSET 1  ");
    $lastLoginDateTime = $stmt->fetchColumn();
    return $lastLoginDateTime;
}

/**
 * @param $string
 * @return string
 */
function generateHash($string)
{
    require_once __DIR__ . "/../conf/conf.php";
    $salt = getSalt();
    $string2Hash = $salt . $string . $_SESSION['username'];
    $hash = hash('md4', $string2Hash);
    return $hash;
}

/**
 * @param $hash4Validate
 * @param $string
 * @return bool
 */
function validateHash($hash4Validate, $string)
{
    require_once __DIR__ . "/../conf/conf.php";
    $salt = getSalt();
    $string2Hash = $salt . $string . $_SESSION['username'];
    $hash = hash('md4', $string2Hash);
    if ($hash4Validate == $hash) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param $idLocalProject
 * @return array
 */
function getProject($idLocalProject)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idLocalProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectsActive WHERE idLocalProject =:idLocalProject');
        $stmt->bindParam(':idLocalProject', $idLocalProject, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}

function getProjectById($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectsActive WHERE idProject =:idProject LIMIT 1');
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}

function getOuNameById($idOu)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idOu)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT name FROM ou WHERE idOu =:idOu');
        $stmt->bindParam(':idOu', $idOu, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $result[0]['name'];
}

/**
 * @param $idLocalProject
 * @return array
 */
function getIntentionProject($idLocalProject)
{

    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idLocalProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectsActive WHERE idLocalProject =:idLocalProject');
        $stmt->bindParam(':idLocalProject', $idLocalProject, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}


/**
 * @param $idLocalProject
 * @return array
 */
function getInPreparationProject($idLocalProject)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idLocalProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectsActive WHERE idLocalProject =:idLocalProject');
        $stmt->bindParam(':idLocalProject', $idLocalProject, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}

function getDoneProject($idLocalProject)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idLocalProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectsActive WHERE idLocalProject =:idLocalProject');
        $stmt->bindParam(':idLocalProject', $idLocalProject, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}

function getNextPhaseId($idPhase, $isConcept = false, $technologicalProjectType = 'normal')
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $phasesArr = array();
    $idPhaseActual = $idPhase;
    if ($technologicalProjectType == 'normal' OR $technologicalProjectType == 'topic') {
        if (is_numeric($idPhaseActual)) {
            $stmt = $dbh->getDbLink()->query("SELECT `idPhase`, name, level FROM `rangePhases` WHERE hidden is FALSE AND level > (SELECT level FROM rangePhases WHERE idPhase = $idPhaseActual) ORDER BY level ASC");
            $phasesArr = $stmt->fetchAll();
        } /*elseif (is_numeric($idPhaseActual) && $isConcept) {
            $stmt = $dbh->getDbLink()->query("SELECT `idPhase`, name, level FROM `rangePhases` WHERE idPhase = $idPhaseActual");
            $phasesArr = $stmt->fetchAll();
        }*/
    }
    if ($technologicalProjectType == 'lite') {
        if (is_numeric($idPhaseActual)) {
            $stmt = $dbh->getDbLink()->query("SELECT `idPhase`, name, level FROM `rangePhases` WHERE phaseForLiteProject is TRUE AND hidden is FALSE AND level > (SELECT level FROM rangePhases WHERE phaseForLiteProject is TRUE AND idPhase = $idPhaseActual) ORDER BY level ASC");
            $phasesArr = $stmt->fetchAll();
        } /*elseif (is_numeric($idPhaseActual) && $isConcept) {
            $stmt = $dbh->getDbLink()->query("SELECT `idPhase`, name, level FROM `rangePhases` WHERE phaseForLiteProject is TRUE AND idPhase = $idPhaseActual");
            $phasesArr = $stmt->fetchAll();
        }*/
    }


    return $phasesArr;
}

/**
 * @param $idLocalProject
 * @return array
 */
function getReadyProject($idLocalProject)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idLocalProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectReadyPhase WHERE idLocalProject =:idLocalProject');
        $stmt->bindParam(':idLocalProject', $idLocalProject, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}

function getInProgressProject($idLocalProject)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idLocalProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewProjectInProgress WHERE idLocalProject =:idLocalProject');
        $stmt->bindParam(':idLocalProject', $idLocalProject, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
}

function getFilesList($idProject, $documentCategory = null)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idProject)) {
        $dbh = new DatabaseConnector();
        if (!isset($documentCategory)) {
            $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewDocumentsActualVersions WHERE idProject =:idProject');
        } elseif (is_numeric($documentCategory)) {
            $stmt = $dbh->getDbLink()->prepare('SELECT * FROM viewDocumentsActualVersions WHERE idProject =:idProject AND idDocumentCategory = :documentCategory ');
            $stmt->bindParam(':documentCategory', $documentCategory, PDO::PARAM_INT);
        }
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($documentCategory)) {
            foreach ($result as $key => $radek)
                $result[$key]['idDocumentCategory'] = 0;
        }
    }

    return $result;
}

function createRelation($idProjectNew, $idProjectOrigin, $IdTypeRelation)
{
    $lastId = false;
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idProjectNew) && is_numeric($idProjectOrigin) && is_numeric($IdTypeRelation)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO projectsRelations (idProject, idProjectRelation,idRelationType, created, username) VALUE (:idProjectNew, :idProjectOrigin, :idRelationType,NOW(),:username) ");
        $stmt->bindParam(':idProjectNew', $idProjectNew, PDO::PARAM_INT);
        $stmt->bindParam(':idProjectOrigin', $idProjectOrigin, PDO::PARAM_INT);
        $stmt->bindParam(':idRelationType', $IdTypeRelation, PDO::PARAM_INT);
        $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        $lastId = $dbh->getDbLink()->lastInsertId();
    }

    return $lastId;

}

function relationExist($idProjectNew)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idProjectNew)) {
        $stmt = $dbh->getDbLink()->query("SELECT idRelation FROM projectRelations WHERE idProject = $idProjectNew 
                                                    UNION
                                                    SELECT idRelation FROM projectRelations WHERE idProjectRelation = $idProjectNew 
 ");
        $id = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($id)) {
            return $id;
        } else {
            return false;
        }

    }
}

function disableRelation($idProjectNew, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    require_once __DIR__ . "/autoLoader.php";

    if (is_numeric($idProjectNew)) {
        $stmt = $dbh->getDbLink()->prepare("DELETE FROM `projectRelations` WHERE idProject = $idProjectNew AND idRelationType NOT IN (3,2);
                                                                  DELETE FROM `projectRelations`  WHERE idProjectRelation = $idProjectNew AND idRelationType NOT IN (3,2) ;");
        if ($stmt->execute()) {
            return true;
        }

    }
}

function createRelationArr($postArr)
{
    $lastId = false;
    if (is_array($postArr)) {
        require_once __DIR__ . "/autoLoader.php";
        $idProjectNew = $postArr['projectId'];
        disableRelation($idProjectNew);
        unset($postArr['projectId']);
        foreach ($postArr as $key => $relationType) {
            $IdTypeRelationName = $key;
            $IdTypeRelation = (int)filter_var($IdTypeRelationName, FILTER_SANITIZE_NUMBER_INT);
            foreach ($relationType as $idProjectOrigin) {
                if (is_numeric($idProjectNew) && is_numeric($idProjectOrigin) && is_numeric($IdTypeRelation)) {
                    insertRelation($IdTypeRelation, $idProjectNew, $idProjectOrigin);
                }
            }
        }
    }
    return $lastId;

}

function restoreFileVersion($idDocumentLocal)
{
    require_once __DIR__ . "/autoLoader.php";
    if (is_numeric($idDocumentLocal)) {
        $fileInfo = getFileInfoArr($idDocumentLocal);
        $fileInfoActual = getFileInfoArr(null, $fileInfo['idDocument']); // kvuli hodnote aktualni verze
        $idProject = $fileInfoActual['IdProject'];
        $oldNameFile = $fileInfo['name'];
        $extension = pathinfo($oldNameFile, PATHINFO_EXTENSION);
        $onlyFileName = pathinfo($oldNameFile, PATHINFO_FILENAME);
        $arr = explode("[ver.", $onlyFileName);
        $version = intval($fileInfoActual['version']) + 1;
        $newFileName = $arr[0] . '[ver.' . $version . "]." . $extension;

        $username = $_SESSION['username'];
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `projects2documents`(`idDocument`, `idDocumentCategory`, `name`, `size`, `path`, `description`, `idDocType`, `created`, `IdProject`, `deleted`, `deletedAuthor`, `documentAuthor`, restored, restoredFrom, version)  select `idDocument`, `idDocumentCategory`,  :newFileName , `size`, `path`, `description`, `idDocType`, NOW(), `IdProject`, FALSE, NULL, :username, TRUE, $idDocumentLocal, :version FROM projects2documents where idDocumentLocal = :idDocumentLocal");
        $stmt->bindValue(':idDocumentLocal', $idDocumentLocal, PDO::PARAM_INT);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':newFileName', $newFileName, PDO::PARAM_STR);
        $stmt->bindValue(':version', $version, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $oldNameFile = __DIR__ . $fileInfo['path'] . "/" . $oldNameFile;
            $newFileName = __DIR__ . $fileInfo['path'] . "/" . $newFileName;
            if (!copy($oldNameFile, $newFileName)) {
                $stmt = $dbh->getDbLink()->rollBack();
                return false;
            } else {
                $lastIdLocal = $dbh->getDbLink()->lastInsertId();
                insertActionLog(getLastProjectLocalFromProjectId($idProject), 8, $dbh);

                $stmt = $dbh->getDbLink()->commit();

                return true;
            }
        } else {
            $stmt = $dbh->getDbLink()->rollBack();
            return false;
        }

    }
}

function getFileInfoArr($idDocumentLocal = null, $idDocument = null, $fileName = null)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $fileInfoArr = null;
    if (isset($idDocumentLocal) && is_numeric($idDocumentLocal)) {
        $stmt = $dbh->getDbLink()->query("SELECT projects2documents.*,rangeDocumentTypes.name as documentTypeName FROM projects2documents JOIN rangeDocumentCategories USING(idDocumentCategory) JOIN rangeDocumentTypes USING(idDocType) WHERE idDocumentLocal = $idDocumentLocal  ");
        $fileInfoArr = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (isset($idDocument)) {
        $check = explode("-", $idDocument);
        if (is_numeric($check[0]) && is_numeric($check[1]) && count($check) == 2) {
            $stmt = $dbh->getDbLink()->query("SELECT * FROM viewDocumentsActualVersions WHERE idDocument = '$idDocument' ");
            $fileInfoArr = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } elseif (isset($fileName)) {
        $fileName = htmlspecialchars($fileName);
        $stmt = $dbh->getDbLink()->prepare("SELECT * FROM projects2documents WHERE name LIKE :name  ");
        $stmt->bindParam(':name', $fileName, PDO::PARAM_STR);
        $stmt->execute();
        $fileInfoArr = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return $fileInfoArr;

}

function getRelatedProject($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $result = false;
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT *,'potomek' as hiearchy FROM viewProjectsRelations WHERE idProject = $idProject ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $dbh->getDbLink()->prepare("SELECT relationName, projectName as originProjectName,idProject as idProjectRelation,'rodic' as hiearchy FROM viewProjectsRelations WHERE idProjectRelation = $idProject");
        $stmt->execute();
        $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = array_merge($result, $result2);
    }
    if (count($result) > 0) {
        return $result;

    } else {
        return false;
    }
}

function getRelationInfo($idRelation)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idRelation)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeRelationTypes WHERE  idRelationType= :idRelation");
        $stmt->bindParam(':idRelation', $idRelation, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
    }
    return $result;
}

function getParentProject($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT idProjectRelation as id,idRelationType,originProjectName as projectName FROM viewProjectsRelations WHERE  idProject= :idProject");
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $result;
}

function getChildProject($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("SELECT idProject as id,idRelationType,projectName  FROM viewProjectsRelations WHERE  idProjectRelation= :idProject");
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $result;
}


function getFilesCategoryNames()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT * FROM rangeDocumentCategories WHERE hidden IS NOT TRUE ORDER BY orderNum ASC');

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;

}

function deleteFile($idDocumentLocal)
{
    $countDeleted = 0;
    if (is_numeric($idDocumentLocal)) {
        $fileInfoArr = getFileInfoArr($idDocumentLocal);
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
        $username = $_SESSION['username'];
        try {
            if (($_SESSION['role'] == 'admin' or $_SESSION['role'] == 'adminEditor')) {
                $stmt = $dbh->getDbLink()->query("UPDATE `projects2documents` SET deletedAt= NOW(), `deleted`= TRUE,`deletedAuthor`= '$username' WHERE idDocumentLocal = $idDocumentLocal");
                $countDeleted = $stmt->rowCount();
            } elseif (($_SESSION['role'] == 'editor')) {
                $stmt = $dbh->getDbLink()->query("UPDATE `projects2documents` SET deletedAt= NOW(), `deleted`= TRUE,`deletedAuthor`= '$username' WHERE idDocumentLocal = $idDocumentLocal AND documentAuthor = '$username'");
                $countDeleted = $stmt->rowCount();
            }
        } catch (Exception $e) {
            writeError2Log(__FUNCTION__, $idDocumentLocal, $e);

            $stmt = $dbh->getDbLink()->rollBack();
            return false;
        }
    }

    if ($countDeleted > 0) {
        insertActionLog(getLastProjectLocalFromProjectId($fileInfoArr['idProject']), 13, $dbh);
        $stmt = $dbh->getDbLink()->commit();
        return true;
    } else {
        return false;
    }

}

/*
function rrmdir($path)
{
    $i = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile()) {
            unlink($f->getRealPath());
        } else if (!$f->isDot() && $f->isDir()) {
            rrmdir($f->getRealPath());
        }
    }
}

function flushPreviewFiles()
{
    rrmdir(TMP_FILES_PREVIEWS);
}*/

function previewFile($idDocumentLocal = null, $idDocument = null, $fileName = null)
{
    $return = FALSE;
    if (isset($_SESSION['role']) and ($_SESSION['role'] == 'editor' or $_SESSION['role'] == 'adminEditor' or $_SESSION['role'] == 'admin')) {
        //flushPreviewFiles();
        if (isset($idDocumentLocal) && is_numeric($idDocumentLocal)) {
            $fileInfoArr = getFileInfoArr($idDocumentLocal);
        } elseif (isset($idDocument)) {
            $check = explode("-", $idDocument);
            if (is_numeric($check[0]) && is_numeric($check[1]) && count($check) == 2) {
                $fileInfoArr = getFileInfoArr(null, $idDocument);
            }
        } elseif (isset($fileName)) {
            $fileInfoArr = getFileInfoArr(null, null, $fileName);
        }
        if (isset($fileInfoArr)) {
            $path = __DIR__ . $fileInfoArr['path'];
            $file = $path . "/" . $fileInfoArr['name'];

            $tmpPathBase = TMP_FILES_PREVIEWS . $fileInfoArr['idProject'];
            $time = time();
            $newFileName = md5(($fileInfoArr['name'] . $time)) . '.tmp';
            $newFileNameWithPath = $tmpPathBase . '/' . $newFileName;
            if (file_exists($file)) {
                if (!file_exists($tmpPathBase)) {
                    mkdir($tmpPathBase, 0755, true);
                }
                if (!copy($file, $newFileNameWithPath)) {
                    echo "failed to copy $file...\n";
                } else {
                    $return = TMP_FILES_PREVIEWS_JS . $fileInfoArr['idProject'] . "/" . $newFileName;
                }
            }
        }
    }
    return $return;
}

function downloadFile($idDocumentLocal = null, $idDocument = null, $fileName = null)
{
    if (isset($_SESSION['role']) and ($_SESSION['role'] == 'editor' or $_SESSION['role'] == 'adminEditor' or $_SESSION['role'] == 'admin')) {

        if (isset($idDocumentLocal) && is_numeric($idDocumentLocal)) {
            $fileInfoArr = getFileInfoArr($idDocumentLocal);
        } elseif (isset($idDocument)) {
            $check = explode("-", $idDocument);
            if (is_numeric($check[0]) && is_numeric($check[1]) && count($check) == 2) {
                $fileInfoArr = getFileInfoArr(null, $idDocument);
            }
        } elseif (isset($fileName)) {
            $fileInfoArr = getFileInfoArr(null, null, $fileName);
        }
        if (isset($fileInfoArr)) {
            $path = __DIR__ . $fileInfoArr['path'];
            $file = $path . "/" . $fileInfoArr['name'];
            if (file_exists($file)) {
                insertActionLog(getLastProjectLocalFromProjectId($fileInfoArr['idProject']), 11);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }
        }
    }
}

function getPreviewFile($idDocumentLocal = null, $idDocument = null, $fileName = null)
{

    if (isset($_SESSION['role']) and ($_SESSION['role'] == 'editor' or $_SESSION['role'] == 'adminEditor' or $_SESSION['role'] == 'admin')) {
        if (isset($idDocumentLocal) && is_numeric($idDocumentLocal)) {
            $fileInfoArr = getFileInfoArr($idDocumentLocal);
        } elseif (isset($idDocument)) {
            $check = explode("-", $idDocument);
            if (is_numeric($check[0]) && is_numeric($check[1]) && count($check) == 2) {
                $fileInfoArr = getFileInfoArr(null, $idDocument);
            }
        } elseif (isset($fileName)) {
            $fileInfoArr = getFileInfoArr(null, null, $fileName);
        }
        if (isset($fileInfoArr)) {
            $path = __DIR__ . $fileInfoArr['path'];
            $file = $path . "/" . $fileInfoArr['name'];
            if (file_exists($file)) {
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=juuu");
                readfile($file);
                exit;
            }
        }
    }
}

function getTagsForDocumentId($idDocument)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT tagName, tags2documents.idTag, tagColor FROM `tags2documents` JOIN rangeTags ON rangeTags.idTag=tags2documents.idTag WHERE idDocument = :idDocument ');
    $stmt->bindParam(':idDocument', $idDocument, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function setProjectToPublish(int $idProject, string $username, $publish)
{
    $lastIdLocal = false;
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if ($publish) {
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `projectsToPublish`(`idProject`, `publishAt`, `publishBy`) VALUES (:idProject,now(),:username)
                ON DUPLICATE KEY UPDATE deletedAt = null, deletedBy = null, publishAt = NOW()
                ");
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastIdLocal = true;
        }
    }
    if (!$publish) {
        $stmt = $dbh->getDbLink()->prepare("UPDATE `projectsToPublish` SET deletedAt = NOW(), deletedBy = :username Where idProject = :idProject");
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastIdLocal = true;
        }

    }
    return $lastIdLocal;
}

function getProjectPublishState(int $idProject)
{
    $result = false;
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT 1 FROM `projectsToPublish`WHERE idProject = :idProject AND deletedAt IS NULL ');
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;

}

function getTagsForDocumentIdHtml($idDocument)
{
    $tags = getTagsForDocumentId($idDocument);
    $html = "";
    foreach ($tags as $tag) {
        $html .= "<span class=\"badge badge-primary tag-in-table\" style='background-color: #" . $tag['tagColor'] . "' data-toggle=\"tooltip\" data-placement=\"top\" title='Vyhledat dokumenty v této kategorii, označených či obsahujících slovo \"" . $tag['tagName'] . "\"'>" . $tag['tagName'] . "</span>";
    }
    return $html;
}

function getFilesVersionTable($idFile)
{
    $check = explode("-", $idFile);
    if (is_numeric($check[0]) && is_numeric($check[1]) && count($check) == 2) {
        $tableFilesVersionBody = "";
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->query("SELECT * FROM projects2documents  WHERE idDocument = '$idFile' ORDER by version DESC ");
        $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hashToken = generateHash(date('H'));
        $tags = getTagsForDocumentIdHtml($idFile);
        foreach ($dataArr as $key => $row) {
            $tableFilesVersionBody .= "<tr>
                   
                     <td>$row[version]</td>
                    <td>$row[description]</td>
                  
                    <td>$row[created]</td>
                    <td>$row[documentAuthor]</td>
                                        <td>$row[name]</td>

                    <td>$tags</td>
                    <td class=\"text-right\">";
            if ($key != 0) {
                $tableFilesVersionBody .= "<button document-restore='$row[idDocumentLocal]' file-id='$idFile' class=\"btn btn-link btn-info btn-just-icon restore document-restore\"><i class=\"material-icons\"  data-toggle=\"tooltip\" data-placement=\"top\" title=\"Vrátit tuto verzi jako aktuální\">restore</i></button>";
            }
            $tableFilesVersionBody .= "<a href='download.php?idDocumentLocal=$row[idDocumentLocal]&token=$hashToken' class=\"btn btn-link btn-warning btn-just-icon download\" download><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Stáhnout soubor\">save_alt</i></a>
                        <!--<a href=\"#delete$row[idDocumentLocal]\" class=\"btn btn-link btn-danger btn-just-icon remove\"><i class=\"material-icons\">close</i></a>-->
                    </td>
                </tr>";
        }
        $html = " <div class=\"material-datatables\">
            <table id=\"datatablesFilesVersion$idFile\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                <thead>
                <tr>
            
                   
                     <th>Verze </th>
                    
                    <th>Popis</th>
                   
                    <th>Nahráno</th>
                    <th>Uživatelem</th>
                    <th>Název souboru</th>
                    <th>Štítky</th>
                    <th class=\"disabled-sorting text-right\">Operace</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                      <th>Verze </th>
                    <th>Popis</th>
                
                    <th>Nahráno</th>
                    <th>Uživatelem</th>
                                        <th>Název souboru</th>

                    <th>Štítky</th>
                    <th class=\"text-right\">Operace</th>
                </tr>
                </tfoot>
                <tbody>
               $tableFilesVersionBody
                </tbody>
            </table>
        </div>
        ";
        echo $html;
    }


}

function generateFilesPanel($idProject, $tabOrPanel)
{
    $categoryInfo = getFilesCategoryNames();

    $tableFilesBodyArrCat0 = getFilesList($idProject);
    $tableFilesBodyArrCat1 = getFilesList($idProject, $categoryInfo[1]['idDocumentCategory']);
    $tableFilesBodyArrCat2 = getFilesList($idProject, $categoryInfo[2]['idDocumentCategory']);
    $tableFilesBodyArrCat3 = getFilesList($idProject, $categoryInfo[3]['idDocumentCategory']);
    $tableFilesBodyArrCat4 = getFilesList($idProject, $categoryInfo[4]['idDocumentCategory']);
    $tableFilesBodyArrCat5 = getFilesList($idProject, $categoryInfo[5]['idDocumentCategory']);

    $wholeTableArr = array_merge($tableFilesBodyArrCat0, $tableFilesBodyArrCat1, $tableFilesBodyArrCat2,
        $tableFilesBodyArrCat3, $tableFilesBodyArrCat4, $tableFilesBodyArrCat5);
    //print_r($wholeTableArr);

    $tableFilesBodyHtmlCat0 = "";
    $tableFilesBodyHtmlCat1 = "";
    $tableFilesBodyHtmlCat2 = "";
    $tableFilesBodyHtmlCat3 = "";
    $tableFilesBodyHtmlCat4 = "";
    $tableFilesBodyHtmlCat5 = "";
    $hashToken = generateHash(date('H'));

    foreach ($wholeTableArr as $row) {
        $disabled = "disabled";
        $toolTipDelete = "Smazat soubor";
        if ($_SESSION['role'] == 'editor' && $row['documentAuthor'] != $_SESSION['username'] && !in_array($row['documentAuthor'],
                $_SESSION['teammates'])) {
            $disabled = 'disabled ';
            $toolTipDelete = "Tento soubor nemůžete smazat, protože jste ho nenahráli.";
        }
        $newVersionPart = ($_SESSION['role'] == 'editor' || $_SESSION['role'] == 'adminEditor') ?
            "<a href=\"#update$row[idDocumentLocal]\" document-id='$row[idDocument]' project-id='$idProject' document-description='$row[description]' class=\"btn btn-link btn-primary btn-just-icon note_add update-file-version\" data-toggle=\"modal\" data-target=\"#uploadFileModal\"><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Nahrát novou verzi\">note_add</i></a>" : " ";

        $deleteFilePart = ($_SESSION['role'] == 'editor' || $_SESSION['role'] == 'adminEditor') ?
"                        <button  document-id-local='$row[idDocumentLocal]' table-target='datatablesFiles" . $idProject . '_' . $row['idDocumentCategory'] . "' token='$hashToken' class=\"btn btn-link btn-danger btn-just-icon remove remove-file\" $disabled ><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title='$toolTipDelete'>close</i></button>" : "";


        $tags = getTagsForDocumentIdHtml($row['idDocument']);
        $catMinusOne = $row['idDocumentCategory']; // Category number 0-4 instead of 1-5
        ${"tableFilesBodyHtmlCat" . $catMinusOne} .= "<tr id='idDocumentLocal$row[idDocumentLocal]' project-id='$idProject'>
                    <td>$row[description]</td>
                    
                  
                    <td><i>" . strtoupper($row['documentTypeName']) . " </i></td>
                    <td>$row[created]</td>
                    <td>$row[documentAuthor]</td>
                      <td>$row[name]</td>
                    <td>$tags</td>
                    <td class=\"text-right\">
                        " . ($row['documentTypeName'] == 'pdf' || $row['documentTypeName'] == 'jpg' || $row['documentTypeName'] == 'png' ? "<button file='download.php?idDocumentLocal=$row[idDocumentLocal]&token=$hashToken' class=\"btn btn-link btn-success btn-just-icon preview\" file-type='$row[documentTypeName]'><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Prohlédnout soubor\">remove_red_eye</i></button>" : "") . "
                        <a href=\"#updateTags$row[idDocumentLocal]\" document-id='$row[idDocument]' project-id='$idProject' class=\"btn btn-link btn-dark btn-just-icon update-tags\" data-toggle=\"modal\" data-target=\"#tagsUpdateModal\"><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Vybrat štítky\">local_offer</i></a>
                        $newVersionPart
                        <a href=\"#versions$row[idDocument]\" document-id='$row[idDocument]' project-id='$idProject' class=\"btn btn-link btn-info btn-just-icon restore file-version-browser\" data-toggle=\"modal\" data-target=\"#fileVersionBrowsingModal\"><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Předchozí verze souboru\" >restore</i></a>
                        <a href='download.php?idDocumentLocal=$row[idDocumentLocal]&token=$hashToken' class=\"btn btn-link btn-warning btn-just-icon download\" download><i class=\"material-icons\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Stáhnout soubor\">save_alt</i></a>
                        $deleteFilePart
                    </td>
                </tr>
                ";
    }

    $cardNames = ["Vsechno", "Stavba", "Dodavatel", "Zapisky", "Dokumentace", "Dalsi"];
    $cardIcons = ["all_inbox", "home", "group", "description", "work", "credit_card"];

    if ($tabOrPanel == 'panel') {
        $html = "<ul class='nav nav-tabs d-flex justify-content-start' data-tabs='tabs'>";
        for ($panel = 0; $panel < 6; $panel++) {
            $html .= "
               <li class='nav-item'>
                  <a class='nav-link fileCategory' file-category-id='" . $panel . "' href='#files" . $cardNames[$panel] . "$idProject' data-toggle='tab'>
                      <i class='material-icons'>" . $cardIcons[$panel] . "</i>" . $categoryInfo[$panel]['name'] . "
                      <div class='ripple-container'>
                      </div>
                  </a>
               </li>
            ";
        }
        $html .= "</ul>";
    }
    if ($tabOrPanel == 'tab') {
        $html = "";
        for ($panel = 1; $panel < 7; $panel++) {
            $panelFromZero = $panel - 1;
            if ($panel > 1 && $_SESSION['role'] == 'editor' || $_SESSION['role'] == 'adminEditor' || $_SESSION['role'] == 'admin')
                $newFile = "<btn class='btn btn-primary float-right plusButton' id='fileToProject$idProject' data-toggle=\"modal\" data-target=\"#uploadFileModal\"><i class='fa fa-plus'></i>Nahrát nový soubor</btn>";
            else
                $newFile = "";
            $html .= "
            <div class='tab-pane' id='files" . $cardNames[$panelFromZero] . "$idProject' files-category='$panel'>
               <div class='row'>
                  <div class=\"card\">
                     <div class=\"card-header card-header-primary card-header-icon\">
                        <div class=\"card-icon\">
                           <i class=\"material-icons\">list</i>
                        </div>
                        <h4 class=\"card-title\">Seznam souborů$newFile</h4>
                     </div>
                     <div class=\"card-body\">
                        <div class=\"toolbar\">
                        <!--        Here you can write extra buttons/actions for the toolbar              -->
                        </div>
                        <div class=\"material-datatables\">
                        <table id=\"datatablesFiles$idProject" . '_' . $categoryInfo[$panelFromZero]['idDocumentCategory'] . "\" class=\"table table-striped table-no-bordered table-hover\" cellspacing=\"0\" width=\"100%\" style=\"width:100%\">
                           <thead>
                              <tr>
                                
                                 <th>Popis</th>
                                 <th>Typ</th>
                                 <th>Nahráno</th>
                                 <th>Uživatelem</th>
                                  <th>Název souboru</th>
                                 <th>Štítky</th>
                                 <th class=\"disabled-sorting text-right\">Operace</th>
                              </tr>
                           </thead>
                           <tfoot>
                              <tr>
                                
                                 <th>Popis</th>
                                 <th>Typ</th>
                                 <th>Nahráno</th>
                                 <th>Uživatelem</th>
                                 <th>Název souboru</th>

                                 <th>Štítky</th>
                                 <th class=\"disabled-sorting text-right\">Operace</th>
                              </tr>
                           </tfoot>
                           <tbody>
                              " . ${"tableFilesBodyHtmlCat" . $panelFromZero} . "
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
           </div>
        </div>
             ";
        }
    }
    echo $html;
}

function generateCardHeader($project, $detail, $idPhaseName)
{
    $idPhaseName = ($idPhaseName) ?: $project->baseInformation['phaseName'];

    $relations = $project->relatons;
    $disableEtapa = '';
    if (array_key_exists('3', $relations)) { //jesltize je projekt rodic
        $disableEtapa = 'disabled';
    }
    $disableEdit = 'disabled';
    $disablePriority = 'disabled';
    if ($_SESSION['role'] == 'editor' && $project->baseInformation['editor'] == $_SESSION['username'] && checkEditorInProjectHistory($_SESSION['username'], $project->getId()) or in_array($project->baseInformation['editor'], $_SESSION['teammates'])) {
        $disableEdit = '';
        $disablePriority = '';
    }

    if ($_SESSION['role'] == 'adminEditor') {
        $disableEdit = '';
        $disablePriority = '';
    }

    $relatedInfo = "";
    $phaseChangeButton = "";
    $disableEditPhaseOne = '';


    if ($project->baseInformation['idPhase'] == 1 && $_SESSION['role'] == 'editor') {
        $disableEditPhaseOne = 'disabled';
    }
    $priorityScore = 'Nestanoveno';
    $priorityCorrection = 'Nestanoveno';
    if ($project->baseInformation['priorityAtts'] != NULL) {
        $priorityScore = new Priority($project->getId(), json_decode($project->baseInformation['priorityAtts'], true));
        $priorityCorrection = round(($priorityScore->getCorrectionValue() * 1000), 1);
        $priorityScore = $priorityScore->getResult();
    }
    $area = '';
    $communicationName = '';
    $communicationStationing = '';

    foreach ($project->getArea() as $a) {
        $area .= "<li>" . $a['name'] . "</li>";
    }


    foreach ($project->getCommunication() as $c) {
        $communicationName .= "<li>" . $c['name'] . "</li>";
        if (($c['stationingFrom'] != null) && ($c['stationingTo'] != null)) {
            $communicationStationing .= "<li> - km " . $c['stationingFrom'] . " - " . $c['stationingTo'] . "</li>";
        } elseif ($c['comment'] != null) {
            $communicationStationing .= "<li> - " . $c['comment'] . "</li>";
        }

    }

    $allowedRoles = ['editor', 'adminEditor'];
    if (!$project->baseInformation['existNonTerminalRequest'] && $project->baseInformation['idPhase'] != 1 && !array_key_exists('3', $relations) && in_array($_SESSION['role'], $allowedRoles)) {
        $phaseChangeButton = "<a data-toggle='tooltip' data-placement='top' title='Změna fáze' href='phaseChange.php?idLocalProject=" . $project->baseInformation['idLocalProject'] . "&idProject=" . $project->getId() . "' class='p-2 $disableEtapa'>
                                <i class='material-icons' >skip_next</i>
                            </a>";
    }

    if ($project->baseInformation['technologicalProjectType'] == 'lite') {
        $tempalte = array(
            "assignments" => array(
                "icon" => "assignment",
                "label" => "úkoly",
                "class" => ""
            ),
            "requests" => array(
                "icon" => "approval",
                "label" => "žádanky",
                "class" => ""
            ),
            "subject" => array(
                "icon" => "subject",
                "label" => "předmět",
                "class" => ""
            ),
            "objects" => array(
                "icon" => "category",
                "label" => "Sdružené objekty",
                "class" => ""
            ),
            "prePriceConstruction" => array(
                "icon" => "attach_money",
                "label" => "cena stavby",
                "class" => ""
            ),
            "contractPriceConstruction" => array(
                "icon" => "attach_money",
                "label" => "cena stavby",
                "class" => ""
            ),
            "priceConstruction" => array(
                "icon" => "attach_money",
                "label" => "cena stavby",
                "class" => ""
            ),
            "generalContractor" => array(
                "icon" => "group",
                "label" => "zhotovitel stavby",
                "class" => ""
            ),
            "constructionTime" => array(
                "icon" => "access_time",
                "label" => "doba realizace",
                "class" => ""
            ),
            "deadlines" => array(
                "icon" => "date_range",
                "label" => "termíny",
                "class" => "deadlineSP"
            ),
            "warranties" => array(
                "icon" => "view_timeline",
                "label" => "Záruka díla",
                "class" => ""
            )
        );

    } else {
        $tempalte = array(
            "assignments" => array(
                "icon" => "assignment",
                "label" => "úkoly",
                "class" => ""
            ),
            "requests" => array(
                "icon" => "approval",
                "label" => "žádanky",
                "class" => ""
            ),
            "subject" => array(
                "icon" => "subject",
                "label" => "předmět",
                "class" => ""
            ),
            "objects" => array(
                "icon" => "category",
                "label" => "Sdružené objekty",
                "class" => ""
            ),
            "prePricePlaning" => array(
                "icon" => "attach_money",
                "label" => "cena pd, ič, ad",
                "class" => ""
            ),
            "prePriceConstruction" => array(
                "icon" => "attach_money",
                "label" => "cena stavby, tds, bozp",
                "class" => ""
            ),
            "contractPriceConstruction" => array(
                "icon" => "attach_money",
                "label" => "cena stavby, tds, bozp",
                "class" => ""
            ),
            "pricePlaning" => array(
                "icon" => "attach_money",
                "label" => "cena pd, ič, ad",
                "class" => ""
            ),
            "priceConstruction" => array(
                "icon" => "attach_money",
                "label" => "cena stavby, tds, bozp",
                "class" => ""
            ),
            "projectContractor" => array(
                "icon" => "group",
                "label" => "zhotovitel projektu",
                "class" => ""
            ),
            "constructionOversight" => array(
                "icon" => "group",
                "label" => "tds a bozp",
                "class" => ""
            ),
            "generalContractor" => array(
                "icon" => "group",
                "label" => "zhotovitel stavby",
                "class" => ""
            ),
            "constructionTime" => array(
                "icon" => "access_time",
                "label" => "doba realizace",
                "class" => ""
            ),
            "deadlines" => array(
                "icon" => "date_range",
                "label" => "termíny",
                "class" => "deadlineSP"
            ),
            "warranties" => array(
                "icon" => "view_timeline",
                "label" => "Záruka díla",
                "class" => ""
            )
        );
    }

    $navTabs = '';
    $systemsHtml = '';
    require_once(CLASSES . "Enums.php");
    $allowedPriorityAtts = [];
    foreach (Priorita_selecty::SELECT as $eachSelect) {
        $className = "Priorita_" . $eachSelect;
        if (class_exists($className, false)) {
            if (in_array($project->baseInformation['idPhase'], $className::DISABLE_IN_PHASES)) {
                $allowedPriorityAtts[$className::FORM_NAME] = FALSE;
            } else {
                $allowedPriorityAtts[$className::FORM_NAME] = true;

            }
        }
    }
    $allowedPriorityAtts = json_encode($allowedPriorityAtts);

    foreach ($project->getCardTemplate() as $tab) {
        if (array_key_exists($tab, $tempalte)) {
            $navTabs .= "<li class='nav-item " . $tempalte[$tab]['class'] . "' data-id=" . $project->getId() . ">
                            <a class='nav-link ' href='#" . $tab . $project->getId() . "' data-toggle='tab'>
                                <i class='material-icons'>" . $tempalte[$tab]['icon'] . "</i>" . $tempalte[$tab]['label'] . "
                            </a>
                         </li>";
        } elseif (in_array('externalSystems', $project->getCardTemplate())) {
            $ginisOrAthena = ($project->baseInformation['ginisOrAthena'] != null) ? "<span data-toggle='tooltip' data-placement='auto' title='' data-original-title='" . $project->baseInformation['noteGinisOrAthena'] . "'>" . strtoupper($project->baseInformation['ginisOrAthena']) . "</span>" : '';
            $dateEvidence = ($project->baseInformation['dateEvidence'] == 1) ? "<span data-toggle='tooltip' data-placement='auto' title='Doloženo: " . date("d. m. Y", strtotime($project->getDeadlineByType(13)['value'])) . "'> | D</span>" : "<span data-toggle='tooltip' data-placement='auto' title='Není potřeba dokládat'> | X</span>";
            $systemsHtml = "<div class='ml-auto'>
                                <div class='d-flex justify-content-end systemsBox'>
                                    <h4>
                                        $ginisOrAthena
                                        $dateEvidence
                                    </h4>       
                                </div>
                            </div>";
        }
    }
    $detailButton = !$detail ? "<a  data-toggle='tooltip' data-placement='top' title='Detail projektu' href='detail.php?idProject=" . $project->getId() . "' class='p-2'>
                            <i class='material-icons' >search</i>
                        </a>" : "";
    $publishButton = $_SESSION['role'] == 'view' ?  "" : ($project->baseInformation['published'] ?
        "<a data-toggle='tooltip' data-placement='top' title='Zruš publikování projektu' href='/submits/setProjectToPublish.php?idProject=" . $project->getId() . "&token=" . generateHash($project->getId()) . "' class='p-2 $disableEdit'>
                            <i id='publishButton_" . $project->getId() . "' class='publishProject material-icons' >public_off</i>
                        </a>" :
        "<a data-toggle='tooltip' data-placement='top' title='Publikovat projekt' href='/submits/setProjectToPublish.php?idProject=" . $project->getId() . "&token=" . generateHash($project->getId()) . "' class='p-2 $disableEdit'>
                            <i id='publishButton_" . $project->getId() . "' class='publishProject material-icons' >public</i>
                        </a>");
    $delAndRelationButtons = $detail ? " 
                          <a data-toggle='tooltip' data-placement='top' title='Relace' class='p-2'>
                            <i data-toggle='modal' data-target='#projectRelationModal' data-id='" . $project->getId() . "' class='material-icons'>compare_arrows</i>
                        </a>
                        <a data-toggle='tooltip' data-placement='top' title='Smazání projektu' href='/submits/deleteProjectSubmit.php?deleteProject=true&idProject=" . $project->getId() . "&token=" . generateHash($project->getId()) . "&delete=true' class='p-2 $disableEdit'>
                            <i class='deactivateProject material-icons' >delete</i>
                        </a>" : "";
    $suspensionCss = ($project->baseInformation['idPhase'] != 2) ? "d-none" : "";
    $nextPhaseArr = getNextPhaseId($project->baseInformation['idPhase'], false, $project->baseInformation['technologicalProjectType']);
    $phasingIcon = ($_SESSION['role'] != 'view' && $project->baseInformation['phasing'] == 1 && !array_key_exists('2', $relations) && $project->baseInformation['technologicalProjectType'] != 'lite' ) ? "<a data-toggle='tooltip' data-placement='top' title='Etapizovat projekt' href='createPhaseProject.php?idProject=" . $project->getId() . "'  class='p-2 ' >
                            <i class='material-icons' >alt_route</i>
                        </a>" : "";
    $editIcon = $_SESSION['role'] == 'view' ?  "" : "<a data-toggle='tooltip' data-placement='top' title='Editace projektu' href='editProject.php?idProjectForEdit=" . $project->getId() . "&" . $_SERVER['QUERY_STRING'] . "'  class='p-2 $disableEdit $disableEditPhaseOne' >
                            <i class='material-icons' >edit</i>
                        </a>";
    $conceptInfo = ($project->baseInformation['inConcept'] == 1) ? " (V konceptu) -> " . $nextPhaseArr[0]['name'] : "";
    $conceptIcon = ($project->baseInformation['inConcept'] == 1) ? "<span class='badge badge-secondary'>V konceptu</span>" : "";
    $passableIcon = ($project->baseInformation['passable'] == 1) ? "<span class='badge badge-warning'>Zprůjezdněno</span>" : "";

    $requestExistBadge = ($project->baseInformation['existNonTerminalRequest'] == 1) ? "<span class='badge badge-primary'>Nevyřízená žádanka</span>" : "";
    $valueProgessBar = (getNextPhaseId($project->baseInformation['idPhase'], true, $project->baseInformation['technologicalProjectType'])[0]['level']) * 20;
    $conceptProgressBar = ($project->baseInformation['inConcept'] == 1) ? "<div class='progress-bar progress-bar-striped bg-dark' role='progressbar' style='width: 20%' aria-valuenow='20' aria-valuemin=0' aria-valuemax='100'></div>" : "";
    $finSource = ($project->getFinSource() != null) ? " | " . $project->getFinSource() : '';

    $projectSubtypeName = (isset($project->baseInformation['projectSubtypeName'])) ? " - " . $project->baseInformation['projectSubtypeName'] : "";
    $editorHistory = editorHistoryForTooltipOnProjectCard($project->getId());
    $disablePriority = $project->baseInformation['idPhase'] < 3 ? 'disabled' : '';
    $priorityBlock = " <a class='align-self-center pr-2 $disablePriority'><i data-toggle='modal' data-allowedatts='" . urlencode($allowedPriorityAtts) . "'data-target='#projectPrioritymodal' data-idphase=" . $project->baseInformation['idPhase'] . " data-atts='" . urlencode($project->baseInformation['priorityAtts']) . "'data-id='" . $project->getId() . "' class='align-self-center pr-2 material-icons'>low_priority</i></a>
                        <ul class='list-unstyled align-self-center mr-2 pr-2 mb-0 border-right border-white fsH4'>
                            <li>Priorita: <span id='priorityScoreCard" . $project->getId() . "'>$priorityScore</span></li>
                            <li>S korekcí: <span id='priorityCorrectionCard" . $project->getId() . "'> " . $priorityCorrection . "</span></li>
                        </ul>";
    $html = "<div class='nav-tabs-navigation'>
<span class='progress' style='height: 2px;'>
  <div class='progress-bar bg-success' role='progressbar' style='width: $valueProgessBar%' aria-valuenow='$valueProgessBar' aria-valuemin=0' aria-valuemax='100'></div>
  $conceptProgressBar
</span>
                <div class='d-flex justify-content-start'>
                    <h4 class=''>
                       <span class='font-weight-bold'> " . $project->baseInformation['name'] . " </span>
                    </h4>
                    <span class='ml-auto control'>
                    $requestExistBadge
                    $passableIcon
                    $conceptIcon
                   $editIcon
                        <a data-toggle='tooltip' title='Přerušení stavby' class='p-2 $suspensionCss'>
                            <i data-toggle='modal' data-target='#projectSuspensionsModal' data-id='" . $project->getId() . "' class='material-icons $disableEtapa'  >pause_circle_filled</i>
                        </a>
                      
                        <a data-toggle='tooltip' data-placement='top' title='Soubory' onclick='showFilesPanel(" . $project->getId() . ")' class='p-2'>
                            <i class='material-icons' >folder</i>
                        </a>
                        $phasingIcon
                        $phaseChangeButton
                        $detailButton
                        $publishButton
                        $delAndRelationButtons
                    </span>
                </div>
                <div class='d-flex justify-content-between'>
                    <h4>
                    ID: " . $project->getId() . " |  " . $project->baseInformation['projectTypeName'] . $projectSubtypeName . " | " . $idPhaseName . $conceptInfo . $finSource . "
                    </h4>
                    <h4 data-toggle='tooltip' data-placement='top' data-html='true' title='$editorHistory'>" . $project->baseInformation['editorName'] . "</h4>
                </div>
                <div class='d-flex justify-content-between'>
                    <div class='d-flex align-self-center'>
                       $priorityBlock
                        <i class='align-self-center pr-2 material-icons'>map</i>
                        <ul class='list-unstyled align-self-center mr-2 pr-2 mb-0 border-right border-white fsH4'>
                        " . $area . "
                        </ul>
                        <i class='align-self-center pr-2 material-icons'>theaters</i>
                        <ul class='list-unstyled align-self-center mr-1 mb-0 fsH4'>
                        " . $communicationName . "
                        </ul>
                        <ul class='list-unstyled align-self-center fsH4 mb-0'>
                        " . $communicationStationing . "
                        </ul>  
                    </div>
                    
                </div>
                <div class='d-flex nav-tabs-wrapper mt-2 '>
                    <ul class='nav nav-tabs col' data-tabs='tabs' id='tabNavs" . $project->getId() . "'>
                        $navTabs
                    </ul>
                    $systemsHtml
                </div>
                
            </div>";

    return $html;
}

function generateRelationBox($relatedProjectsArr, $style)
{
    $html = "";
    if (is_array($relatedProjectsArr)) {
        foreach ($relatedProjectsArr as $relation) {

            $html .= "<div class='col'>
                                    <div class='box box-$style'>
     $relation[relationName] -> <a href='detail.php?idProject=" . $relation['idProjectRelation'] . "'>$relation[originProjectName] (ID: $relation[idProjectRelation])</a>
                                    </div>
                                </div>";
        }
    }

    return $html;

}

/**
 * @param $idLocalProject
 * @return string
 */
function generateProjectCard($idProject, $idPhase, $idPhaseName, $style, $detail = false, $etapaPotomek = false, $nadrazenyProjekt = null)
{
    require_once __DIR__ . "/autoLoader.php";

    $project = new Project($idProject);
    // $idPhase = $project->baseInformation['idPhase'];
    if($project->baseInformation['technologicalProjectType']== 'lite'){
        $template = array(
            "assignments" => array(
                "view" => false
            ),
            "requests" => array(
                "view" => false
            ),
            "subject" => array(
                "view" => false
            ),
            "prePriceConstruction" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 6,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "contractPriceConstruction" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 6,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "priceConstruction" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Skutečné ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 5,
                                'dph' => true
                            )
                        )
                    ),
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 6,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "generalContractor" => array(
                "label" => "zhotovitel stavby",
                "view" => false,
                "idCompanyType" => 2,
                "contacts" => array(
                    array(
                        'idContactType' => 5
                    ),
                    array(
                        'idContactType' => 6
                    ),
                    array(
                        'idContactType' => 12
                    )
                )
            ),
            "constructionTime" => array(
                "label" => "doba realizace",
                "view" => false
            ),
            "objects" => array(
                "label" => "sdružené objekty",
                "view" => false
            ),
            "deadlines" => array(
                "label" => "termíny",
                "view" => false
            ),
            "warranties" => array(
                "label" => "záruka díla",
                "view" => false
            )
        );
        $pricePanels = array("prePriceConstruction", "contractPriceConstruction","priceConstruction");
        $contactPanels = array( "constructionOversight", "generalContractor");

    }
    else{
        $template = array(
            "assignments" => array(
                "view" => false
            ),
            "requests" => array(
                "view" => false
            ),
            "subject" => array(
                "view" => false
            ),
            "prePricePlaning" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 3,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 4,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 13,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "prePriceConstruction" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 6,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 8,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "contractPriceConstruction" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Ceny dle PD",
                        "prices" => array(
                            array(
                                'idPriceType' => 11,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 12,
                                'dph' => true
                            )
                        )
                    ),
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 6,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 8,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "pricePlaning" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Skutečné ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 2,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 1,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 14,
                                'dph' => true
                            )
                        )
                    ),
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 3,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 4,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 13,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "priceConstruction" => array(
                "view" => false,
                "types" => array(
                    array(
                        "label" => "Skutečné ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 5,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 7,
                                'dph' => true
                            )
                        )
                    ),
                    array(
                        "label" => "Ceny dle PD",
                        "prices" => array(
                            array(
                                'idPriceType' => 11,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 12,
                                'dph' => true
                            )
                        )
                    ),
                    array(
                        "label" => "Předpokládané ceny",
                        "prices" => array(
                            array(
                                'idPriceType' => 6,
                                'dph' => true
                            ),
                            array(
                                'idPriceType' => 8,
                                'dph' => true
                            )
                        )
                    )
                )
            ),
            "projectContractor" => array(
                "label" => "zhotovitel projektu",
                "view" => false,
                "idCompanyType" => 1,
                "contacts" => array(
                    array(
                        'idContactType' => 1
                    ),
                    array(
                        'idContactType' => 2
                    ),
                    array(
                        'idContactType' => 3
                    ),
                    array(
                        'idContactType' => 4
                    ),
                )
            ),
            "constructionOversight" => array(
                "label" => "tds a bozp",
                "view" => false,
                "idCompanyType" => 3,
                "contacts" => array(
                    array(
                        'idContactType' => 11
                    ),
                    array(
                        'idContactType' => 9
                    ),
                    array(
                        'idContactType' => 10
                    )
                )
            ),
            "generalContractor" => array(
                "label" => "zhotovitel stavby",
                "view" => false,
                "idCompanyType" => 2,
                "contacts" => array(
                    array(
                        'idContactType' => 5
                    ),
                    array(
                        'idContactType' => 6
                    ),
                    array(
                        'idContactType' => 12
                    )
                )
            ),
            "constructionTime" => array(
                "label" => "doba realizace",
                "view" => false
            ),
            "deadlines" => array(
                "label" => "termíny",
                "view" => false
            ),
            "objects" => array(
                "label" => "sdružené objekty",
                "view" => false
            ),
            "warranties" => array(
                "label" => "záruka díla",
                "view" => false
            )
        );
        $pricePanels = array("prePricePlaning", "prePriceConstruction", "contractPriceConstruction", "pricePlaning", "priceConstruction");
        $contactPanels = array("projectContractor", "constructionOversight", "generalContractor");

    }


    $cards = $project->getCardTemplate($project->baseInformation['technologicalProjectType']);
    $warrantiesArr = $project->getWarrantiesDeadlines();
    $warrantyHtml = empty($warrantiesArr) ? "<div class='card-body'><b>Nebyla vyplněna záruka a datum předání zhotovené stavby.</b></div>" : "";
    $cardBody = "";
    foreach ($warrantiesArr as $warranty) {
        $warrantyPeriod = "";
        if ($warranty['idDeadlineType'] == 26) {
            $warrantyPeriod = "Záruka " . $project->baseInformation['constructionWarrantyPeriod'] . " měsíců";
        }
        if ($warranty['idDeadlineType'] == 25) {
            $warrantyPeriod = "Záruka " . $project->baseInformation['technologyWarrantyPeriod'] . " měsíců";
        }

        $warrantyHtml .= "<div class='col'>
                                    <div class='card'>
                                        <div class='card-header card-header-text card-header-$style'>
                                            <div class='card-text'>
                                                <h4 class='card-title'>$warranty[name]</h4>
                                            </div>
                                        </div>
                                        <div class='card-body'>" .
            date("Y-m-d", strtotime($warranty['value'])) . "<br>
                                        $warrantyPeriod
                                        </div>
                                    </div>
                                </div>";
    }

    $cardBody .= "<div class='tab-pane' role='tabpanel' id='warranties" . $project->getId() . "'>
                                <h3>Záruka díla</h3>
                                <div class='row'>
                                    $warrantyHtml
                                </div>
                          </div>";

    foreach ($cards as $card) {
        if (array_key_exists($card, $template)) {
            $template[$card]["view"] = true;
        }
        else{
            $template[$card]["view"] = false;
        }
    }


    if ($template["assignments"]['view'] == true) {
        $cardBody .= "<div class='tab-pane' role='tabpanel' id='assignments" . $project->getId() . "'>";
        if (in_array($_SESSION['role'], ['editor','adminEditor','admin'])) $cardBody .= "<button class='btn btn-primary float-right add-new-task' project-id='" . $project->getId() . "' data-toggle='modal' data-target='#projectTasksUpdateModal'><i class='fa fa-plus'></i> Přidat úkol</button>";
        $cardBody .= "<h3>Úkoly</h3>
                        <div class='row' id='tasks" . $project->getId() . "'>
                        " . listTasks($project->getId()) . "
                        </div>
                      </div>";
    }
    $requestsPart = listRequests($project->getId());

    if ($template["requests"]['view'] == true) {
        $cardBody .= "<div class='tab-pane' role='tabpanel' id='requests" . $project->getId() . "'>";
        if (in_array($_SESSION['role'], ['editor','adminEditor','admin'])) $cardBody .= "<button class='btn btn-primary float-right add-new-request' project-idPhase ='".$project->baseInformation['idPhase']."' project-id='" . $project->getId() . "' data-toggle='modal' data-target='#projectRequestUpdateModal'><i class='fa fa-plus'></i> Přidat žádost</button>";
        $cardBody .= "<h3>Žádanky</h3>
                        <div class='row' id='requestsRow" . $project->getId() . "'>
                        " . $requestsPart . "
                        </div>
                      </div>";
    }

    if ($template["subject"]['view'] == true) {
        $cardBody .= "<div class='tab-pane' role='tabpanel' id='subject" . $project->getId() . "'>
                        <div class='card'>
                            <div class='card-body'>
                                " . unicodeToUtf8(stripTags($project->baseInformation['subject'])) . "
                            </div>
                        </div>
                      </div>";
    }
    /*  if ($template["warranties"]['view'] == true) {
          $cardBody .= "<div class='tab-pane' role='tabpanel' id='warranties" . $project->getId() . "'>
                          <div class='card'>
                              <div class='card-body'>
                              Datum předání stavby: ".date("Y-m-d", strtotime($project->getWarrantiesDeadlines()[0]['value']))."</br>
                                  Doba záruky na stavební část: ". $project->baseInformation['constructionWarrantyPeriod'] . " měsíců</br>
                                   Doba záruky na technologickou část: ". $project->baseInformation['technologyWarrantyPeriod'] . " měsíců
                              </div>
                          </div>
                        </div>";
      }*/
    if ($template["deadlines"]['view'] == true) {
        $cardBody .= "<div class='tab-pane' role='tabpanel' id='deadlines" . $project->getId() . "'>
                            <div class='row'>
                                <div class='col'>
                                    <div class='fullCalendar' id='fullCalendar" . $project->getId() . "' data-id='" . $project->getId() . "'></div>
                                </div>
                            </div>
                       </div>";
    }

    if ($template["constructionTime"]['view'] == true) {

        if ($project->baseInformation['constructionTimeWeeksOrMonths'] == "m") {
            $constructionTimeWeeksOrMonthLabel = "měsíců";
            $construnctionDates = $project->getConstructionDates("m");
        }
        if ($project->baseInformation['constructionTimeWeeksOrMonths'] == "w") {
            $constructionTimeWeeksOrMonthLabel = "týdnů";
            $construnctionDates = $project->getConstructionDates("w");
        }
        if ($project->baseInformation['constructionTimeWeeksOrMonths'] == "d") {
            $constructionTimeWeeksOrMonthLabel = "dnů";
            $construnctionDates = $project->getConstructionDates("d");
        }
        $constritonHandoverDate = new DateTime($construnctionDates['constructionHandoverDate']);
        $constructionDeadline = new DateTime($construnctionDates['constructionDeadline']);

        $suspensions = $project->getSuspensions();
        $susArr = array();
        foreach ($suspensions as $supension) {
            array_push($susArr, Suspension::fromDb($supension['idSuspension']));
        }

        usort($susArr, function ($a, $b) {
            return $a->dateFrom > $b->dateFrom;
        });

        $fin = array();
        foreach ($susArr as $key => $supension) {
            array_push($fin, array($supension->dateFrom, $supension->dateTo));
        }

        $n = 0;
        $count = count($fin);
        for ($i = 1; $i < $count; ++$i) {
            if ($fin[$i][0] > $fin[$n][1]) {
                $n = $i;
            } else {
                if ($fin[$n][1] < $fin[$i][1]) {
                    $fin[$n][1] = $fin[$i][1];
                }
                unset($fin[$i]);
            }
        }
        $sumSuspensions = 0;
        foreach ($fin as $date) {
            $sumSuspensions += $date[0]->diff($date[1])->format('%a');
        }
        $modResult = (int)($sumSuspensions % 7) + 1;
        $suspensionAdded = null;
        if ($sumSuspensions > 0) {
            $suspensionAdded = $modResult > 0 ? " + " . (floor($sumSuspensions / 7) . " týdnů " . $modResult . " dní odstávky") : " + " . floor($sumSuspensions / 7) . " týdnů odstávky";
        }


        // $constructionDeadline->add(new DateInterval('P' . $project->baseInformation['constructionTime'] .  strtoupper($project->baseInformation['constructionTimeWeeksOrMonths']) ));
        $currentDate = $date = new DateTime('today');
        $fromBegin = $constritonHandoverDate->diff($currentDate);
        $toEnd = $currentDate->diff($constructionDeadline);
        //$fromBeginPercent = ((int)($fromBegin->format('%a')) / ($project->baseInformation['constructionTime'] * 7)) * 100;
        $delitel = ((int)($fromBegin->format('%a')) + (int)($toEnd->format('%a'))) != 0 ? (int)($fromBegin->format('%a')) + (int)($toEnd->format('%a')) : 1;
        $fromBeginPercent = ((int)($fromBegin->format('%a'))) / $delitel * 100;
        $toEndPercent = 100 - $fromBeginPercent;
        $susBarHtml = '';
        foreach ($susArr as $sus) {
            $susBarHtml .= $sus->createTimeline($constritonHandoverDate, $constructionDeadline);
        }

        /*
        print_r('<pre>');
        print_r($suspensions);
        print_r('</pre>');
        */


        if ($currentDate >= $constructionDeadline) {
            $barHTML = "
            <div class='progress'>
                <div class='progress-bar progress-bar-striped bg-success progress-bar-animated' role='progressbar' style='width: 100%' aria-valuemin='0' aria-valuemax='100'>Stavba byla dokončena</div>
            </div>";
        } else if ($currentDate <= $constritonHandoverDate) {
            $barHTML = "
            <div class='progress'>
                <div class='progress-bar progress-bar-striped bg-danger progress-bar-animated' role='progressbar' style='width: 100%' aria-valuemin='0' aria-valuemax='100'>Stavba doposud nezapočala</div>
            </div>";
        } else {
            $barHTML = "
            <div class='progress'>
                <div class='progress-bar progress-bar-striped bg-success progress-bar-animated' role='progressbar' style='width: " . $fromBeginPercent . "%' aria-valuemin='0' aria-valuemax='100'>" . $fromBegin->format('%a dní') . "</div>
                <div class='progress-bar progress-bar-striped bg-danger progress-bar-animated' role='progressbar' style='width: " . $toEndPercent . "%' aria-valuemin='0' aria-valuemax='100'>" . $toEnd->format('%a dní') . "</div>
            </div>";
        }
        $cardBody .= "<div class='tab-pane' role='tabpanel' id='constructionTime" . $project->getId() . "'>
                            <div class='row'>
                                 <div class='col-md-12'>
                                    <div class='card'>
                                        <div class='card-header card-header-text card-header-$style'>
                                            <div class='card-text'>
                                                <h4 class='card-title'>Doba Realizace</h4>
                                            </div>
                                        </div>
                                        <div class='card-body'>
                                            <div class='row justify-content-between'>
                                                <span class='col'>Datum předání staveniště: " . $constritonHandoverDate->format('d. m. Y') . "</span>
                                                <span>" . $project->baseInformation['constructionTime'] . "  $constructionTimeWeeksOrMonthLabel" . $suspensionAdded . "</span>
                                                <span class='col text-right'>Deadline: " . $constructionDeadline->format('d. m. Y') . "</span>
                                            </div>
                                            $barHTML
                                            $susBarHtml
                                        </div>
                                    </div>
                                </div>
                            </div>
                       </div>";
    }

    if ($template["objects"]['view'] == true) {
        $obejctHtml = '';
        foreach ($project->getObjects() as $obejct) {
            $obejctHtml .= $obejct->htmlCard($style, $idPhase);
        }

        if ($obejctHtml == '') {
            $obejctHtml = "<div class='col'><div class='card'><div class='card-body'><p>Projekt nemá přídružené objekty</p></div></div></div>";
        }

        $cardBody .= "<div class='tab-pane' role='tabpanel' id='objects" . $project->getId() . "'>
                            <div class='row'>
                            $obejctHtml
                            </div>
                       </div>";
    }


    foreach ($pricePanels as $pricePanel) {
        if ($template[$pricePanel]['view'] == true) {
            $cardBody .= "<div class='tab-pane' role='tabpanel' id='$pricePanel" . $project->getId() . "'>";
            foreach ($template[$pricePanel]['types'] as $type) {
                $label = (isset($type['label'])) ? "<div class='row'><div class='col'><h3>" . $type['label'] . "</h3></div></div>" : "";
                $cardBody .= $label;
                $cardBody .= "<div class='row'>";
                $isCenaSet = false;
                foreach ($type['prices'] as $price) {
                    $b = $project->getPricesByType($price['idPriceType']);
                    if ($b->getValue() == null) {
                        continue;
                    }
                    if ($price['dph']) {
                        $isCenaSet = true;
                        $cardBody .= "
                        <div class='col'>
                            <div class='box box-$style'>
                                {$b->getLabel(true)} <strong> {$b->getValueFormated(true)} s DPH</strong> (Cena bez DPH: {$b->getValueFormated(false)} Kč)
                            </div>
                        </div>";
                    } else {
                        $isCenaSet = true;
                        $cardBody .= "
                        <div class='col'>
                            <div class='box box-$style'>
                                {$b->getLabel(false)} <strong> {$b->getValueFormated(false)} Kč</strong>
                            </div>
                        </div>";
                    }
                }
                if (!$isCenaSet) {
                    $cardBody .= "<div class='col'>Nenalezeny žádné hodnoty cen.</div>";
                }
                $cardBody .= "</div>";
            }
            $cardBody .= "</div>";
        }
    }
    foreach ($contactPanels as $contactPanel) {
        if ($template[$contactPanel]['view'] == true) {
            $contactHtml = '';
            $isContactSet = false;
            foreach ($template[$contactPanel]['contacts'] as $contactTemplate) {
                $contact = $project->getContactByType($contactTemplate['idContactType']);
                $contact['phone'] = is_numeric($contact['phone']) ? number_format($contact['phone'], 0, ',', ' ') : 'Nevyplněno';
                if (!empty($contact['name'])) {
                    $isContactSet = true;

                    $contactHtml .= "<div class='col'>
                                    <div class='card'>
                                        <div class='card-header card-header-text card-header-$style'>
                                            <div class='card-text'>
                                                <h4 class='card-title'>$contact[contactTypeName]</h4>
                                            </div>
                                        </div>
                                        <div class='card-body'>
                                            $contact[name]<br>
                                            <a href='tel:+420$contact[phone]'>+420 " . $contact['phone'] . "</a><br>
                                            <a href='mailto:$contact[email]'>$contact[email]</a>
                                        </div>
                                    </div>
                                </div>";
                }
            }
            if (!$isContactSet) {
                $contactHtml .= "<p>Nenalezen žádný kontakt</p>";
            }
            $cardBody .= "<div class='tab-pane' role='tabpanel' id='$contactPanel" . $project->getId() . "'>
                                <h3>" . $project->getCompanyByType($template[$contactPanel]['idCompanyType'])['name'] . "</h3>
                                <div class='row'>
                                    $contactHtml
                                </div>
                          </div>";
        }
    }

    $etapizovanoPotomek = "";
    if ($etapaPotomek) {
        $etapizovanoPotomek = " <div class='card-header-primary'><h5>
                       <span class='font-weight-bold d-flex justify-content-start'><i class='material-icons'>alt_route</i> ETAPA PROJEKTU  ID: $nadrazenyProjekt </span> 
                    </h5></div>";
    }

    $html = "<div class='card'>
$etapizovanoPotomek
                <div class='card-header card-header-tabs card-header-$style'>
                   " . generateCardHeader($project, $detail, $idPhaseName, $requestExistNonTerminal) . "
                </div>
                <div class='card-body'>
                    <div class='tab-content' id='tabContent" . $project->getId() . "'>
                        $cardBody
                    </div>
                </div>
             </div>";
    return $html;
}


function getStatMonth2Projects()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($_SESSION['global_filtr']))
        $where = " WHERE u.idOu = " . $_SESSION['global_filtr'];
    elseif ($_SESSION['global_filtr'] === "my")
        $where = " WHERE projects.author = '" . $_SESSION['username'] . "' OR projects.editor = '" . $_SESSION['username'] . "'";
    else
        $where = "";
    $query = $dbh->getDbLink()->query("SELECT MONTH(created) as mesic,count(*) as countProjektu FROM 
( SELECT projects.*, ROW_NUMBER() OVER (PARTITION BY idProject ORDER BY idLocalProject DESC) as r FROM projects JOIN users u ON u.username = projects.editor $where) a WHERE r = 1 AND created > DATE_SUB(now(), INTERVAL 12 MONTH) 
                                                                                                                    GROUP BY MONTH(created)
");
    $graphData = $query->fetchAll(PDO::FETCH_ASSOC);
    return $graphData;

}


function getLastLogins($limit = 50, $interval = NULL, array $ouArr = null, $offset = NULL) // interval is in DAYS
{
    $loginDataArr = false;
    $where = "";
    if (is_array($ouArr)) {
        $where = " idOu IN (" . implode(",", $ouArr) . ") AND ";
    }
    if (is_numeric($limit)) {
        if ($interval && is_numeric($interval)) {
            if ($offset && is_numeric($offset)) {
                $intervalQuery = "AND loginTime > DATE_SUB(now(), INTERVAL " . ($interval + $offset) . " DAY) AND loginTime < DATE_SUB(now(), INTERVAL $offset DAY)";
            } else {
                $intervalQuery = "AND loginTime > DATE_SUB(now(), INTERVAL $interval DAY)";
            }
        } else {
            $intervalQuery = "";
        }
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $query = $dbh->getDbLink()->query("SELECT * FROM logins JOIN users USING(username) 
                                                        WHERE $where (result LIKE 'Prihlaseni probehlo, presmerovavam' OR result LIKE 'Neuspesne prihlaseni, spatne heslo.') $intervalQuery ORDER BY loginTime DESC LIMIT $limit");
        $loginDataArr = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $loginDataArr;
}

function changeAccessDeniedUser($username, $accessDenied)
{
    if (is_numeric($accessDenied)) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE users SET accessDenied = :pristup, updated = NOW() WHERE username LIKE :username");
        $stmt->bindValue(':pristup', $accessDenied, PDO::PARAM_INT);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $accessDenied;
        } else {
            echo 'Chyba pri zmene stavu';
        }
    }
}

function updateEditorReportSettings($editorReport)
{
    if ($editorReport === 'OFF') {
        $editorReport = 0;
    } else {
        $editorReport = 1;
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("UPDATE users SET editorReport = :report WHERE username = :username");
    $stmt->bindValue(':report', $editorReport, PDO::PARAM_INT);
    $stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
    if ($stmt->execute()) {
        echo $editorReport;
    } else {
        echo 'Chyba pri zmene stavu';
    }
}

function updateManagerReportSettings($idManagerReport)
{
    if (is_numeric($idManagerReport) && ($_SESSION['role'] == 'admin' or $_SESSION['role'] == 'adminEditor')) {
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("UPDATE users SET idReportConfig = :report WHERE username = :username");
        $stmt->bindValue(':report', $idManagerReport, PDO::PARAM_INT);
        $stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            echo "Nastavení manažerských emailoveých reportů změněno.";
        } else {
            echo 'Chyba při změně nastavení manažerských emailoveých reportů.';
        }
    }
}

function getUsersList()
{
    $usersArr = false;

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT users.name,username,email,updated,created,ou.name as ou,idOu, rangeRoleTypes.name as role, rangeRoleTypes.idRoleType, accessDenied, IFNULL(logins.loginTime,'Nikdy nepřihlášen') as lastLogin FROM `users` 
JOIN ou USING(idOu) JOIN rangeRoleTypes USING (idRoleType)
LEFT JOIN logins USING(username) WHERE (logins.idLogin = (SELECT MAX(idLogin) FROM logins WHERE username = users.username) OR NOT EXISTS (SELECT username FROM logins WHERE username = users.username)) AND idOu != 1 ORDER BY users.name");
    $usersArr = $query->fetchAll(PDO::FETCH_ASSOC);

    return $usersArr;

}

function getStatYear2Projects()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();

    if (is_numeric($_SESSION['global_filtr']))
        $where = " AND viewProjectsActive.idOu = " . $_SESSION['global_filtr'];
    elseif ($_SESSION['global_filtr'] === "my")
        $where = " AND (viewProjectsActive.author = '" . $_SESSION['username'] . "' OR viewProjectsActive.editor = '" . $_SESSION['username'] . "')";
    else
        $where = "";

    $query = $dbh->getDbLink()->query("SELECT YEAR(created) as rok,COUNT(idProject) as countProjektu

FROM viewProjectsActive 
WHERE idPhase = 1 $where
GROUP BY YEAR(created)
");
    $graphData = $query->fetchAll(PDO::FETCH_ASSOC);
    return $graphData;

}

function getDashboardStatsNonGraph(array $ouArr = null)
{
    $novostavbyCiselnikId = 1;

    if (is_array($ouArr)) {
        $whereNovostavby = " and idOu IN (" . implode(",", $ouArr) . ")";
        $whereAll = " WHERE idOu IN (" . implode(",", $ouArr) . ")";
        $whereMosty = " AND idOu IN (" . implode(",", $ouArr) . ")";
        $whereStatsAll = " AND idOu IN (" . implode(",", $ouArr) . ")";
    }

    if (is_numeric($_SESSION['global_filtr'])) {
        $whereNovostavby = " and idOu = " . $_SESSION['global_filtr'];
        $whereAll = " WHERE idOu = " . $_SESSION['global_filtr'];
        $whereMosty = " AND idOu = " . $_SESSION['global_filtr'];
        $whereStatsAll = " AND idOu = " . $_SESSION['global_filtr'];

    } elseif ($_SESSION['global_filtr'] === "my") {
        $whereNovostavby = " AND author = '" . $_SESSION['username'] . "' OR editor = '" . $_SESSION['username'] . "'";
        $whereAll = " WHERE author = '" . $_SESSION['username'] . "' OR editor = '" . $_SESSION['username'] . "'";
        $whereMosty = " AND author = '" . $_SESSION['username'] . "' OR editor = '" . $_SESSION['username'] . "'";
        $whereStatsAll = " AND author = '" . $_SESSION['username'] . "' OR editor = '" . $_SESSION['username'] . "'";

    } else {
        $whereNovostavby = "";
        $whereAll = "";
        $whereMosty = "";
        $whereStatsAll = "";
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT IFNULL(COUNT(idProject),0) as countNovostavby FROM viewProjectsActive WHERE idProjectType = $novostavbyCiselnikId $whereNovostavby");
    $nonGraphStatsNovostavby = $query->fetch();
    $query = $dbh->getDbLink()->query("SELECT IFNULL(COUNT(idProject),0) FROM viewProjectsActive $whereAll ");
    $nonGraphStatCountAll = $query->fetch();
    $query = $dbh->getDbLink()->query("SELECT IFNULL(SUM(IFNULL(cena, 0)), 0) as cenaMostu FROM (
SELECT value as cena FROM `viewProjectsActive` d JOIN prices USING(idProject) JOIN rangePriceTypes USING(idPriceType)
WHERE idProjectSubtype IN (7) AND `idPriceType` IN (5,6,11) AND ordering = (SELECT MAX(ordering) FROM prices tmp JOIN rangePriceTypes USING(idPriceType) WHERE idPriceType IN (5,6,11) AND tmp.idProject = d.idProject ) $whereMosty
UNION
SELECT attributes.value as cena FROM viewProjectsActive JOIN objects USING (idProject) JOIN attributes USING (idObject) WHERE idProjectSubtype NOT IN (7) AND objects.idObjectType = 1 AND attributes.idAttributeType = 1 $whereMosty
    ) cenyMostu");
    $nonGraphStatsMosty = $query->fetch();
    $query = $dbh->getDbLink()->query("SELECT IFNULL(SUM(IFNULL(value, 0)), 0) FROM `viewProjectsActive` d JOIN prices USING(idProject) JOIN rangePriceTypes USING(idPriceType)
WHERE `idPriceType` IN (5,6,11) AND ordering = (SELECT MAX(ordering) FROM prices tmp JOIN rangePriceTypes USING(idPriceType) WHERE idPriceType IN (5,6,11) AND tmp.idProject = d.idProject ) $whereStatsAll");
    $nonGraphStatsAll = $query->fetch();

    return array(
        'cenaMostu' => $nonGraphStatsMosty[0],
        'cenaStaveb' => $nonGraphStatsAll[0],
        'countNovostavby' => $nonGraphStatsNovostavby[0],
        'countStavebCelkem' => $nonGraphStatCountAll[0]
    );

}

function getFileTypeArr()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query('SELECT name,extension FROM rangeDocumentTypes WHERE hidden IS FALSE');
    $possibleFileTypes = $query->fetchAll();

    return $possibleFileTypes;
}

function arrActiveOus()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->query('SELECT DISTINCT idOu, ouName  FROM viewProjectsActive ');
    $editorsArr = $stmt->fetchAll();
    if (count($editorsArr) > 0) {
        return $editorsArr;
    } else {
        return FALSE;
    }

}

function selectActiveOus()
{
    $ousArr = arrActiveOus();
    $html = "";
    foreach ($ousArr as $ou) {
        $html .= "<option value='$ou[idOu]' data-value='$ou[idOu]'>$ou[ouName]</option>";
    }
    return $html;
}

function newFileUpload($file, $projectId, $idDocumentCategory, $description = null)
{
    ini_set('max_execution_time', 900);
    require_once __DIR__ . "/../conf/conf.php";
    $getFilesConf = getUploadStorage();
    try {
        if (is_numeric($projectId) && is_numeric($idDocumentCategory)) {
            $targetDir = __DIR__ . $getFilesConf['uploadDir'];
            $fileSizeLimit = $getFilesConf['fileSizeLimit'];
            $uploadStatus = false;
            // $originalName = str_replace(" ","_", escapeDiacritics(basename($file["file2Upload"]["name"]))); update 21 11 2022 uploadovany nazev souboru uz nebudem ukladat
            $fileType = strtolower(pathinfo($file["file2Upload"]["name"], PATHINFO_EXTENSION));
            $tempfile = $file['file2Upload']['tmp_name'];
            $fileSize = filesize($tempfile);
            if ($fileSize < $fileSizeLimit) {
                require_once __DIR__ . "/autoLoader.php";
                $dbh = new DatabaseConnector();
                $query = $dbh->getDbLink()->query('SELECT extension FROM rangeDocumentTypes WHERE hidden IS FALSE');
                $possibleFileTypes = $query->fetchAll(PDO::FETCH_COLUMN, 0);
                $query = $dbh->getDbLink()->query('SELECT idDocumentCategory FROM rangeDocumentCategories WHERE hidden IS FALSE');
                $possibleFileCategory = $query->fetchAll(PDO::FETCH_COLUMN, 0);
                $query = $dbh->getDbLink()->query('SELECT DISTINCT idProject FROM projects WHERE deletedDate IS NULL');
                $possibleProjectsId = $query->fetchAll(PDO::FETCH_COLUMN, 0);


                if ((in_array($fileType, $possibleFileTypes)) && (in_array($idDocumentCategory,
                        $possibleFileCategory)) && (in_array($projectId, $possibleProjectsId))) {
                    $query = $dbh->getDbLink()->query("SELECT idDocType FROM rangeDocumentTypes WHERE hidden IS FALSE AND extension LIKE '$fileType'");
                    $idDocType = $query->fetchColumn();
                    $query = $dbh->getDbLink()->query("SELECT CONCAT($projectId,'-',IFNULL(COUNT(idDocumentLocal),0)+1) as name, version FROM projects2documents WHERE idProject = $projectId AND version = 1");
                    $result = $query->fetch(PDO::FETCH_ASSOC);
                    $idDocument = $result['name'];
                    $namePrefix = $result['name'] . "_";
                    $version = 1;
                    $username = $_SESSION['username'];
                    $stmt = $dbh->getDbLink()->beginTransaction();
                    $fileSize = round(($fileSize / 1000000), 1);
                    $targetPath2Db = $getFilesConf['uploadDir'] . "/" . $projectId . "/" . $idDocumentCategory;
                    $stmt = $dbh->getDbLink()->prepare("INSERT INTO `projects2documents`(`idDocument`,  `idDocumentCategory`,  `path`, `description`, `idDocType`, `created`, `documentAuthor`, `idProject`, `version`, size) VALUES ('$idDocument', $idDocumentCategory, '$targetPath2Db' ,'$description', $idDocType, NOW(), '$username', $projectId, $version, $fileSize)");

                    if ($stmt->execute()) {
                        $lastIdLocal = $dbh->getDbLink()->lastInsertId();
                        insertActionLog(getLastProjectLocalFromProjectId($projectId), 9, $dbh);
                        $nameFile = $namePrefix . $lastIdLocal . "[ver." . $version . "]" . "." . $fileType;
                        $targetPath = $targetDir . "/" . $projectId . "/" . $idDocumentCategory;

                        $targetFile = $targetPath . "/" . $nameFile;

                        if (!file_exists($targetPath)) {
                            mkdir($targetPath, 0755, true);
                        }

                        if (move_uploaded_file($tempfile, $targetFile)) {
                            if (file_exists($targetFile)) {
                                $stmt = $dbh->getDbLink()->prepare("UPDATE `projects2documents` SET `name`= '$nameFile'  WHERE idDocumentLocal = $lastIdLocal");
                                $stmt->execute();
                                $stmt = $dbh->getDbLink()->commit();
                                $uploadStatus = true;
                                echo $idDocument;
                            } else {
                                $stmt = $dbh->getDbLink()->rollBack();
                                echo "Něco se pokazilo při uploadu, kontaktujte správce aplikace. (fileSystem)";
                            }

                        } else {
                            $stmt = $dbh->getDbLink()->rollBack();
                            echo "Něco se pokazilo při uploadu, kontaktujte správce aplikace. (fileSystem)";
                        }
                    } else {
                        $stmt = $dbh->getDbLink()->rollBack();
                        echo "Něco se pokazilo při uploadu, kontaktujte správce aplikace. (DB)";
                    }

                } else {
                    echo "Neplatna kategorie nebo typ souboru.";
                }
            } else {

                echo "Soubor je prilis velky. Aktualni limit je $fileSizeLimit bajtu";
            }
        } else {
            echo "neeee";
        }

    } catch (Exception $e) {
        writeError2Log(__FUNCTION__, $file, $e);
        return false;
    }

}

/*
 * $interval je ve dnech
 */

function getArrActionsLogByLimit($limit = 100)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (is_numeric($limit)) {
        $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogNoHidden` JOIN rangeActionTypes USING (idTypeAction) LIMIT $limit");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function getArrActionsLogByLimitForUsersProjects($limit = 100, $username = NULL)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (!isset($username)) {
        $username = $_SESSION['username'];
    }
    if (is_numeric($limit)) {
        $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogNoHidden` JOIN rangeActionTypes USING (idActionType) JOIN `viewProjectsActive` USING (idLocalProject) WHERE author = '$username' OR editor = '$username' LIMIT $limit");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function getArrActionsLogByInterval($interval = 30)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (is_numeric($interval)) {
        $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogNoHidden` JOIN rangeActionTypes USING (idActionType) WHERE `created`  < DATE_ADD(now(), INTERVAL $interval DAY) AND created > DATE_ADD(now(), INTERVAL -$interval DAY)");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function getArrActionsLogAllByInterval($interval = 30)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (is_numeric($interval)) {
        $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogAll` JOIN rangeActionTypes USING (idActionType) WHERE `created`  < DATE_ADD(now(), INTERVAL $interval DAY) AND created > DATE_ADD(now(), INTERVAL -$interval DAY)");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function getArrActionsLogByIntervalNonViewed($interval = 30)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (is_numeric($interval)) {
        $username = $_SESSION['username'];
        $query = $dbh->getDbLink()->query("SELECT viewActionsLogNoHidden.* FROM `viewActionsLogNoHidden` JOIN rangeActionTypes USING (idActionType) WHERE `created` < DATE_ADD(now(), INTERVAL $interval DAY) AND created > DATE_ADD(now(), INTERVAL -$interval DAY) AND idAction NOT IN ( SELECT idAction FROM notifications WHERE username='$username' ) ORDER BY created DESC ");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function insertViewedNotification($idAction)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $lastId = false;
    if (is_numeric($idAction)) {
        $query = $dbh->getDbLink()->query("SELECT idAction FROM `actionsLogs` WHERE idAction = $idAction");
        $possibleIdAction = $query->fetch(PDO::FETCH_COLUMN, 0);
        if ($possibleIdAction == $idAction) {
            $username = $_SESSION['username'];
            $stmt = $dbh->getDbLink()->prepare("INSERT INTO `notifications`(`username`, `idAction`, `viewed`) VALUES ('$username', $idAction, NOW())");

            if ($stmt->execute()) {
                $lastId = $dbh->getDbLink()->lastInsertId();
            }
        }
    }

    return $lastId;

}

function insertActionLog($idLocalProject, $idActionType, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    require_once __DIR__ . "/autoLoader.php";
    $lastId = false;
    if (is_numeric($idLocalProject) && is_numeric($idActionType)) {
        $username = $_SESSION['username'];
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `actionsLogs`(`idActionType`, `created`, `username`, `idLocalProject`) VALUES ($idActionType, NOW(), '$username', $idLocalProject)");
        if ($stmt->execute()) {
            $lastId = $dbh->getDbLink()->lastInsertId();
        }
    }

    return $lastId;
}

function getProjectIdFromidProjectLocal($idProjectLocal)
{
    $idProject = false;
    if (is_numeric($idProjectLocal)) {
        $dbh = new DatabaseConnector();
        require_once __DIR__ . "/autoLoader.php";
        $query = $dbh->getDbLink()->query("SELECT idProject FROM projects WHERE idProject = (SELECT idProject FROM projects WHERE idLocalProject = $idProjectLocal) AND idLocalProject < $idProjectLocal ORDER BY idLocalProject DESC LIMIT 1");
        $idProject = $query->fetchColumn();
    }

    return $idProject;
}

function getLastProjectLocalFromProjectId($projectId)
{
    $idProjectLocal = false;
    if (is_numeric($projectId)) {
        $dbh = new DatabaseConnector();
        require_once __DIR__ . "/autoLoader.php";
        $query = $dbh->getDbLink()->query("SELECT idLocalProject FROM `viewProjectsActive` WHERE  idProject = $projectId");
        $idProjectLocal = $query->fetchColumn();
    }

    return $idProjectLocal;
}

function findDiffProjects($idProjectLocal, $idProjectLocal2Compare = null)
{
    if (is_numeric($idProjectLocal)) {
        require_once __DIR__ . "/autoLoader.php";
        $returnZmenyArr = array();
        $dbh = new DatabaseConnector();
        if (is_null($idProjectLocal2Compare)) {
            $query = $dbh->getDbLink()->query("SELECT idLocalProject FROM `projects` WHERE idProject = (SELECT idProject FROM projects WHERE idLocalProject = $idProjectLocal) AND idLocalPRoject < $idProjectLocal ORDER BY idLocalProject DESC LIMIT 1");
            $idProjectLocal2Compare = $query->fetchColumn();
            $query = $dbh->getDbLink()->query("SELECT * FROM `projects` WHERE idLocalProject = $idProjectLocal ");
            $row2Compare1 = $query->fetch(PDO::FETCH_ASSOC);
            $query = $dbh->getDbLink()->query("SELECT * FROM `projects` WHERE idLocalProject = $idProjectLocal2Compare ");
            $row2Compare2 = $query->fetch(PDO::FETCH_ASSOC);
            // print_r($row2Compare1);
            if (count((array)$row2Compare1) > 1 && count((array)$row2Compare2) > 1) {
                foreach ($row2Compare1 as $indexSloupecku => $valueSloupecku) {
                    if ($valueSloupecku != $row2Compare2[$indexSloupecku]) {
                        $returnZmenyArr[$indexSloupecku] = $valueSloupecku;
                    }
                }
            } else {
                $returnZmenyArr = 'Nelze nalezt predka nebo projekt';
            }
        } else {
            if (is_numeric($idProjectLocal2Compare)) {
                $query = $dbh->getDbLink()->query("SELECT * FROM `projects` WHERE idLocalProject = $idProjectLocal ");
                $row2Compare1 = $query->fetch(PDO::FETCH_ASSOC);
                $query = $dbh->getDbLink()->query("SELECT * FROM `projects` WHERE idLocalProject = $idProjectLocal2Compare ");
                $row2Compare2 = $query->fetch(PDO::FETCH_ASSOC);
                if (count((array)$row2Compare1) > 1 && count((array)$row2Compare2) > 1) {
                    foreach ($row2Compare1 as $indexSloupecku => $valueSloupecku) {
                        if ($valueSloupecku != $row2Compare2[$indexSloupecku]) {
                            $returnZmenyArr[$indexSloupecku] = $valueSloupecku;
                        }
                    }
                } else {
                    $returnZmenyArr = 'Nelze nalezt predka nebo projekt';
                }

            }
        }
        return $returnZmenyArr;

    } else {
        echo "Neplatne vstupy";
    }

}

function updateFileUpload($file, $projectId, $idDocument, $idDocumentCategory, $description = null)
{
    require_once __DIR__ . "/../conf/conf.php";
    $getFilesConf = getUploadStorage();
    if (is_numeric($projectId) && is_numeric($idDocumentCategory)) {
        $targetDir = __DIR__ . $getFilesConf['uploadDir'];
        $fileSizeLimit = $getFilesConf['fileSizeLimit'];
        $uploadStatus = false;
        // $originalName =  str_replace(" ","_", escapeDiacritics(basename($file["file2Upload"]["name"])));
        $fileType = strtolower(pathinfo($file["file2Upload"]["name"], PATHINFO_EXTENSION));
        $tempfile = $file['file2Upload']['tmp_name'];
        $fileSize = filesize($tempfile);
        if ($fileSize < $fileSizeLimit) {
            require_once __DIR__ . "/autoLoader.php";
            $dbh = new DatabaseConnector();
            $query = $dbh->getDbLink()->query('SELECT extension FROM rangeDocumentTypes WHERE hidden IS FALSE');
            $possibleFileTypes = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            $query = $dbh->getDbLink()->query('SELECT idDocumentCategory FROM rangeDocumentCategories WHERE hidden IS FALSE');
            $possibleFileCategory = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            $query = $dbh->getDbLink()->query('SELECT DISTINCT idProject FROM projects WHERE deletedDate IS NULL');
            $possibleProjectsId = $query->fetchAll(PDO::FETCH_COLUMN, 0);

            //$clamav = new Clamav();

            if ((in_array($fileType, $possibleFileTypes)) && (in_array($idDocumentCategory,
                    $possibleFileCategory)) && (in_array($projectId, $possibleProjectsId))) {
                // $countNumber = str_replace($projectId, '', $idDocument);
                $query = $dbh->getDbLink()->query("SELECT idDocType FROM rangeDocumentTypes WHERE hidden IS FALSE AND extension LIKE '$fileType'");
                $idDocType = $query->fetchColumn();
                $query = $dbh->getDbLink()->query("SELECT version FROM viewDocumentsActualVersions WHERE idDocument = '$idDocument' ");
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $namePrefix = $idDocument . "_";
                $version = $result['version'] + 1;
                $targetPath2Db = $getFilesConf['uploadDir'] . "/" . $projectId . "/" . $idDocumentCategory;
                $username = $_SESSION['username'];
                $stmt = $dbh->getDbLink()->beginTransaction();
                $fileSize = round(($fileSize / 1000000), 1);
                $stmt = $dbh->getDbLink()->prepare("INSERT INTO `projects2documents`(`idDocument`,  `idDocumentCategory`, `path`, `description`, `idDocType`, `created`, `documentAuthor`, `IdProject`, `version`,size) VALUES ('$idDocument', $idDocumentCategory, '$targetPath2Db' ,'$description', $idDocType, NOW(), '$username', $projectId, $version,$fileSize)");

                if ($stmt->execute()) {
                    $lastIdLocal = $dbh->getDbLink()->lastInsertId();
                    insertActionLog(getLastProjectLocalFromProjectId($projectId), 5, $dbh);
                    $nameFile = $namePrefix . $lastIdLocal . "[ver." . $version . "]" . "." . $fileType;
                    $targetPath = $targetDir . "/" . $projectId . "/" . $idDocumentCategory;
                    $targetFile = $targetPath . "/" . $nameFile;
                    if (!file_exists($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }

                    if (move_uploaded_file($tempfile, $targetFile)) {
                        if (file_exists($targetFile)) {
                            $stmt = $dbh->getDbLink()->prepare("UPDATE `projects2documents` SET`name`= '$nameFile'  WHERE idDocumentLocal = $lastIdLocal");
                            $stmt->execute();
                            $stmt = $dbh->getDbLink()->commit();
                            $uploadStatus = true;
                            echo $idDocument;
                        } else {
                            $stmt = $dbh->getDbLink()->rollBack();
                            echo "Něco se pokazilo při uploadu, kontaktujte správce aplikace. (fileSystem)";
                        }

                    } else {
                        echo "Něco se pokazilo při uploadu, kontaktujte správce aplikace. (fileSystem)";
                    }
                } else {
                    $stmt = $dbh->getDbLink()->rollBack();
                    echo "Něco se pokazilo při uploadu, kontaktujte správce aplikace. (DB)";
                }

            } else {
                echo "Neplatna kategorie nebo typ souboru.";
            }
        } else {

            echo "Soubor je prilis velky. Aktualni limit je $fileSizeLimit bajtu";
        }
    } else {
        echo "neeee";
    }


}

function setWhereArr($getArr)
{
    if (is_array($getArr)) {
        $getEsc = htmlspecialcharsArr($getArr);
        $returnArr = [];
        if (isset($getEsc['idProjectType'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idProjectType']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idProjectType'] = $arrValue;
        }
        if (isset($getEsc['idProjectSubtype'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idProjectSubtype']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idProjectSubtype'] = $arrValue;
        }
        if (isset($getEsc['idCommunication'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idCommunication']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idCommunication'] = $arrValue;
        }
        if (isset($getEsc['idArea'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idArea']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idArea'] = $arrValue;
        }
        if (isset($getEsc['idPhase'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idPhase']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idPhase'] = $arrValue;
        }
        if (isset($getEsc['editor'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['editor']);
            $activeEdtitorArr = arrActiveEditors();
            if (count((array)$tempArr) > 0 && is_array($activeEdtitorArr)) {
                $activeEdtitorArr = call_user_func_array('array_merge', $activeEdtitorArr);

                foreach ($tempArr as $key => $item) {
                    if ($key == 0 && in_array($item, $activeEdtitorArr)) {
                        $arrValue .= "'" . $item . "'";
                    } elseif ($key > 0 && in_array($item, $activeEdtitorArr)) {
                        $arrValue .= "," . "'" . $item . "'";
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['editor'] = $arrValue;
        }
        if (isset($getEsc['ou'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['ou']);
            $activeOurArr = arrActiveOus();
            if (count((array)$tempArr) > 0 && is_array($activeOurArr)) {
                $activeOurArr = call_user_func_array('array_merge', $activeOurArr);

                foreach ($tempArr as $key => $item) {
                    if ($key == 0 && in_array($item, $activeOurArr)) {
                        $arrValue .= "'" . $item . "'";
                    } elseif ($key > 0 && in_array($item, $activeOurArr)) {
                        $arrValue .= "," . "'" . $item . "'";
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idOu'] = $arrValue;
        }
        if (isset($getEsc['idProject'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idProject']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idProject'] = $arrValue;
        }

        if (isset($getEsc['idFinSources'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['idFinSources']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['idFinSource'] = $arrValue;
        }
        if (isset($getEsc['contactBuildManager'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['contactBuildManager']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['contactBuildManager'] = $arrValue;
        }
        if (isset($getEsc['buildCompanyId'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['buildCompanyId']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['buildCompanyId'] = $arrValue;
        }
        if (isset($getEsc['supervisorCompanyId'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['supervisorCompanyId']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['supervisorCompanyId'] = $arrValue;
        }
        if (isset($getEsc['projectCompanyId'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['projectCompanyId']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['projectCompanyId'] = $arrValue;
        }
        if (isset($getEsc['contactDesigner'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['contactDesigner']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['contactDesigner'] = $arrValue;
        }

        if (isset($getEsc['contactSupervisor'])) {
            $arrValue = "";
            $tempArr = explode(",", $getEsc['contactSupervisor']);
            if (count((array)$tempArr) > 0) {
                foreach ($tempArr as $key => $item) {
                    if (is_numeric($item) && $key == 0) {
                        $arrValue .= $item;
                    } elseif (is_numeric($item) && $key > 0) {
                        $arrValue .= "," . $item;
                    }
                }
            } elseif (count((array)($tempArr)) == 0 && is_numeric($tempArr[0])) {
                $arrValue = $tempArr[0];
            }
            $returnArr['contactSupervisor'] = $arrValue;
        }
        // print_r($returnArr);
        return array_filter($returnArr); //filter se zbavuje nevalidnich value NULL atp
    }

}

function setWhereCond($whereArr)
{
    // print_r($whereArr);
    $whereCond = " ";
    $countCond = count((array)$whereArr);
    if ($countCond > 0) {
        foreach ($whereArr as $key => $value) {
            $whereCond .= $key . " IN (" . $value . ") AND ";
        }
        $whereCond .= " 1";
    } else {
        $whereCond = "1";
    }
    //echo $whereCond;
    return $whereCond;
}

function setOrderBy($orderBy = null)
{
    $default = "ORDER BY disableEtapa DESC, priorityScore,idPhase, idProject DESC";
    $order = $default;
    switch ($orderBy) {
        case "ID_desc":
            $order = "ORDER BY disableEtapa DESC, idProject DESC, priorityScore,idPhase ";
            break;
        case "faze_desc":
            $order = "ORDER BY disableEtapa DESC,idPhase ASC, priorityScore ,idProject DESC  ";
            break;
        case "priorita_desc":
            $order = "ORDER BY disableEtapa DESC, priorityScore DESC,idPhase, idProject DESC";
            break;
        case "ID_asc":
            $order = "ORDER BY disableEtapa DESC, idProject ASC, priorityScore,idPhase ";
            break;
        case "faze_asc":
            $order = "ORDER BY disableEtapa DESC,idPhase DESC,priorityScore ,idProject ASC  ";
            break;
        case "priorita_asc":
            $order = "ORDER BY disableEtapa DESC, priorityScore ASC,idPhase, idProject ASC";
            break;
        case "project_type_asc":
         $order = "ORDER BY idProjectType DESC, priorityScore DESC ,idPhase, idProject ASC";
            break;
    }
    return $order;
}

/**
 * @return array
 */
function getFilteredProjects($get = null, $limit, $active)
{

    $offset = ($active - 1) * $limit;
    $whereArr = setWhereArr($get);
    $whereStatement = setWhereCond($whereArr);
    $orderCon = setOrderBy($get['projectsOrder']);

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT DISTINCT idProject,idPhase, phaseName FROM viewProjectsWithJoinsActive 
    WHERE $whereStatement $orderCon LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    //print_r($stmt);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


function getRelationsIdPotomci(int $idProject)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare(
        "SELECT DISTINCT idProject,idPhase FROM viewProjectsWithJoinsActive 
    WHERE idProject IN (SELECT idProjectRelation FROM projectRelations WHERE  idRelationType IN (3) AND idProject = $idProject) ");
    //print_r($stmt);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getRelationsIdRodic(int $idProject)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare(
        "SELECT DISTINCT idProject FROM viewProjectsWithJoinsActive 
    WHERE idProject IN (SELECT idProjectRelation FROM projectRelations WHERE  idRelationType IN (2) AND idProject = $idProject) LIMIT 1");
    //print_r($stmt);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;
}


function getNumberOgFilteredProjects($get = null)
{
    $whereArr = setWhereArr($get);
    $whereStatement = setWhereCond($whereArr);

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT count(DISTINCT idProject) FROM viewProjectsWithJoinsActive  
      WHERE $whereStatement
    ORDER BY idPhase ASC");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result[0];
}

/**
 * @param $projects
 * @return null|string
 */
function generateProjectsListing($projects, $detail = false)
{

    $html = null;
    $relations = null;
    $newOrderedArr = [];
    $newOrderedArrKey = 0;
    // print_r("OD DEBUG:");
    //print_r($projects);
    if (!empty($projects)) {
        //print_r($projects);
        foreach ($projects as $key => $project) {
            $rodic = getRelationsIdRodic($project['idProject']);

            $project['etapaPotomek'] = is_numeric($rodic) ? true : false;
            $project['etapaRodicId'] = is_numeric($rodic) ? $rodic : null;
            //$project['phaseName'] = '';
            //print_r($relatedProject['etapaRodicId']);
            if (!array_search($project['idProject'], array_column($newOrderedArr, 'idProject'))) {
                $newOrderedArr[$newOrderedArrKey] = $project;
                $newOrderedArrKey++;
            }

            $relationsPotomci = getRelationsIdPotomci($project['idProject']);
            if (!empty($relationsPotomci)) {
                foreach ($relationsPotomci as $relatedProject) {
                    $keyArr = null;
                    $keyArr = array_search($relatedProject['idProject'], array_column($projects, 'idProject'));
                    if ($keyArr) {
                        unset($projects[$keyArr]);
                        // print_r($projects);

                    }
                    $relatedProject['etapaPotomek'] = true;
                    $relatedProject['etapaRodicId'] = $project['idProject'];
                    $newOrderedArr[$newOrderedArrKey] = $relatedProject;
                    $newOrderedArrKey++;

                }
            }
        }
        //  print_r($newOrderedArr);
        foreach ($newOrderedArr as $newProjectsOrder) {
            $style = styleByPhase($newProjectsOrder['idPhase']);

            $html .= generateProjectCard($newProjectsOrder['idProject'], $newProjectsOrder['idPhase'], $newProjectsOrder['phaseName'], $style, $detail, $newProjectsOrder['etapaPotomek'], $newProjectsOrder['etapaRodicId']);
        }
    } else {
        $html = "Nebyly nalezeny žádné projekty";
    }
    return $html;
}

/**
 * @param $idProject
 * @return bool|int
 */
function deleteProject($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $countVersions = findProjectVersions($idProject);
    $countDeleted = false;
    $username = $_SESSION['username'];
    if (is_numeric($idProject) && $countVersions != false) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
        switch ($_SESSION['role']) {
            case 'adminEditor':
                $stmt = $dbh->getDbLink()->prepare('UPDATE projects SET deletedDate = NOW(), deleteAuthor = :username WHERE idLocalProject = (SELECT * FROM (SELECT MAX(tmp.idLocalProject) FROM projects tmp WHERE tmp.idProject = :idProject) tmp2) ');
                $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $countDeleted = $stmt->rowCount();
                }
                break;
            case 'admin':
                $stmt = $dbh->getDbLink()->prepare('UPDATE projects SET deletedDate = NOW(), deleteAuthor = :username WHERE idLocalProject = (SELECT * FROM (SELECT MAX(tmp.idLocalProject) FROM projects tmp WHERE tmp.idProject = :idProject) tmp2) ');
                $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $countDeleted = $stmt->rowCount();
                }
                break;
            case 'editor':
                $stmt = $dbh->getDbLink()->prepare('UPDATE projects SET deletedDate = NOW(), deleteAuthor = :username WHERE idLocalProject = (SELECT * FROM (SELECT MAX(tmp.idLocalProject) FROM projects tmp WHERE tmp.idProject = :idProject) tmp2) AND editor = :editor ');
                $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
                $stmt->bindParam(':editor', $_SESSION['username'], PDO::PARAM_STR);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $countDeleted = $stmt->rowCount();
                }

        }
        if ($countDeleted == 0) {
            $stmt = $dbh->getDbLink()->rollBack();
            $countDeleted = false;
        } elseif ($countDeleted > 0) {
            insertActionLog(getLastProjectLocalFromProjectId($idProject), 12, $dbh);
            $stmt = $dbh->getDbLink()->commit();
        }
    }
    return $countDeleted;
}


function copyProject($idProject, $dbh = null)
{
    $lastId = false;
    if (is_numeric($idProject)) {
        try {
            if (is_null($dbh)) {
                $dbh = new DatabaseConnector();
            }
            require_once __DIR__ . "/autoLoader.php";
            $stmt = $dbh->getDbLink()->prepare('INSERT INTO `projects` SELECT * FROM viewProjectsActualVersionsNoIdLocal
             WHERE viewProjectsActualVersionsNoIdLocal.idProject = :idProject');
            $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $lastId = $dbh->getDbLink()->lastInsertId();
            }
        } catch (Exception $e) {
            $lastId = 'Chyba DB pri kopirovani udaju projektu, Chyba: ' . $e;
        }
    }
    return $lastId;
}

function writeError2Log($typ, $data, $e)
{
    $date = date('m/d/Y h:i:s a', time());
    $myfile = fopen(SYSTEMLOGS . "/error.log", "a");
    fwrite($myfile, PHP_EOL . $date);
    fwrite($myfile, PHP_EOL . $typ);
    fwrite($myfile, PHP_EOL . print_r($data, true));
    fwrite($myfile, PHP_EOL . $e->getLine());
    fwrite($myfile, PHP_EOL . $e->getMessage());

    fclose($myfile);
}

function updateAssignments($idProject, $assignments)
{

    $lastId = false;
    $username = $_SESSION['username'];
    $assignments = htmlspecialchars($assignments);
    if (is_numeric($idProject)) {
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
        $lastId = copyProject($idProject, $dbh);
        if ($lastId != false) {
            try {
                $stmt = $dbh->getDbLink()->prepare('UPDATE `projects` SET author = :author, assignments=:assignments, created=NOW() WHERE idLocalProject = :idLocalProject');
                $stmt->bindParam(':idLocalProject', $lastId, PDO::PARAM_INT);
                $stmt->bindParam(':author', $username, PDO::PARAM_STR);
                $stmt->bindParam(':assignments', $assignments, PDO::PARAM_STR);
                $stmt->execute();
                insertActionLog($lastId, 6, $dbh);
                $stmt = $dbh->getDbLink()->commit();
            } catch
            (Exception $e) {
                $stmt = $dbh->getDbLink()->rollBack();
                $lastId = 'Chyba transakce, vracím změny zpět. Chyba: ' . $e;
            }
        }
    }
    return $lastId;
}


function insertCommunication2Project($idProject, $arrComm, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }

    $lastId = false;
    if (is_numeric($idProject) && is_array($arrComm)) {
        if (!isset($arrComm['stationingFrom'])) {
            $statFrom = null;
        } else {
            $statFrom = $arrComm['stationingFrom'];
        }

        if (!isset($arrComm['stationingTo'])) {
            $statTo = null;
        } else {
            $statTo = $arrComm['stationingTo'];
        }

        if (!isset($arrComm['comment'])) {
            $comment = "";

        } else {
            $comment = $arrComm['comment'];
        }

        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `project2communication`(`idProject`, `idCommunication`, `stationingFrom`, `stationingTo`, `gpsN1`, `gpsN2`, `gpsE1`, `gpsE2`, `allPoints`, comment) VALUES (:idProject,:idCommunication,:stationingFrom,:stationingTo,:gpsN1,:gpsN2,:gpsE1,:gpsE2, :allPoints, :comment);");
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idCommunication', $arrComm['idCommunication'], PDO::PARAM_INT);
        $stmt->bindValue(':stationingFrom', $statFrom, PDO::PARAM_STR);
        $stmt->bindValue(':stationingTo', $statTo, PDO::PARAM_STR);
        $stmt->bindValue(':gpsN1', $arrComm['gpsN1'], PDO::PARAM_STR);
        $stmt->bindValue(':gpsN2', $arrComm['gpsN2'], PDO::PARAM_STR);
        $stmt->bindValue(':gpsE1', $arrComm['gpsE1'], PDO::PARAM_STR);
        $stmt->bindValue(':gpsE2', $arrComm['gpsE2'], PDO::PARAM_STR);
        $stmt->bindValue(':allPoints', $arrComm['allPoints'], PDO::PARAM_STR);
        $stmt->bindValue(':comment', htmlspecialchars(trim($comment)), PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = true;
        }
    }

    return $lastId;


}

function insertArea2Project($idProject, $areaId, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idProject) && is_numeric($areaId)) {
        $stmt = $dbh->getDbLink()->prepare("
 DELETE FROM `project2area` WHERE idProject = :idProject AND idArea = :idArea;
INSERT INTO `project2area`(`idProject`, `idArea`) VALUES (:idProject,:idArea);");
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idArea', $areaId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $lastId = true;
        }

    }

    return $lastId;
}

function insertAtt2Object($idObject, $idAttType, $value, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idObject) && is_numeric($idAttType)) {
        $value = htmlspecialchars($value);
        $stmt = $dbh->getDbLink()->prepare("
INSERT INTO `attributes`(`idObject`, `idAttributeType`, `value`) VALUES (:idObject,:idAttributeType,:value) ON DUPLICATE KEY UPDATE value = :value");
        $stmt->bindValue(':idObject', $idObject, PDO::PARAM_INT);
        $stmt->bindValue(':idAttributeType', $idAttType, PDO::PARAM_INT);
        $stmt->bindValue(':value', $value, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = true;
        }
    }
    return $lastId;
}


function logPost($post)
{
    $date = date('m/d/Y h:i:s a', time());
    $myfile = fopen(SYSTEMLOGS . "/lastInserted.log", "w");
    fwrite($myfile, '\n' . print_r($_POST, true));
    fclose($myfile);
}

function insertRelation($idRelationType, $idProject, $idProjectRelation, $dbh = null, $onlyOnce = false)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `projectRelations`(`username`, `idProject`, `idRelationType`, `idProjectRelation`, `created`) VALUES (:username,:idProject,:idRelationType,:idProjectRelation,NOW())");
        $stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idRelationType', $idRelationType, PDO::PARAM_INT);
        $stmt->bindValue(':idProjectRelation', $idProjectRelation, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $lastId = true;
            $relationInfoArr = getRelationInfo($idRelationType);
            if (!$onlyOnce) {
                insertRelation($relationInfoArr['relationFromProjectRelation'], $idProjectRelation, $idProject, $dbh, true);
            }
        }
    }
    return $lastId;
}

function insertPrice($idPriceType, $idProject, $value = null, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if ($value >= 0) {
        $value = floatval($value);
    } else {
        throw new Exception('Neplatny vstup, ocekavan float');
    }
    if (is_numeric($idProject) && is_numeric($value)) {
        $stmt = $dbh->getDbLink()->prepare("
        DELETE FROM `prices` WHERE idProject = :idProject AND idPriceType = :idPriceType;
        INSERT INTO `prices`(`idPriceType`, `idProject`, `value`) VALUES (:idPriceType,:idProject,:value);
        
        ");
        $stmt->bindValue(':idPriceType', $idPriceType, PDO::PARAM_INT);
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':value', $value, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $lastId = true;
        }
    }
    return $lastId;
}

function insertCompany2Project($idProject, $idCompany, $idCompanyType, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("
        DELETE FROM `project2company` WHERE idProject = :idProject AND idCompanyType = :idCompanyType;
        INSERT INTO `project2company`(`idProject`, `idCompany`, `idCompanyType`) VALUES (:idProject,:idCompany,:idCompanyType);
        ");
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idCompany', $idCompany, PDO::PARAM_INT);
        $stmt->bindValue(':idCompanyType', $idCompanyType, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $lastId = true;
        }
    }
    return $lastId;
}

function insertContacts2Project($idProject, $idContact, $idContanctType, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("
        DELETE FROM `project2contact` WHERE idProject = :idProject AND idContactType = :idContactType;
        INSERT INTO `project2contact`(`idProject`, `idContact`, `idContactType`) VALUES (:idProject,:idContact,:idContactType);
        ");
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idContact', $idContact, PDO::PARAM_INT);
        $stmt->bindValue(':idContactType', $idContanctType, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $lastId = true;
        }
    }
    return $lastId;
}


function insertDeadlines2Project($idProject, $idDeadlineType, $value = null, $note = null, $dbh = null)
{
    $deadlineValue = date("Y-m-d", strtotime(str_replace('/', '-', $value)));
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("
        DELETE FROM `deadlines` WHERE idProject = :idProject AND idDeadlineType = :idDeadlineType;
        INSERT INTO `deadlines`(`idProject`, `idDeadlineType`, `value`, note, inserted, inserted_by) VALUES (:idProject,:idDeadlineType,:value, :note, NOW(), :username);
        ");
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idDeadlineType', $idDeadlineType, PDO::PARAM_INT);
        $stmt->bindValue(':value', $deadlineValue, PDO::PARAM_STR);
        $stmt->bindValue(':note', htmlspecialchars($note), PDO::PARAM_STR);
        $stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            $lastId = true;
        }
    }
    return $lastId;
}

function flushDeadline($idProject, $idDeadlineType, $dbh = null)
{
    if (is_null($dbh)) {
        $dbh = new DatabaseConnector();
    }
    $lastId = false;
    if (is_numeric($idProject)) {
        $stmt = $dbh->getDbLink()->prepare("
      DELETE FROM `deadlines` WHERE idProject = :idProject AND idDeadlineType = :idDeadlineType;        

        ");
        $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->bindValue(':idDeadlineType', $idDeadlineType, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $lastId = true;
        }
    }
    return $lastId;
}

function insertObject($idProject, $arrObj, $dbh = null)
{
    if (is_null($dbh)) {
        $dbhExist = false;
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->beginTransaction();
    }
    $lastId = false;
    if (is_numeric($idProject) && is_array($arrObj)) {
        if (!isset($arrObj['idObject'])) {
            $stmt = $dbh->getDbLink()->prepare("
       
INSERT INTO `objects`(`idProject`, `idObjectType`, `name`) VALUES (:idProject,:idTypeObject,:name) ON DUPLICATE KEY UPDATE name = :name ");
            $stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
            $stmt->bindValue(':idTypeObject', $arrObj['idObjectType'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $arrObj['name'], PDO::PARAM_STR);
            $stmt->execute();
            $lastId = $dbh->getDbLink()->lastInsertId();
        } else {
            $lastId = $arrObj['idObject'];
        }
        if (isset($arrObj['attribute']) && is_array($arrObj['attribute'])) {
            foreach ($arrObj['attribute'] as $arrEachAtt) {
                //if (!insertAtt2Object($arrObj['idTypeObject'], $arrEachAtt['idAttributeType'], $arrEachAtt['value'] == FALSE)) {
                if (!insertAtt2Object($lastId, $arrEachAtt['idAttributeType'], $arrEachAtt['value'],
                    $dbh)) {
                    return false;
                }
            }
        }
    }
    return $lastId;
}

/**
 * @param $phaseLevel
 * @return null|string
 */
function generateNextPhaseForm($idPhase, $project)
{
    switch ($idPhase) {
        case 5:
            $html = generateTopicPhaseSwitchForm($project, $idPhase);
            break;
        case 4:
            $html = generateInPreparationPhaseSwitchForm($project, $idPhase);
            break;
        case 3:
            $html = generateReadyPhaseSwitchForm($project, $idPhase);
            break;
        case 2:
            $html = generateInProgressPhaseSwitchForm($project, $idPhase);
            break;
        case 1:
            $html = generateDoneSwitchForm($project);
            break;
    }
    return $html;

}

function getPriceType($idPriceType)
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT idPriceType, name, idPriceSubtype FROM rangePriceTypes WHERE idPriceType=:idPriceType LIMIT 1");
    $stmt->bindValue(':idPriceType', $idPriceType, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
}

function getConatctType($idContactType)
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM rangeContactTypes WHERE idContactType = :idContactType LIMIT 1");
    $stmt->bindValue(':idContactType', $idContactType, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
}

function deadlineFormFromTemplate($deadlineTemplates, $project, $seed)
{
    $deadlineHtml = "";
    $hashToken = generateHash(date('H'));

    foreach ($deadlineTemplates as $deadlineTemplate) {
        $deadline = $project->getDeadlineByType($deadlineTemplate['idDeadlineType']);
        $deadlineValue = ($deadline['value'] != null) ? date("d-m-Y", strtotime($deadline['value'])) : '';
        $deleteButton = "";
        if (!$deadline["hidden"]) {
            $deleteButton = "<span class='close-button float-right remove-deadline' deadline-type='" . $deadline['idDeadlineType'] . "' project-id='" . $project->getId() . "' token='$hashToken'>X</span>";
        }
        $deadlineHtml .= "<div class='col-md-6 deadline-col'>
                            <div class='input-group form-control-lg'>
                                <div class='input-group-prepend'>
                                    <span class='input-group-text'>
                                      <i class='material-icons'>date_range</i>
                                    </span>
                                </div>
                                <div class='form-group col'>
                                    $deleteButton
                                    <label>$deadline[deadlineTypeName]
                                    </label>                                       
                                    <input type='text' name='deadlines[$seed][value]'  class='form-control datetimepicker " . $deadlineTemplate['class'] . "' value=$deadlineValue>
                                    <input type='text' name='deadlines[$seed][note]' placeholder='Poznámka' class='form-control " . $deadlineTemplate['class'] . "' value='" . htmlspecialchars_decode($deadline['note']) . "'>
                                    <input type='hidden' name='deadlines[$seed][idDeadlineType]' value='" . $deadline['idDeadlineType'] . "'>
                                </div>
                            </div>
                        </div>";
        $seed++;
    }

    return $deadlineHtml;
}

function deadlineTemplatesForProject($project)
{
    $deadlines = $project->getDeadlinesForForm();
    $deadlineTemplates = array();
    foreach ($deadlines as $deadline) {
        array_push($deadlineTemplates, array(
            'idDeadlineType' => $deadline['idDeadlineType'],
            'class' => 'deadline'
        ));
    }
    return $deadlineTemplates;
}

function priceFormFromTemplate($priceTemplate, $project)
{
    $prices = "";
    foreach ($priceTemplate as $key => $template) {
        $price = $project->getPricesByType($template['idPriceType']);
        $class = (isset($template['class'])) ? $template['class'] : null;
        $prices .= "<div class='col-md-6'>{$price->formRepresenation($key,$template['dph'],$class)}</div>";
    }
    return $prices;
}


function contactsFromTemplate($contactTemplate, $project, $seed)
{
    $contactsHtml = "";
    foreach ($contactTemplate as $contactId) {
        $disableCheckbox = '';
        if ($contactId['disable'] == true) {
            $disableCheckbox = "<div class='togglebutton '>
                                    <label>
                                    <input type='checkbox' id='toggleContact$contactId[idContactType]' class='contactToggle' checked='true'>
                                        <span class='toggle'></span>Nevyplňuje se
                                    </label>
                                </div>";
        }
        $contact = $project->getContactByType($contactId['idContactType']);
        $defaultOption = (is_array($contact) && count($contact)) ? "<option selected value=$contact[idContact] >$contact[text]</option>" : '';
        $contactsHtml .= "<div class='row contactWrapper' id='contact'>
                            <div class='col-md-12'>
                                <h4>$contact[contactTypeName]</h4>$disableCheckbox
                            </div>
                            <div class='col-md-6'>
                                <div class='input-group form-control-lg'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text'>
                                          <i class='material-icons'>person</i>
                                        </span>
                                    </div>
                                                <div class='col'>
                                                    <label style='width: 100%' for='selectContact$contactId[idContactType]'>Vyberte kontakt
                                                        <select id='selectContact$contactId[idContactType]'  name='contact[$seed][idContact]' class='select2 filterSelect selectContact'style='width: 100%' data-ajaxurl = 'getContact.php' title='Vyberte kontakt' >
                                                        $defaultOption
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
 
                            </div>     
                            <div class='col-md-6'>
                            <button class='btn btn-primary float-right plusButton' id='newContact' data-toggle='modal' data-target='#contactModal' style='margin-top: 15px'><i class='fa fa-plus'></i>Přidat nový kontakt<div class='ripple-container'></div></button>
</div>                                       
                            <input type='hidden' name='contact[$seed][idContactType]' value='$contactId[idContactType]'>
                        </div>";
        $seed++;
    }

    return $contactsHtml;
}

function generateTopicPhaseSwitchForm($project, $idPhase)
{
    $priceTemplate = array(
        array(
            "idPriceType" => 6,
            "dph" => false,
            "class" => ''
        ), array(
            "idPriceType" => 8,
            "dph" => false,
            "class" => ''
        ), array(
            "idPriceType" => 4,
            "dph" => false,
            "class" => 'mergedPrice'
        ), array(
            "idPriceType" => 3,
            "dph" => false,
            "class" => 'mergedPrice'
        ), array(
            "idPriceType" => 13,
            "dph" => false,
            "class" => 'prePricePDAD'
        )
    );
    $form = generateTopicPhaseForm($project, $priceTemplate, $idPhase);

    $html = "
    <form method='post' action='/submits/changeProjectPhaseSubmit.php' id='nextPhaseForm' class='form-horizontal phaseForm'>
        <input type='hidden' value='" . $_SESSION['username'] . "' name='editor'>
        <input type='hidden' value='" . $project->getId() . "' name='idProject'>
         <input type='hidden' value='0' name='inConcept'>

            $form
       
        <div class='row float-right'>
            <div class='col'>
                <input type='submit' id='saveValidate' class='btn btn-danger btn-wd' value='Změna fáze do Záměru' name='finish'>
            </div>
        </div>
    </form>
    ";
    return $html;

}

function generateIntensionPhaseSwitchForm($project, $idPhase)
{

    $company = $project->getCompanyByType(1);

    if ($project->baseInformation['mergePricePDAD'] == 1) {
        $priceTemplate = array(
            array(
                "idPriceType" => 14,
                "dph" => false
            )
        );
    } else {
        $priceTemplate = array(
            array(
                "idPriceType" => 2,
                "dph" => false
            ), array(
                "idPriceType" => 1,
                "dph" => false
            )
        );
    }

    $prices = priceFormFromTemplate($priceTemplate, $project);

    $html = "
    <form method='post' action='/submits/changeProjectPhaseSubmit.php' id='nextPhaseForm' class='form-horizontal phaseForm'>
        <input type='hidden' value='" . $_SESSION['username'] . "' name='editor'>
        <input type='hidden' value='" . $project->getId() . "' name='idProject'>
        <input type='hidden' value='" . $idPhase . "' name='idPhase'>
        <div class='row'>
            $prices
        </div>
       
        <div class='row float-right'>
            <div class='col'>
                <input type='submit' id='saveNoValidate' formnovalidate='formnovalidate' class='btn btn-danger btn-wd noEnterSubmit' value='Uložit jako koncept' name='finish'>
                <input type='submit' id='saveValidate' class='btn btn-danger btn-wd' value='Změna fáze do fáze v přípravě' name='finish'>
            </div>
        </div>
    </form>
    ";
    return $html;
}


/**
 * @return string
 */
function generateInPreparationPhaseSwitchForm($project, $idPhase)
{

    $company = $project->getCompanyByType(1);

    if ($project->baseInformation['mergePricePDAD'] == 1) {
        $priceTemplate = array(
            array(
                "idPriceType" => 14,
                "dph" => false
            )
        );
    } else {
        $priceTemplate = array(
            array(
                "idPriceType" => 2,
                "dph" => false
            ), array(
                "idPriceType" => 1,
                "dph" => false
            )
        );
    }

    $prices = priceFormFromTemplate($priceTemplate, $project);

    $contactTemplate = array(
        array(
            "idContactType" => 1,
            "disable" => false,
            "group" => 'contactProjectContractor'
        ), array(
            "idContactType" => 2,
            "disable" => false,
            "group" => 'contactProjectContractor'
        ), array(
            "idContactType" => 3,
            "disable" => false,
            "group" => 'contactProjectContractor'
        ), array(
            "idContactType" => 4,
            "disable" => false,
            "group" => 'contactProjectContractor'
        )
    );

    $contactsHtml = contactsFromTemplate($contactTemplate, $project, 0);

    $deadlineTemplates = array(
        array(
            'idDeadlineType' => 23,
            'class' => 'sp'
        )
    );

    /* $durUrChecked = ((int)$project->baseInformation['deadlineDurUrRequired'] === 0) ? '' : 'checked';
     $studyChecked = ((int)$project->baseInformation['deadlineStudyRequired'] === 0) ? '' : 'checked';
     $eiaChecked = ((int)$project->baseInformation['deadlineEIARequired'] === 0) ? '' : 'checked';
     $mergedDeadlinesChecked = ((int)$project->baseInformation['mergedDeadlines'] === 0) ? '' : 'checked';
     $tesChecked = ((int)$project->baseInformation['deadlineTesRequired'] === 0) ? '' : 'checked'; */

    // 1. zasmluvněný stupeň PD - pokud existuje, tak vyplnit jinak nechat prázný jeden deadline box se selectem termínů z dostupných termínů pro fázi 5
    $projectDeadline = $project->getDeadlines();
    $deadlineType = empty($projectDeadline) ? 0 : $projectDeadline[0]['idDeadlineType'];
    $deadlineValue = empty($projectDeadline) ? "" : date("d-m-Y", strtotime($projectDeadline[0]['value']));
    $deadlineNote = empty($projectDeadline) ? "" : $projectDeadline[0]['note'];
    $deadlineTypeSelectOptionsHtml = "";
    $deadlineTypesAvailable = selectDeadlineTypesAvailableOnlyInPhase(5);
    foreach ($deadlineTypesAvailable as $deadlineTypeAvailable) {
        $selected = ($deadlineTypeAvailable['idDeadlineType'] == $deadlineType) ? "selected" : "";
        $deadlineTypeSelectOptionsHtml .= "<option value='$deadlineTypeAvailable[idDeadlineType]' $selected>$deadlineTypeAvailable[name]</option>";
    }


    $html = "
    <form method='post' action='/submits/changeProjectPhaseSubmit.php' id='nextPhaseForm' class='form-horizontal phaseForm'>
        <input type='hidden' value='" . $_SESSION['username'] . "' name='editor'>
        <input type='hidden' value='" . $project->getId() . "' name='idProject'>
        <input type='hidden' value='" . $idPhase . "' name='idPhase'>
        <div class='row'>
            $prices
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <h3>Zhotovitel projektu</h3>
            </div>
            <div class='col-md-6'>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>business</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <select id='contractorProjectCompany' required name='company[0][idCompany]' data-live-search='true' class='selectpicker' title='Firma' data-style='select-with-transition'>
                           " . selectCompanies($company['idCompany']) . "
                        </select>
                        <input type='hidden' value='1' name='company[0][idCompanyType]'>
                    </div>
                </div>
            </div>
        </div>
        $contactsHtml
        <div class='row'>
            <div class='col-md-12'>
                <h3>1. zasmluvněný stupeň PD:</h3>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>date_range</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <label>Vyber 1. zasmluvněný stupeň PD
                        </label>
                        <select name='deadlines[0][idDeadlineType]' class='selectpicker form-control' data-style='select-with-transition'>
                            $deadlineTypeSelectOptionsHtml
                        </select>                                       
                        <input type='text' name='deadlines[0][value]' required class='form-control datetimepicker' value='$deadlineValue'>
                        <input type='text' name='deadlines[0][note]' placeholder='Poznámka' class='form-control' value='$deadlineNote'>
                    </div>
                </div>
            </div>
        </div>
        <div class='row float-right'>
            <div class='col'>
                <input type='submit' id='saveNoValidate' formnovalidate='formnovalidate' class='btn btn-danger btn-wd noEnterSubmit' value='Uložit jako koncept' name='finish'>
                <input type='submit' id='saveValidate' class='btn btn-danger btn-wd' value='Změna fáze do fáze v přípravě' name='finish'>
            </div>
        </div>
    </form>
    ";
    return $html;
}

/**
 * @return null
 */
function generateReadyPhaseSwitchForm($project, $idPhase)
{

    $ginisChecked = null;
    $athenaChecked = null;
    if ($project->baseInformation['ginisOrAthena'] == 'g') {
        $ginisChecked = 'checked';
        $athenaChecked = null;
    } elseif ($project->baseInformation['ginisOrAthena'] == 'a') {
        $ginisChecked = null;
        $athenaChecked = 'checked';
    }
    $cenyLabel = '';
    $deadlinePart = "";
    $toggleEvidence = 'checked';
    if ($project->baseInformation['dateEvidence'] === '0') {
        $toggleEvidence = '';
    }
    $deadlineHtml = deadlineFormFromTemplate(deadlineTemplatesForProject($project), $project, 0);
    $hidden = empty($deadlineHtml) ? "" : "hidden";

    if ($project->baseInformation['technologicalProjectType'] == 'normal') {
        $deadlinePart = "        <div class='row' id='deadlines'>
            <div class='col-md-12'>
                <button class='btn btn-primary float-right' type='button' id='newDeadline' data-toggle='modal' data-target='#addDeadlineModal' style='margin-top: 15px'><i class='fa fa-plus mr-1'></i> Přidat termín<div class='ripple-container'></div></button>
                <h3>Termíny:</h3>
            </div>
            <div id='noDeadline' class='col-12 $hidden'><h3>Stavba  zatím žádné termíny nemá. <a href='#' data-toggle='modal' data-target='#addDeadlineModal'>Zadejte první termín.</a></h3></div>
            $deadlineHtml
        </div>";
        $cenyLabel = " <div class='col-md-12'>
                <h3>Ceny do tenderu (VŘ)</h3>
            </div>";
        $priceTemplate = array(
            array(
                "idPriceType" => 11,
                "dph" => false
            ), array(
                "idPriceType" => 12,
                "dph" => false
            )
        );
        $prices = priceFormFromTemplate($priceTemplate, $project);

        $deadlineTemplates = array(
            array(
                'idDeadlineType' => 13,
                'class' => 'dateEvidence'
            )
        );
    }

    $objects = $project->getObjects();
    $objectsHtml = '';
    foreach ($objects as $key => $object) {
        $objectsHtml .= $object->htmlFormFilled($key, $idPhase);
    }


    $html = "
    <form method='post' action='' id='nextPhaseForm' class='form-horizontal phaseForm'>
        <div class='row'>
            $cenyLabel
            $prices
        </div>
        <div class='row'>
                        <div class='col-md-12'>
                            <h3>Zdroje financování</h3>
                        </div>
                        <div class='col-md-12 d-flex align-items-center'>
                            <div class='col-md-6'>
                                <div class='input-group form-control-lg'>
                                    <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>account_balance</i>
                                </span>
                                    </div>
                                    <div class='form-group col'>
                                        <div class='dropdown bootstrap-select show-tick dropup'>
                                        <label>Stavba</label>
                                            <select class='selectpicker' data-style='select-with-transition' required
                                                    name='idFinSource' title='Zdroj financování stavby' tabindex='-98'>
                                                ". selectFinancialSources($project->baseInformation['idFinSource'], false)."
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class='input-group form-control-lg'>
                                    <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>account_balance</i>
                                </span>
                                    </div>
                                    <div class='form-group col'>
                                        <div class='dropdown bootstrap-select show-tick dropup'>
                                             <label>PD</label>
                                            <select class='selectpicker' data-style='select-with-transition' required
                                                    name='idFinSourcePD' title='Zdroj financování PD'
                                                    tabindex='-98'>
                                                ".selectFinancialSources($project->baseInformation['idFinSourcePD'], true) ."
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
        <div class='row'>
            $objectsHtml
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <h3>Termín vložení - systém</h3>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>comment</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <label for='noteGinisOrAthena' class='bmd-label-floating'>Číslo systému Ginis nebo Athena
                        </label>   
                        <input type='text' class='form-control' required id='noteGinisOrAthena' name='noteGinisOrAthena' value='" . $project->baseInformation['noteGinisOrAthena'] . "'>
                    </div>
                </div>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>computer</i>
                        </span>
                    </div>
                    <div class='checkbox-radios col'>
                        <div class='form-check'>
                          <label class='form-check-label'>
                            <input class='form-check-input' type='radio' name='ginisOrAthena' $ginisChecked value='g'>GINIS
                            <span class='circle'>
                              <span class='check'></span>
                            </span>
                          </label>
                        </div>
                        <div class='form-check'>
                          <label class='form-check-label'>
                            <input class='form-check-input' type='radio' name='ginisOrAthena' $athenaChecked value='a'>Athéna
                            <span class='circle'>
                              <span class='check'></span>
                            </span>
                          </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        $deadlinePart
        <div class='row float-right'>
            <div class='col'>
                <input type='hidden' id='idPhase' name='idPhase' value=$idPhase class='form-control'>
                        <input type='hidden' value='" . $_SESSION['username'] . "' name='editor'>

                <input type='hidden' id='idProject' name='idProject' value='" . $project->getId() . "' class='form-control'>
                <input type='submit' id='saveNoValidate' formnovalidate='formnovalidate' class='btn btn-danger btn-wd' value='Uložit jako koncept' name='finish'>
                <input type='submit' id='saveValidate' class='btn btn-danger btn-wd' value='Změna fáze do fáze připraveno' name='finish'>
            </div>
        </div>
    </form>
    ";
    return $html;
}

/**
 * @return null
 */
function generateInProgressPhaseSwitchForm($project, $idPhase)
{
    if ($project->baseInformation['technologicalProjectType'] == 'normal') {
        $constructionOversight = $project->getCompanyByType(3) ?? null;
        $priceTemplate = array(
            array(
                "idPriceType" => 5,
                "dph" => false
            ), array(
                "idPriceType" => 7,
                "dph" => false
            )
        );
        $contactGeneralContractorTemplate = array(
            array(
                "idContactType" => 5,
                "disable" => false,
                "group" => 'contactGeneralContractor'
            ), array(
                "idContactType" => 6,
                "disable" => false,
                "group" => 'contactGeneralContractor'
            ), array(
                "idContactType" => 12,
                "disable" => true,
                "group" => 'contactGeneralContractor'
            )
        );
        $contactConstructionOversightTemplate = array(
            array(
                "idContactType" => 10,
                "disable" => false,
                "group" => 'contactConstructionOversight'
            ), array(
                "idContactType" => 9,
                "disable" => false,
                "group" => 'contactConstructionOversight'
            )
        );
        $tdsBozpPart = "        <div class='row'>
            <div class='col-md-12'>
                <h3>TDS + BOZP</h3>
            </div>
            <div class='col-md-6 col'>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>business</i>
                        </span>
                    </div>
                    <div class='form-group col'>
      
                        <label for='constructionOversightCompany' class='bmd-label-floating'>Firma
                            </label>
                            <input list='constructionOversightCompanyDataListInput' name='company[1][idCompany]' required class='form-control companyIdDatalist' autocomplete='off' value='$constructionOversight[idCompany]'>
                            <datalist class='DataList' id='constructionOversightCompanyDataList'>
                                " . selectCompanies($constructionOversight['idCompany']) . "
                            </datalist>
                        
                        
                        <input type='hidden' value='3' name='company[1][idCompanyType]'>
                    </div>
                </div>
            </div>
        </div>
";
    }
    if ($project->baseInformation['technologicalProjectType'] == 'lite') {
        $priceTemplate = array(
            array(
                "idPriceType" => 5,
                "dph" => false
            )
        );

        $contactGeneralContractorTemplate = array(
            array(
                "idContactType" => 5,
                "disable" => false,
                "group" => 'contactGeneralContractor'
            ), array(
                "idContactType" => 6,
                "disable" => false,
                "group" => 'contactGeneralContractor'
            ), array(
                "idContactType" => 12,
                "disable" => true,
                "group" => 'contactGeneralContractor'
            )
        );

    }


    $objects = $project->getObjects();
    $objectsHtml = '';
    foreach ($objects as $key => $object) {
        $objectsHtml .= $object->htmlFormFilled($key, $idPhase);
    }


    $deadlineTemplates = array(
        array(
            'idDeadlineType' => 12,
            'class' => 'dateSiteHandover'
        )
    );
    $selectedWeeks = ($project->baseInformation['constructionTimeWeeksOrMonths'] === "w") ? "selected" : "";
    $selectedMonths = ($project->baseInformation['constructionTimeWeeksOrMonths'] === "m") ? "selected" : "";
    $selectedDays = ($project->baseInformation['constructionTimeWeeksOrMonths'] === "d") ? "selected" : "";

    $deadlineHtml = deadlineFormFromTemplate($deadlineTemplates, $project, 0);
    $price = priceFormFromTemplate($priceTemplate, $project);
    $generalContractor = $project->getCompanyByType(2);

    $contactsGeneralContractorHtml = contactsFromTemplate($contactGeneralContractorTemplate, $project, 0);
    $contactsConstructionOversightHtml = isset($contactConstructionOversightTemplate) ? contactsFromTemplate($contactConstructionOversightTemplate, $project, count($contactGeneralContractorTemplate)) : null;
    $html = "
    <form method='post' id='nextPhaseForm' action='' class='form-horizontal phaseForm'>
        <div class='row'>
            <div class='col-md-12'>
                <h3>Skutečné ceny dle smlouvy</h3>
            </div>
            $price
        </div>
        <div class='row'>
        $objectsHtml
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <h3>Zhotovitel stavby</h3>
            </div>
            <div class='col-md-6'>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>business</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                    <label for='generalContractorCompany'>Zhotovitelská firma
                            <select id='generalContractorCompany' name='company[0][idCompany]' required class='select2  form-control'  title='Firma' >
                           " . selectCompanies($generalContractor['idCompany']) . "
                        </select>
                        </label>
                        <input type='hidden' value='2' name='company[0][idCompanyType]'>
                    </div>
                </div>
            </div>
        </div>
        $contactsGeneralContractorHtml
 $tdsBozpPart
        $contactsConstructionOversightHtml
        <div class='row'>
            <div class='col-md-12'>
                <h3>Doba realizace</h3>
            </div>
            $deadlineHtml
            <div class='col-md-6'>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                            <i class='material-icons'>timelapse</i>
                        </span>
                    </div>
                    <div class='form-group col bmd-form-group'>
                        <label class='bmd-label-floating'>
                            Doba realizace stavby (týdny/měsíce)
                        </label>
                        <input type='number' min='1' step='1' class='form-control' name='constructionTime' value='" . $project->baseInformation['constructionTime'] . "' required>
                    </div>
                    <div class='input-group-append' style='width: 100px;'>
                        <select name='constructionTimeWeeksOrMonths' class='selectpicker form-control' data-style='select-with-transition'>
                            <option value='w' $selectedWeeks>týdnů</option>
                            <option value='m' $selectedMonths>měsíců</option>
                            <option value='d' $selectedDays>dnů</option>

                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class='row float-right'>
            <div class='col'>
                <input type='hidden' id='idPhase' name='idPhase' value='$idPhase' class='form-control'>
                        <input type='hidden' value='" . $_SESSION['username'] . "' name='editor'>

                <input type='hidden' id='idProject' name='idProject' value='" . $project->getId() . "' class='form-control'>
                <input type='submit' id='saveNoValidate' formnovalidate='formnovalidate' class='btn btn-danger btn-wd' value='Uložit jako koncept' name='finish'>
                <input type='submit' id='saveValidate' class='btn btn-danger btn-wd' value='Změna fáze do fáze v realizaci' name='finish'>
            </div>
        </div>
    </form>
    ";
    return $html;
}

/**
 * @return null
 */
function generateDoneSwitchForm($project)
{
    $deadlineTemplates = array(
        array(
            'idDeadlineType' => 24,
            'class' => 'dateSiteHandover'
        )
    );
    $deadlineHtml = deadlineFormFromTemplate($deadlineTemplates, $project, 0);
    $warrantiesHtml = generateWarantiesInputs(getWarrantiesTypes());
    $passableChecked = ($project->baseInformation['passable'] == 1) ? 'checked' : '';

    $html = "
    <form method='post' id='nextPhaseForm' action='' class='form-horizontal phaseForm'>
        <div class='row'>
        <div class='col-md-12'>
            <p>
                <h4>Potvrzením tohoto formuláře přepnete projekt do finálního stavu zrealizováno. <br/><br/>Berte na vědomí, že projekt se uzamkne proti změnám v editaci. 
                <br/><b>ZKONTROLUJTE</b> tak dosud uložené informace v projektu a případně proveďte změny před změnou do finální fáze. V případě, že byla stavba zprůjezdněna, ale nedošlo k předání, zaškrtněte níže tlačítko Zprůjezdněno a uložte fázi jako koncept
                </h4>
            </p>
        </div>
        </div>
         <div class='row'>
         <div class='col-md-6'>
         <div class='col-md-12 d-flex align-items-center'>
            <div class='togglebutton'>
                <label>
                    <input type='checkbox' class='togglePrice' name='passable' $passableChecked id='passable'>
                    <span class='toggle'></span>
                    Zprůjezdněno
                </label>
            </div>
        </div>
        </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <h3>Předání dokončené stavby a záruky:</h3>
            </div>
            $deadlineHtml
            $warrantiesHtml
        </div>
        
       
        <div class='row float-right'>
            <div class='col'>
                <input type='hidden' id='idPhase' name='idPhase' value='1' class='form-control'>
                 <input type='hidden' value='" . $_SESSION['username'] . "' name='editor'>

                <input type='hidden' name='idProject' value='" . $project->getId() . "' class='form-control'>
                <input type='submit' id='saveNoValidate' formnovalidate='formnovalidate' class='btn btn-danger btn-wd' value='Uložit jako koncept' name='finish'>
                <input type='submit' id='saveValidate' class='btn btn-danger btn-wd' value='Změna fáze do fáze zrealizováno' name='finish'>
            </div>
        </div>
    </form>
    ";
    return $html;
}

function getWarrantiesTypes()
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `rangeWarrantiesTypes` ");
    $stmt->execute();
    return ($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function generateWarantiesInputs($warranties)
{
    $html = "<div class='col-md-6'>";
    foreach ($warranties as $warranty) {
        $html .= "<div class='input-group form-control-lg'><div class='form-group col'><label for='warranty" . $warranty['idWarrantyType'] . "'>" . $warranty['name'] . "</label><input id='warranty" . $warranty['idWarrantyType'] . "' type='number' name='" . $warranty['nameForPOST'] . "' class='form-control' placeholder='Délka záruky v měsících'></div></div>";
    }
    $html .= "</div>";
    return $html;
}

function getProjectPhase($idProject)
{
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare("SELECT idProject, idLocalProject, idPhase FROM viewProjectsActive WHERE idProject=:idProject");
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    return ($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function generateCurrentPhaseForm($project, $isPhasing = false)
{
    $html = '';
    if($project->baseInformation['technologicalProjectType'] == 'lite'){
        switch ($project->baseInformation['idPhase']) {
            case 6:
                $priceTemplate = array(
                    array(
                        "idPriceType" => 6,
                        "dph" => false,
                        "class" => ''
                    )
                );
                $html .= generateTopicPhaseForm($project, $priceTemplate, 6);
                break;
            case 5:
                $priceTemplate = array(
                    array(
                        "idPriceType" => 6,
                        "dph" => false,
                        "class" => ''
                    )
                );
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                break;
            case 4:
                if ($project->baseInformation['mergePricePDAD'] == 1) {
                    $priceTemplate = array(
                             array(
                            "idPriceType" => 6,
                            "dph" => false
                        )
                    );
                } else {
                    $priceTemplate = array(
                         array(
                            "idPriceType" => 6,
                            "dph" => false
                        )
                    );
                }

                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project, $isPhasing);
                break;
            case 3:
                if ($project->baseInformation['mergePricePDAD'] == 1) {
                    $priceTemplate = array(
                    );
                } else {
                    $priceTemplate = array(
                    );
                }
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project);
                $html .= generateReadyPhaseForm($project);
                break;
            case 2:
                    $priceTemplate = array(
                             array(
                            "idPriceType" => 5,
                            "dph" => false
                        )
                    );
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project);
                $html .= generateReadyPhaseForm($project);
                $html .= generateInPreparationForm($project);
                break;
            case 1:
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 5,
                            "dph" => false
                        ),
                    );
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project);
                $html .= generateReadyPhaseForm($project);
                $html .= generateInPreparationForm($project);
                break;
        }

    }
    else {
        switch ($project->baseInformation['idPhase']) {
            case 6:
                $priceTemplate = array(
                    array(
                        "idPriceType" => 6,
                        "dph" => false,
                        "class" => ''
                    ), array(
                        "idPriceType" => 8,
                        "dph" => false,
                        "class" => ''
                    ), array(
                        "idPriceType" => 4,
                        "dph" => false,
                        "class" => 'mergedPrice'
                    ), array(
                        "idPriceType" => 3,
                        "dph" => false,
                        "class" => 'mergedPrice'
                    ), array(
                        "idPriceType" => 13,
                        "dph" => false,
                        "class" => 'prePricePDAD'
                    )
                );
                $html .= generateTopicPhaseForm($project, $priceTemplate, 6);
                break;
            case 5:
                $priceTemplate = array(
                    array(
                        "idPriceType" => 6,
                        "dph" => false,
                        "class" => ''
                    ), array(
                        "idPriceType" => 8,
                        "dph" => false,
                        "class" => ''
                    ), array(
                        "idPriceType" => 4,
                        "dph" => false,
                        "class" => 'mergedPrice'
                    ), array(
                        "idPriceType" => 3,
                        "dph" => false,
                        "class" => 'mergedPrice'
                    ), array(
                        "idPriceType" => 13,
                        "dph" => false,
                        "class" => 'prePricePDAD'
                    )
                );
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                break;
            case 4:
                if ($project->baseInformation['mergePricePDAD'] == 1) {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 14,
                            "dph" => false
                        ), array(
                            "idPriceType" => 6,
                            "dph" => false
                        ), array(
                            "idPriceType" => 8,
                            "dph" => false
                        ),
                    );
                } else {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 2,
                            "dph" => false
                        ), array(
                            "idPriceType" => 6,
                            "dph" => false
                        ), array(
                            "idPriceType" => 1,
                            "dph" => false
                        ), array(
                            "idPriceType" => 8,
                            "dph" => false
                        ),
                    );
                }

                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project, $isPhasing);
                break;
            case 3:
                if ($project->baseInformation['mergePricePDAD'] == 1) {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 14,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 11,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 12,
                            "dph" => false
                        ),
                    );
                } else {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 2,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 11,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 1,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 12,
                            "dph" => false
                        ),
                    );
                }
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project);
                $html .= generateReadyPhaseForm($project);
                break;
            case 2:
                if ($project->baseInformation['mergePricePDAD'] == 1) {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 14,
                            "dph" => false
                        ), array(
                            "idPriceType" => 5,
                            "dph" => false
                        ), array(
                            "idPriceType" => 7,
                            "dph" => false
                        ),
                    );
                } else {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 2,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 5,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 1,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 7,
                            "dph" => false
                        )
                    );
                }
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project);
                $html .= generateReadyPhaseForm($project);
                $html .= generateInPreparationForm($project);
                break;
            case 1:
                if ($project->baseInformation['mergePricePDAD'] == 1) {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 14,
                            "dph" => false
                        ), array(
                            "idPriceType" => 5,
                            "dph" => false
                        ), array(
                            "idPriceType" => 7,
                            "dph" => false
                        ),
                    );
                } else {
                    $priceTemplate = array(
                        array(
                            "idPriceType" => 2,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 5,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 1,
                            "dph" => false
                        ),
                        array(
                            "idPriceType" => 7,
                            "dph" => false
                        )
                    );
                }
                $html .= generateIntentionPhaseForm($project, $priceTemplate, $isPhasing);
                $html .= generateInPreparationPhaseForm($project);
                $html .= generateReadyPhaseForm($project);
                $html .= generateInPreparationForm($project);
                break;
        }
    }
    return $html;

}

function generateTopicPhaseForm($project, $priceTemplate, $idPhase)
{
    //
    require_once __DIR__ . "/autoLoader.php";
    $areaSelects = '';
    foreach ($project->getArea() as $area) {
        $areaSelects .= "<div class='dropdown bootstrap-select show-tick dropup areaSelectWrap'>
                            <select class='selectArea' data-style='select-with-transition' required
                                    name='idArea[]' data-live-search='true' title='Vyberte okres'
                                    tabindex='-98'>
                                        " . selectArea($area['idArea']) . "
                            </select>
                        </div>";
    }


    $mergedPriceChecked = ($project->baseInformation['mergePricePDAD'] == 1) ? 'checked' : '';
    $mergedPriceChecbox =
        "
        <div class='col-md-12 d-flex align-items-center'>
            <div class='togglebutton'>
                <label>
                    <input type='checkbox' class='togglePrice' $mergedPriceChecked id='togglePrePricePDAD'>
                    <span class='toggle'></span>
                    AD součástí PD
                </label>
                <input type='hidden' value='1' name='mergePricePDAD'>
            </div>
        </div>";


    $prices = priceFormFromTemplate($priceTemplate, $project);

    $communicationBlock = '';

    foreach ($project->getCommunication() as $key => $communication) {
        if ($communication['idCommunicationType'] != 3) {
            $communicationBlock .= "
            <div class='communicationFormGroup'>
                <div class='input-group form-control-md'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>theaters</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <div class='dropdown bootstrap-select show-tick dropup'>
                            <select class='selectCommunication' data-style='select-with-transition'
                                    name='communication[$key][idCommunication]' required
                                    data-live-search='true' title='Vyberte komunikaci'
                                    tabindex='-98'>
                                " . selectRoads($communication['idCommunication'], '1,2') . "
                            </select>
                        </div>
                    </div>
                </div>
                <div class='input-group form-control-md'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                            <i class='material-icons'>navigation</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <label class='bmd-label-floating'>Staničení od</label>
                        <input type='number' step='any' class='form-control stationingFrom'
                               name='communication[$key][stationingFrom]' required value='" . $communication['stationingFrom'] . "'>
                    </div>
                    <div class='form-group col'>
                        <label class='bmd-label-floating'>Staničení do</label>
                        <input type='number' step='any' class='form-control stationingTo'
                               name='communication[$key][stationingTo]' required value='" . $communication['stationingTo'] . "'>
                    </div>
                </div>";

            $communicationBlock .= "
        <div class='input-group form-control-md'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>
                  <i class='material-icons'>gps_fixed</i>
                </span>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS E 1</label>
                <input type='text' id='gpsE_$key' class='form-control gpsE1'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsE1]' required value='" . $communication['gpsE1'] . "'>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS N 1</label>
                <input type='text' id='gpsN_$key' class='form-control gpsN1'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsN1]' required value='" . $communication['gpsN1'] . "'>
            </div>
        </div>
        <div class='input-group form-control-md'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>
                  <i class='material-icons'>gps_fixed</i>
                </span>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS E 2</label>
                <input type='text' id='gpsE2_$key' class='form-control gpsE2'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsE2]' required value='" . $communication['gpsE2'] . "'>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS N 2</label>
                <input type='text' id='gpsN2_$key' class='form-control gpsN2'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsN2]' required value='" . $communication['gpsN2'] . "'>
            </div>
        </div>
        <input type='hidden' id='allPoints_$key' class='form-control allPoints' name='communication[$key][allPoints]' value='" . $communication['allPoints'] . "'>
        <div class='m-4 btn btn-danger modalMapButton' data-toggle='modal'
             data-idOrderCommunication=$key data-num='0' data-target='#modalMapa'>
            Mapa
        </div>
    </div>";
        }
    }

    $objectBlock = '';
    foreach ($project->getObjects() as $key => $object) {
        $objectBlock .= $object->htmlFormFilled($key, $project->baseInformation['idPhase']);
    }

    $relationsBlock = "";
    foreach (getRlationTypes() as $key => $relationType) {
        $relationsBlock .= relationSelectsNew($relationType, $key, $project->getRealationsByType($relationType['idRelationType']));
    }

    $editor = selectEditors($project->baseInformation['editor']);
    $html = '';
    $html .= "  <input type='hidden' value='" . $project->getId() . "' name='idProject'>
                                <input type='hidden' value='$idPhase' name='idPhase'>

                <div class='row'>
                    <div class='col-md-12'>
                        <h4>
                            Základní definice projektu
                        </h4>
                    </div>
                    <div class='col-lg-6'>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>import_contacts</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <label for='name' class='bmd-label-floating'>Název projektu</label>
                                <input type='text' class='form-control' id='name' name='name' required aria-required='true' value='" . $project->baseInformation['name'] . "'>
                            </div>
                        </div>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>subject</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <label for='subject' class='label'>Předmět stavby</label><br>
                                <textarea id='subject' name='subject' rows=\"4\" cols=\"50\" ='subject' class='form-control'>" . unicodeToUtf8(stripTags($project->baseInformation['subject'])) . "</textarea>
                            </div>
                        </div>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>build</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select required class='selectpicker' data-style='select-with-transition' name='idProjectType' id='idProjectType' data-live-search='true' title='Druh stavby' tabindex='-98'>
                                    " . selectProjectTypes($project->baseInformation['idProjectType']) . "
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class='input-group form-control-lg'>
                             <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>build</i>
                                </span>
                            </div>
                             <div class='form-group col pl-0'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select required class='selectpicker' data-style='select-with-transition' name='idProjectSubtype' id='idProjectSubtype' data-live-search='true' title='Specifikace druhu stavby' tabindex='-98'>
                                        " . selectProjectSubtypes($project->baseInformation['idProjectType'], $project->baseInformation['idProjectSubtype']) . "
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-6'>
                        $relationsBlock
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h3>Kontaktní osoba KSÚS</h3>
                    </div>
                    <div class='col-lg-6'>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>face</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select required class='selectpicker' data-style='select-with-transition' name='editor' data-live-search='true' title='Vyberte dozor'>
                                        " . $editor . "
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h3>Lokalizace</h3>
                    </div>
                    <div class='col-md-6'>
                        <div class='input-group form-control-md'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                  <i class='material-icons'>location_city</i>
                                </span>
                            </div>
                            <div class='form-group col' id='areaForm'>
                                <span>Lokace</span>
                                $areaSelects
                            </div>
                        </div>
                        <div class='text-center'>
                            <i id='addArea' class='material-icons pointer active'>add</i>
                            <i id='removeArea' class='material-icons pointer not-active'>remove</i>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='communicationWrapper'>
                            $communicationBlock
                        </div>
                        <div class='text-center'>
                            <i id='addCommunication' class='material-icons active'>add</i>
                            <i id='removeCommunication' class='material-icons not-active'>remove</i>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h4>
                            Sdružené objekty
                        </h4>
                    </div>
                    <div class='col-md-6'>
                        <div class='input-group form-control-md'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                    <i class='material-icons'>gps_fixed</i>
                                </span>
                            </div>
                            <div class='form-group col'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select disabled class='selectObject selectpicker'
                                            data-style='select-with-transition' id='objectSelect'
                                            name='selectObject' data-live-search='true' title='Vyberte typ objektu'
                                            tabindex='-98'>
                                        <?php echo selectObjects(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-6 d-flex align-items-center'>
                        <i id='addObject' class='material-icons not-active'>add</i>
                    </div>
                </div>
                <div class='row' id='objectWrapper'>
                    $objectBlock
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h3>Finance</h3>
                    </div>
                    $mergedPriceChecbox
                    $prices
                </div>

                ";

    return $html;
}


function generateIntentionPhaseForm($project, $priceTemplate, $isPhasing = false)
{
    require_once __DIR__ . "/autoLoader.php";
    $areaSelects = '';
    foreach ($project->getArea() as $area) {
        $areaSelects .= "<div class='dropdown bootstrap-select show-tick dropup areaSelectWrap'>
                            <select class='selectArea' data-style='select-with-transition' required
                                    name='idArea[]' data-live-search='true' title='Vyberte okres'
                                    tabindex='-98'>
                                        " . selectArea($area['idArea']) . "
                            </select>
                        </div>";
    }


    $mergedPriceChecbox = '';
    if (($project->baseInformation['idPhase'] == 5 or $isPhasing) && array_search('13', array_column($priceTemplate, 'idPriceType'))) {
        $mergedPriceChecked = ($project->baseInformation['mergePricePDAD'] == 1) ? 'checked' : '';
        $mergedPriceChecbox =
            "
        <div class='col-md-12 d-flex align-items-center'>
            <div class='togglebutton'>
                <label>
                    <input type='checkbox' class='togglePrice' $mergedPriceChecked id='togglePrePricePDAD'>
                    <span class='toggle'></span>
                    AD součástí PD
                </label>
                <input type='hidden' value='1' name='mergePricePDAD'>
            </div>
        </div>";
    }

    if ($isPhasing) {
        $priceTemplate = array(

            array(
                "idPriceType" => 6,
                "dph" => false,
                "class" => ''
            )
        , array(
                "idPriceType" => 8,
                "dph" => false,
                "class" => ''
            ), array(
                "idPriceType" => 4,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 3,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 13,
                "dph" => false,
                "class" => 'prePricePDAD'
            ),
            array(
                "idPriceType" => 1,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 14,
                "dph" => false,
                "class" => 'prePricePDAD'
            )
        , array(
                "idPriceType" => 2,
                "dph" => false,
                "class" => 'mergedPrice'
            )
        );
        $prices = '';
        foreach ($priceTemplate as $key => $template) {
            $price = new Price\Price(null, $template['idPriceType'], getVat());
            $price->getLabel(false);
            $prices .= "<div class='col-md-6'>{$price->formRepresenation($key, $template['dph'], $template['class'])}</div>";
        }
    } else {
        $prices = priceFormFromTemplate($priceTemplate, $project);

    }

    $communicationBlock = '';
    if (!$isPhasing) {
        foreach ($project->getCommunication() as $key => $communication) {
            if ($communication['idCommunicationType'] != 3) {
                $communicationBlock .= "
            <div class='communicationFormGroup'>
                <div class='input-group form-control-md'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>theaters</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <div class='dropdown bootstrap-select show-tick dropup'>
                            <select class='selectCommunication' data-style='select-with-transition'
                                    name='communication[$key][idCommunication]' required
                                    data-live-search='true' title='Vyberte komunikaci'
                                    tabindex='-98'>
                                " . selectRoads($communication['idCommunication'], '1,2') . "
                            </select>
                        </div>
                    </div>
                </div>
                <div class='input-group form-control-md'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                            <i class='material-icons'>navigation</i>
                        </span>
                    </div>
                    <div class='form-group col'>
                        <label class='bmd-label-floating'>Staničení od</label>
                        <input type='number' step='any' class='form-control stationingFrom'
                               name='communication[$key][stationingFrom]' required value='" . $communication['stationingFrom'] . "'>
                    </div>
                    <div class='form-group col'>
                        <label class='bmd-label-floating'>Staničení do</label>
                        <input type='number' step='any' class='form-control stationingTo'
                               name='communication[$key][stationingTo]' required value='" . $communication['stationingTo'] . "'>
                    </div>
                </div>";
            } else {
                $communicationBlock .= "
            <div class='communicationFormGroup'>
                <div class='input-group form-control-md'>
                    <div class='input-group-prepend'>
                    <span class='input-group-text'>
                        <i class='material-icons'>theaters</i>
                    </span>
                    </div>
                    <div class='form-group col'>
                        <div class='dropdown bootstrap-select show-tick dropup'>
                            <label for='assignments' class='bmd-label-floating'>Název cyklostezky</label>
                            <input type='text' rows='5' class='form-control' id='' name='communication[$key][comment]' value='" . $communication['comment'] . "'></textarea>
                        </div>
                    </div>
                </div>
                <div class='input-group form-control-md'>
                    <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>theaters</i>
                    </span>
                    </div>
                    <div class='form-group col'>
                        <div class='dropdown bootstrap-select show-tick dropup'>
                            <select class='selectCommunication' data-style='select-with-transition' name='communication[$key][idCommunication]' required data-live-search='true' title='Vyberte páteřní cyklostezku' tabindex='-98'>
                                " . selectRoads($communication['idCommunication'], "3") . "
                            </select>
                        </div>
                    </div>
                </div>
            </div>";
            }
            $communicationBlock .= "
        <div class='input-group form-control-md'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>
                  <i class='material-icons'>gps_fixed</i>
                </span>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS E 1</label>
                <input type='text' id='gpsE_$key' class='form-control gpsE1'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsE1]' required value='" . $communication['gpsE1'] . "'>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS N 1</label>
                <input type='text' id='gpsN_$key' class='form-control gpsN1'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsN1]' required value='" . $communication['gpsN1'] . "'>
            </div>
        </div>
        <div class='input-group form-control-md'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>
                  <i class='material-icons'>gps_fixed</i>
                </span>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS E 2</label>
                <input type='text' id='gpsE2_$key' class='form-control gpsE2'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsE2]' required value='" . $communication['gpsE2'] . "'>
            </div>
            <div class='form-group col'>
                <label class='bmd-label-floating'>GPS N 2</label>
                <input type='text' id='gpsN2_$key' class='form-control gpsN2'
                       pattern='[0-9]+([\.,][0-9]+)?'
                       name='communication[$key][gpsN2]' required value='" . $communication['gpsN2'] . "'>
            </div>
        </div>
        <input type='hidden' id='allPoints_$key' class='form-control allPoints' name='communication[$key][allPoints]' value='" . $communication['allPoints'] . "'>
        <div class='m-4 btn btn-danger modalMapButton' data-toggle='modal'
             data-idOrderCommunication=$key data-num='0' data-target='#modalMapa'>
            Mapa
        </div>
    </div>";
        }
    }
    if ($isPhasing) {
        $communicationBlock = "<div class='communicationFormGroup'>
    <div class='input-group form-control-md'>
        <div class='input-group-prepend'>
            <span class='input-group-text'>
              <i class='material-icons'>theaters</i>
            </span>
        </div>
        <div class='form-group col'>
            <div class='dropdown bootstrap-select show-tick dropup'>
                <select class='selectCommunication' data-style='select-with-transition'
                        name='communication[0][idCommunication]' required
                        data-live-search='true' title='Vyberte komunikaci'
                        tabindex='-98'>
                    " . selectRoads(null, '1,2') . "
                </select>
            </div>
        </div>
    </div>
    <div class='input-group form-control-md'>
        <div class='input-group-prepend'>
            <span class='input-group-text'>
              <i class='material-icons'>navigation</i>
            </span>
        </div>
        <div class='form-group col'>
            <label for='stationingFrom' class='bmd-label-floating'>Staničení od</label>
            <input step='any' type='number' class='form-control stationingFrom'
                   name='communication[0][stationingFrom]' required>
        </div>
        <div class='form-group col'>
            <label for='stationingTo' class='bmd-label-floating'>Staničení do</label>
            <input step='any' type='number' class='form-control stationingTo'
                   name='communication[0][stationingTo]' required>
        </div>
    </div>
    <div class='input-group form-control-md'>
        <div class='input-group-prepend'>
                            <span class='input-group-text'>
                              <i class='material-icons'>gps_fixed</i>
                            </span>
        </div>
        <div class='form-group col'>
            <label class='bmd-label-floating'>GPS E 1</label>
            <input type='number' id='gpsE_0' class='form-control gpsE1'
                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                   name='communication[0][gpsE1]' required>
        </div>
        <div class='form-group col'>
            <label class='bmd-label-floating'>GPS N 1</label>
            <input type='number' id='gpsN_0' class='form-control gpsN1'
                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                   name='communication[0][gpsN1]' required>
        </div>
    </div>
    <div class='input-group form-control-md'>
        <div class='input-group-prepend'>
                            <span class='input-group-text'>
                              <i class='material-icons'>gps_fixed</i>
                            </span>
        </div>
        <div class='form-group col'>
            <label class='bmd-label-floating'>GPS E 2</label>
            <input type='number' id='gpsE2_0' class='form-control gpsE2'
                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                   name='communication[0][gpsE2]' required>
        </div>
        <div class='form-group col'>
            <label class='bmd-label-floating'>GPS N 2</label>
            <input type='number' id='gpsN2_0' class='form-control gpsN2'
                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                   name='communication[0][gpsN2]' required>
        </div>
    </div>
    <input type='hidden' id='allPoints_0' class='form-control allPoints' name='communication[0][allPoints]' value=''>
    <div class='m-4 btn btn-danger modalMapButton' data-toggle='modal'
         data-idOrderCommunication=0 data-num='0' data-target='#modalMapa'>
        Mapa
    </div>
</div>

";
    }

    $objectBlock = '';
    foreach ($project->getObjects() as $key => $object) {
        $objectBlock .= $object->htmlFormFilled($key, $project->baseInformation['idPhase']);
    }

    $relationsBlock = "";
    foreach (getRlationTypes() as $key => $relationType) {
        $relationsBlock .= relationSelectsNew($relationType, $key, $project->getRealationsByType($relationType['idRelationType']));
    }

    // 1. zasmluvněný stupeň PD - pokud existuje, tak vyplnit jinak nechat prázný jeden deadline box se selectem termínů z dostupných termínů pro fázi 5
    $projectDeadline = $isPhasing ? "" : $project->getDeadlines();
    $deadlineType = empty($projectDeadline) ? 0 : $projectDeadline[0]['idDeadlineType'];
    $deadlineValue = empty($projectDeadline) ? "" : date("d-m-Y", strtotime($projectDeadline[0]['value']));
    $deadlineNote = empty($projectDeadline) ? "" : $projectDeadline[0]['note'];
    $deadlineTypeSelectOptionsHtml = "";
    $deadlineTypesAvailable = selectDeadlineTypesAvailableOnlyInPhase(5);
    foreach ($deadlineTypesAvailable as $deadlineTypeAvailable) {
        $selected = ($deadlineTypeAvailable['idDeadlineType'] == $deadlineType) ? "selected" : "";
        $deadlineTypeSelectOptionsHtml .= "<option value='$deadlineTypeAvailable[idDeadlineType]' $selected>$deadlineTypeAvailable[name]</option>";
    }
    $editor = $isPhasing ? selectEditors($_SESSION['username']) : selectEditors($project->baseInformation['editor']);
    $financePart = !empty($prices) ? "  <div class='row'>
                    <div class='col-md-12'>
                        <h3>Finance</h3>
                    </div>
                    $mergedPriceChecbox
                    $prices
                </div>" : "";
    $html = '';
    $html .= "  <input type='hidden' value='" . $project->getId() . "' name='idProject'>
                <input type='hidden' value='" . $project->baseInformation['idPhase'] . "' name='idPhase'>
                <div class='row'>
                    <div class='col-md-12'>
                        <h4>
                            Základní definice projektu
                        </h4>
                    </div>
                    <div class='col-lg-6'>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>import_contacts</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <label for='name' class='bmd-label-floating'>Název projektu</label>
                                <input type='text' class='form-control' id='name' name='name' required aria-required='true' value='" . $project->baseInformation['name'] . "'>
                            </div>
                        </div>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>subject</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <label for='subject' class='label'>Předmět stavby</label><br>
                                <textarea id='subject' name='subject' rows=\"4\" cols=\"50\" ='subject' class='form-control'>" . unicodeToUtf8(stripTags($project->baseInformation['subject'])) . "</textarea>
                            </div>
                        </div>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>build</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select required class='selectpicker' data-style='select-with-transition' name='idProjectType' id='idProjectType' data-live-search='true' title='Druh stavby' tabindex='-98'>
                                    " . selectProjectTypes($project->baseInformation['idProjectType']) . "
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class='input-group form-control-lg'>
                             <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>build</i>
                                </span>
                            </div>
                             <div class='form-group col pl-0'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select required class='selectpicker' data-style='select-with-transition' name='idProjectSubtype' id='idProjectSubtype' data-live-search='true' title='Specifikace druhu stavby' tabindex='-98'>
                                        " . selectProjectSubtypes($project->baseInformation['idProjectType'], $project->baseInformation['idProjectSubtype']) . "
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-6'>
                        $relationsBlock
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h3>Kontaktní osoba KSÚS</h3>
                    </div>
                    <div class='col-lg-6'>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>face</i>
                                </span>
                            </div>
                            <div class='form-group col pl-0 bmd-form-group'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select required class='selectpicker' data-style='select-with-transition' name='editor' data-live-search='true' title='Vyberte dozor'>
                                        " . $editor . "
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h3>Lokalizace</h3>
                    </div>
                    <div class='col-md-6'>
                        <div class='input-group form-control-md'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                  <i class='material-icons'>location_city</i>
                                </span>
                            </div>
                            <div class='form-group col' id='areaForm'>
                                <span>Lokace</span>
                                $areaSelects
                            </div>
                        </div>
                        <div class='text-center'>
                            <i id='addArea' class='material-icons pointer active'>add</i>
                            <i id='removeArea' class='material-icons pointer not-active'>remove</i>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='communicationWrapper'>
                            $communicationBlock
                        </div>
                        <div class='text-center'>
                            <i id='addCommunication' class='material-icons active'>add</i>
                            <i id='removeCommunication' class='material-icons not-active'>remove</i>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <h4>
                            Sdružené objekty
                        </h4>
                    </div>
                    <div class='col-md-6'>
                        <div class='input-group form-control-md'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                    <i class='material-icons'>gps_fixed</i>
                                </span>
                            </div>
                            <div class='form-group col'>
                                <div class='dropdown bootstrap-select show-tick dropup'>
                                    <select disabled class='selectObject selectpicker'
                                            data-style='select-with-transition' id='objectSelect'
                                            name='selectObject' data-live-search='true' title='Vyberte typ objektu'
                                            tabindex='-98'>
                                        <?php echo selectObjects(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-6 d-flex align-items-center'>
                        <i id='addObject' class='material-icons not-active'>add</i>
                    </div>
                </div>
                <div class='row' id='objectWrapper'>
                    $objectBlock
                </div>
                $financePart
              <!--  <div class='row'>
                    <div class='col-md-12'>
                        <h3>1. zasmluvněný stupeň PD</h3>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                  <i class='material-icons'>date_range</i>
                                </span>
                            </div>
                            <div class='form-group col'>
                                <label>Vyber 1. zasmluvněný stupeň PD
                                </label>
                                <select name='deadlines[0][idDeadlineType]' class='selectpicker form-control' data-style='select-with-transition'>
                                    $deadlineTypeSelectOptionsHtml
                                </select>                                       
                                <input type='text' name='deadlines[0][value]' required class='form-control datetimepicker' value='$deadlineValue'>
                                <input type='text' name='deadlines[0][note]' placeholder='Poznámka' class='form-control' value='$deadlineNote'>
                            </div>
                        </div>
                    </div>
                </div>-->
                ";

    return $html;
}

function generateInPreparationPhaseForm($project, $isPhasing = false)
{
    if($project->baseInformation['technologicalProjectType'] == 'lite'){
        $contactTemplate = [];
    }
    else {
        $contactTemplate = array(
            array(
                "idContactType" => 1,
                "disable" => false,
                "group" => 'contactProjectContractor'
            ), array(
                "idContactType" => 2,
                "disable" => false,
                "group" => 'contactProjectContractor'
            ), array(
                "idContactType" => 3,
                "disable" => false,
                "group" => 'contactProjectContractor'
            ), array(
                "idContactType" => 4,
                "disable" => false,
                "group" => 'contactProjectContractor'
            )
        );
    }

    $company = $project->getCompanyByType(1);
    $contactsHtml  = contactsFromTemplate($contactTemplate, $project, 0);
    $zhotovitelProjektuPart = !empty($contactTemplate) ? "<div class='row'>
                    <div class='col-md-12'>
                        <h3>Zhotovitel projektu</h3>
                    </div>
                    <div class='col-md-6'>
                        <div class='input-group form-control-lg'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                  <i class='material-icons'>business</i>
                                
                                </span>
                            </div>
                            <div class='form-group col'>
                                <select id='contractorProjectCompany' required name='company[0][idCompany]' data-live-search='true' class='selectpicker' title='Firma' data-style='select-with-transition'>
                                   " . selectCompanies($company['idCompany']) . "
                                </select>
                                <input type='hidden' value='1' name='company[0][idCompanyType]'>
                            </div>
                        </div>
                    </div>
                </div>
                    $contactsHtml" : "";

    $deadlineHtml = $isPhasing ? "" : deadlineFormFromTemplate(deadlineTemplatesForProject($project), $project, 0);

    $hidden = empty($deadlineHtml) ? "" : "hidden";

    $html = '';
    $html .= "
                <input type='hidden' value='" . $project->getId() . "' name='idProject'>
                <input type='hidden' value='" . $project->baseInformation['idPhase'] . "' name='idPhase'>
                    $zhotovitelProjektuPart
                <div class='row'>
                    <div class='col-md-12'>
                        <button class='btn btn-primary float-right' type='button' id='newDeadline' data-toggle='modal' data-target='#addDeadlineModal' style='margin-top: 15px'><i class='fa fa-plus mr-1'></i> Přidat termín<div class='ripple-container'></div></button>
                        <h3>Termíny:</h3>
                    </div>
                </div>
                <div class='row' id='deadlines'>
                    <div id='noDeadline' class='col-12 $hidden'><h3>Stavba  zatím žádné termíny nemá. <button type='button' data-toggle='modal' data-target='#addDeadlineModal'>Zadejte první termín.</button></h3></div>
                    $deadlineHtml
                </div>";

    return $html;
}

function generateReadyPhaseForm($project)
{
    $ginisChecked = null;
    $athenaChecked = null;
    if ($project->baseInformation['ginisOrAthena'] == 'g') {
        $ginisChecked = 'checked';
        $athenaChecked = null;
    } elseif ($project->baseInformation['ginisOrAthena'] == 'a') {
        $ginisChecked = null;
        $athenaChecked = 'checked';
    }
    $toggleEvidence = 'checked';
    if ($project->baseInformation['dateEvidence'] === '0') {
        $toggleEvidence = '';
    }

    $deadlineTemplate = array(
        'idDeadlineType' => 13,
        'class' => 'dateEvidence'
    );

    $deadline = $project->getDeadlineByType($deadlineTemplate['idDeadlineType']);
    $deadlineValue = ($deadline['value'] != null) ? date("d-m-Y", strtotime($deadline['value'])) : '';
    $html = "
    <div class='row'>
        <div class='col-md-6'>
            <h3>Zdroj financování</h3>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>account_balance</i>
                    </span>
                </div>
                <div class='form-group col'>
                    <div class='dropdown bootstrap-select show-tick dropup'>
                        <select class='selectpicker' data-style='select-with-transition' required name='idFinSource' title='Vyberte druh financování' tabindex='-98'>
                           " . selectFinancialSources($project->baseInformation['idFinSource']) . "
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <h3>Termín vložení - systém</h3>
        </div>
        <div class='col-md-6'>
            <h3>Termín doložení</h3>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>computer</i>
                    </span>
                </div>
                <div class='checkbox-radios col'>
                    <div class='form-check'>
                      <label class='form-check-label'>
                        <input class='form-check-input' type='radio' name='ginisOrAthena' $ginisChecked value='g'>GINIS
                        <span class='circle'>
                          <span class='check'></span>
                        </span>
                      </label>
                    </div>
                    <div class='form-check'>
                      <label class='form-check-label'>
                        <input class='form-check-input' type='radio' name='ginisOrAthena' $athenaChecked value='a'>Athéna
                        <span class='circle'>
                          <span class='check'></span>
                        </span>
                      </label>
                    </div>
                </div>
            </div>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>comment</i>
                    </span>
                </div>
                <div class='form-group col'>
                    <label for='noteGinisOrAthena' class='bmd-label-floating'>Číslo systému Ginis nebo Athena
                    </label>   
                    <input type='text' class='form-control' required id='noteGinisOrAthena' name='noteGinisOrAthena' value='" . $project->baseInformation['noteGinisOrAthena'] . "'>
                </div>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>access_time</i>
                    </span>
                </div>
                <div class='togglebutton col'>
                    <label>
                    <input type='checkbox' name='dateEvidenceToggle' value='1' id='toggleEvidence' $toggleEvidence>
                        <span class='toggle'></span>Dokládá se
                    </label>
                    <input type='hidden' name='dateEvidence' value='1'>
                </div>
            </div>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>date_range</i>
                    </span>
                </div>
                <div class='form-group col'>
                    <label>$deadline[deadlineTypeName]
                    </label>                                       
                    <input type='text' name='deadlines[11][value]' required class='form-control datetimepicker " . $deadlineTemplate['class'] . "' value=$deadlineValue>
                    <input type='hidden' name='deadlines[11][idDeadlineType]' value='" . $deadline['idDeadlineType'] . "'>
                </div>
            </div>
        </div>
    </div>";
    return $html;
}

function generateInPreparationForm($project)
{
    $contactTemplate1 = array(
        array(
            "idContactType" => 5,
            "disable" => false,
            "group" => 'contactGeneralContractor'
        ), array(
            "idContactType" => 6,
            "disable" => false,
            "group" => 'contactGeneralContractor'
        ), array(
            "idContactType" => 12,
            "disable" => true,
            "group" => 'contactGeneralContractor'
        )
    );

    if($project->baseInformation['technologicalProjectType'] == 'lite'){
        $contactTemplate2 = [];
    }
    else {
        $contactTemplate2 = array(
            array(
                "idContactType" => 10,
                "disable" => false,
                "group" => 'contactConstructionOversight'
            ), array(
                "idContactType" => 9,
                "disable" => false,
                "group" => 'contactConstructionOversight'
            )
        );
    }

    $deadlineTemplates = array(
        array(
            'idDeadlineType' => 12,
            'class' => 'dateSiteHandover'
        )
    );
    $selectedWeeks = ($project->baseInformation['constructionTimeWeeksOrMonths'] === "w") ? "selected" : "";
    $selectedMonths = ($project->baseInformation['constructionTimeWeeksOrMonths'] === "m") ? "selected" : "";
    $selectedDays = ($project->baseInformation['constructionTimeWeeksOrMonths'] === "d") ? "selected" : "";

    $deadlineHtml = deadlineFormFromTemplate($deadlineTemplates, $project, 12);

    $company1 = $project->getCompanyByType(2);
    $contactsHtml1 = contactsFromTemplate($contactTemplate1, $project, 4);
    $company2 = $project->getCompanyByType(3);
    $contactsHtml2 = contactsFromTemplate($contactTemplate2, $project, 7);
    $tdsBozpPart = !empty($contactTemplate2) ? "<div class='row'>
        <div class='col-md-12'>
            <h3>TDS + BOZP</h3>
        </div>
        <div class='col-md-6'>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>business</i>
                    </span>
                </div>
                <div class='form-group col'>
                    <select id='constructionOversightCompany' required name='company[2][idCompany]' data-live-search='true' class='selectpicker' title='Firma' data-style='select-with-transition'>
                       " . selectCompanies($company2['idCompany']) . "
                    </select>
                    <input type='hidden' value='3' name='company[2][idCompanyType]'>
                </div>
            </div>
        </div>
    </div>
    $contactsHtml2" : "";
    $html =
        "
    <div class='row'>
        <div class='col-md-12'>
            <h3>Zhotovitel stavby</h3>
        </div>
        <div class='col-md-6'>
            <div class='input-group form-control-lg'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'>
                      <i class='material-icons'>business</i>
                    </span>
                </div>
                <div class='form-group col'>
                    <select id='generalContractorCompany' required name='company[1][idCompany]' data-live-search='true' class='selectpicker' title='Firma' data-style='select-with-transition'>
                       " . selectCompanies($company1['idCompany']) . "
                    </select>
                    <input type='hidden' value='2' name='company[1][idCompanyType]'>
                </div>
            </div>
        </div>
    </div>
    $contactsHtml1
    $tdsBozpPart
    <div class='row'>
        <div class='col-md-12'>
            <h3>Doba realizace</h3>
        </div>
        $deadlineHtml
        <div class='col-md-6'>
                <div class='input-group form-control-lg'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                            <i class='material-icons'>timelapse</i>
                        </span>
                    </div>
                    <div class='form-group col bmd-form-group'>
                        <label class='bmd-label-floating'>
                            Doba realizace stavby (týdny/měsíce)
                        </label>
                        <input type='number' min='1' step='1' class='form-control' name='constructionTime' value='" . $project->baseInformation['constructionTime'] . "' required>
                    </div>
                    <div class='input-group-append' style='width: 100px;'>
                        <select name='constructionTimeWeeksOrMonths' class='selectpicker form-control' data-style='select-with-transition'>
                            <option value='w' $selectedWeeks>týdnů</option>
                            <option value='m' $selectedMonths>měsíců</option>
                            <option value='d' $selectedDays>dnů</option>

                        </select>
                    </div>
                </div>
            </div>
    </div>";
    return $html;
}

function countProjectChanges()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (isset($_SESSION['notify'])) {
        $notifyDateTime = $_SESSION['notify'];

        $stmt = $dbh->getDbLink()->prepare("SELECT COUNT(name) FROM `projects` WHERE created > '$notifyDateTime'");

    } else {
        $stmt = $dbh->getDbLink()->prepare('SELECT COUNT(name) FROM `projects` WHERE created > (SELECT MAX(loginTime) FROM logins WHERE username=:user AND loginTime NOT IN (SELECT MAX(loginTime) FROM logins WHERE username=:user))');
    }
    //$stmt = $dbh->getDbLink()->prepare('SELECT COUNT(name) FROM `projects` WHERE created > (SELECT MAX(datum) FROM prihlasovani WHERE username=:user AND datum NOT IN (SELECT MAX(datum) FROM prihlasovani WHERE username=:user))');
    $stmt->bindParam(':user', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $counter = $result[0]['COUNT(name)'];
    return $counter;
}

function selectProjectChanges()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (isset($_SESSION['notify'])) {
        $notifyDateTime = $_SESSION['notify'];

        $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `projects` WHERE created > '$notifyDateTime' ORDER BY idLocalProject DESC");

    } else {
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM `projects` WHERE created > (SELECT MAX(loginTime) FROM logins WHERE username=:user AND loginTime NOT IN (SELECT MAX(loginTime) FROM logins WHERE username=:user)) ORDER BY idLocalProject DESC');
    }
    $stmt->bindParam(':user', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function showProjectChanges()
{
    $zmeny = getArrActionsLogByIntervalNonViewed();
    $html = "";
    foreach ($zmeny as $zmena) {
        $html .= "<tr>";
        $html .= "<td>" . $zmena['created'] . "</td><td>" . $zmena['projectName'] . " (ID " . $zmena['idProject'] . ")</td><td>" . $zmena['actionName'] . "</td><td><button class=\"float-right btn btn-sm btn-rose notification-exit\" notification-id='" . $zmena['idAction'] . "' destination=\"" . $zmena['idProject'] . "\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID " . $zmena['idProject'] . "\"><i class=\"fa fa-sign-in-alt\"></i><div class=\"ripple-container\"></div></button><button class=\"float-right btn btn-sm btn-secondary notification-hide\" notification-id='" . $zmena['idAction'] . "' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Skrýt toto upozornění\"><i class=\"fa fa-eye-slash\"></i><div class=\"ripple-container\"></div></button></td>";
        $html .= "</tr>";
    }
    if ($html == "") {
        $html .= "<tr><td></td><td><h3 class='text-center'>Žádné nové změny v projektech</h3></td><td></td><td></td></tr>";
    }
    echo $html;
}

function showProjectChangesForReport()
{
    $zmeny = getArrActionsLogByLimitForUsersProjects(20);
    $html = "";
    foreach ($zmeny as $zmena) {
        $html .= "<tr>";
        $html .= "<td>" . $zmena['created'] . "</td><td>" . $zmena['projectName'] . " (ID " . $zmena['idProject'] . ")</td><td>" . $zmena['actionName'] . "</td>";
        $html .= "</tr>";
    }
    if ($html == "") {
        $html .= "<tr><td></td><td><h3 class='text-center'>Žádné nové změny v projektech</h3></td><td></td><td></td></tr>";
    }
    echo $html;
}

function projectChangesNotification()
{
    $counter = count(getArrActionsLogByIntervalNonViewed(7));
    if ($counter > 0) {
        echo "<span class=\"notification\" id='notificationNumber'>$counter</span>";
    }
}

function projectChangesNotificationText()
{
    $counter = count(getArrActionsLogByIntervalNonViewed(7));
    echo "" . changesCzech($counter) . " na stavbách.";
}

function changesCzech($counter)
{
    if ($counter == 0) {
        return "Nepřibyla žádná změna";
    } elseif ($counter == 1) {
        return "Přibyla 1 změna";
    } elseif ($counter < 5) {
        return "Přibyly " . $counter . " změny";
    } else {
        return "Přibylo " . $counter . " změn";
    }
}

function getSelectedFilters()
{
    if (isset($_GET['idProject'])) {
        $_SESSION['selected_idProject'] = $_GET['idProject'];
    } else {
        unset($_SESSION['selected_idProject']);
    }
    if (isset($_GET['idProjectType'])) {
        $_SESSION['selected_idProjectType'] = $_GET['idProjectType'];
    } else {
        unset($_SESSION['selected_idProjectType']);
    }
    if (isset($_GET['idComunication'])) {
        $_SESSION['selected_idComunication'] = $_GET['idComunication'];
    } else {
        unset($_SESSION['selected_idComunication']);
    }
    if (isset($_GET['idArea'])) {
        $_SESSION['selected_idArea'] = $_GET['idArea'];
    } else {
        unset($_SESSION['selected_idArea']);
    }

}

function countProjectTermsInNextWeek()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if ((isset($_SESSION['jenMojeProjekty'])) && ($_SESSION['jenMojeProjekty'] == 1)) {
        $stmt = $dbh->getDbLink()->prepare('SELECT DISTINCT viewProjectsActive.idProject FROM `viewProjectsActive` JOIN deadlines ON viewProjectsActive.idProject=deadlines.idProject JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE viewProjectsActive.idProject IN (SELECT idProject FROM `deadlines`  WHERE `value` >= NOW() AND `value` <= NOW() + INTERVAL 30 DAY) AND (editor=:user OR author=:user)');
        $stmt->bindParam(':user', $_SESSION['username'], PDO::PARAM_STR);
    } else {
        $stmt = $dbh->getDbLink()->prepare('SELECT DISTINCT viewProjectsActive.idProject FROM `viewProjectsActive` JOIN deadlines ON viewProjectsActive.idProject=deadlines.idProject JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE viewProjectsActive.idProject IN (SELECT idProject FROM `deadlines`  WHERE `value` >= NOW() AND `value` <= NOW() + INTERVAL 30 DAY)');
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $counter = count($result);
    return $counter;
}

function selectProjectTermsInNextWeek($interval = NULL, $username = NULL, $myProjectsFromSession = TRUE) // interval is in DAYS
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (!isset($interval))
        $interval = 30;
    if ((isset($_SESSION['jenMojeProjekty'])) && ($_SESSION['jenMojeProjekty'] == 1) && $myProjectsFromSession)
        $username = $_SESSION['username'];
    if (isset($username)) {
        $stmt = $dbh->getDbLink()->prepare('SELECT viewProjectsActive.name,deadlines.value,viewProjectsActive.idProject,rangeDeadlineTypes.name as termName FROM `viewProjectsActive` JOIN deadlines ON viewProjectsActive.idProject=deadlines.idProject JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE viewProjectsActive.idProject IN (SELECT idProject FROM `deadlines`  WHERE `value` >= NOW() AND `value` <= NOW() + INTERVAL :interval DAY) AND deadlines.`value` >= NOW() AND deadlines.`value` <= NOW() + INTERVAL :interval DAY AND (editor=:user OR author=:user)');
        $stmt->bindParam(':user', $username, PDO::PARAM_STR);
        $stmt->bindValue(':interval', $interval, PDO::PARAM_INT);
    } else {
        $stmt = $dbh->getDbLink()->prepare('SELECT viewProjectsActive.name,deadlines.value,viewProjectsActive.idProject,rangeDeadlineTypes.name as termName FROM `viewProjectsActive` JOIN deadlines ON viewProjectsActive.idProject=deadlines.idProject JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE viewProjectsActive.idProject IN (SELECT idProject FROM `deadlines`  WHERE `value` >= NOW() AND `value` <= NOW() + INTERVAL :interval DAY) AND deadlines.`value` >= NOW() AND deadlines.`value` <= NOW() + INTERVAL :interval DAY');
        $stmt->bindValue(':interval', $interval, PDO::PARAM_INT);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function selectProjectTermsForFullCalendar($start, $end)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if ((isset($_SESSION['jenMojeProjekty'])) && ($_SESSION['jenMojeProjekty'] == 1)) {
        $stmt = $dbh->getDbLink()->prepare('SELECT viewProjectsActive.name,deadlines.value,viewProjectsActive.idPhase,viewProjectsActive.editor,viewProjectsActive.idProject,rangeDeadlineTypes.name as termName FROM `viewProjectsActive` JOIN deadlines ON viewProjectsActive.idProject=deadlines.idProject JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE viewProjectsActive.idProject IN (SELECT idProject FROM `deadlines`  WHERE `value` >= :start AND `value` <= :endView) AND (editor=:user OR author=:user)');
        $stmt->bindParam(':user', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam(':start', $start, PDO::PARAM_STR);
        $stmt->bindParam(':endView', $end, PDO::PARAM_STR);
    } else {
        $stmt = $dbh->getDbLink()->prepare('SELECT viewProjectsActive.name,deadlines.value,viewProjectsActive.idPhase,viewProjectsActive.editor,viewProjectsActive.idProject,rangeDeadlineTypes.name as termName FROM `viewProjectsActive` JOIN deadlines ON viewProjectsActive.idProject=deadlines.idProject JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE viewProjectsActive.idProject IN (SELECT idProject FROM `deadlines`  WHERE `value` >= :start AND `value` <= :endView)');
        $stmt->bindParam(':start', $start, PDO::PARAM_STR);
        $stmt->bindParam(':endView', $end, PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function projectTermsInNextWeekNotification()
{
    $counter = countProjectTermsInNextWeek();
    if ($counter > 0) {
        echo "<span class=\"notification\">$counter</span>";
    }
}

function projectTermsInNextWeekNotificationText()
{
    $counter = countProjectTermsInNextWeek();
    echo "U " . termsCzech($counter) . " je v příštích 30 dnech naplánován termín.";
}

function termsCzech($counter)
{
    if ($counter == 1) {
        if ((isset($_SESSION['jenMojeProjekty'])) && ($_SESSION['jenMojeProjekty'] == 1)) {
            $moje = "mojí ";
        } else {
            $moje = "";
        }
        return "1 " . $moje . "stavby";
    } else {
        if ((isset($_SESSION['jenMojeProjekty'])) && ($_SESSION['jenMojeProjekty'] == 1)) {
            $moje = "mých ";
        } else {
            $moje = "";
        }
        return $counter . " " . $moje . "staveb";
    }
}

function showTermsInNextWeek($interval = NULL, $username = NULL, $myProjectsFromSession = TRUE)
{
    $projects = selectProjectTermsInNextWeek($interval, $username, $myProjectsFromSession);

    $html = "<tbody>";
    foreach ($projects as $project) {
        $html .= "<tr><td>" . date("Y-m-d", strtotime($project['value'])) . "</td><td>" . $project['name'] . "</td><td>" . $project['idProject'] . "</td><td>" . $project['termName'] . "</td><td><a href=\"detail.php?idProject=" . $project['idProject'] . "\"><i class=\"fa fa-sign-in-alt\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID " . $project['idProject'] . "\"></i></a></td></tr>";
    }
    if ($html == "<tbody>") {
        $html .= "<tr><td></td><td></td><td><h3 class='text-center'>Žádný termín v příštích $interval dnech</h3></td><td></td><td></td></tr>";
    }
    $html .= "</tbody>";
    return $html;
}


function insertCustomEvent($eventArr, $username)
{
    require_once "../../includes/autoLoader.php";
    $lastId = false;
    $eventArrEscaped = htmlspecialcharsArr($eventArr);
    $username = htmlspecialchars($username);
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->beginTransaction();
    try {
        $stmt = $dbh->getDbLink()->prepare('INSERT INTO `calendarEvents` (username,title,description,eventStart,eventEnd,idOu) VALUES (:author,:title,:description,:eventStart,:eventEnd,:idOu)');
        $stmt->bindParam(':author', $username, PDO::PARAM_STR);
        $stmt->bindParam(':title', $eventArrEscaped['title'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $eventArrEscaped['description'], PDO::PARAM_STR);
        $stmt->bindParam(':eventStart', $eventArrEscaped['eventStart'], PDO::PARAM_STR);
        $stmt->bindParam(':eventEnd', $eventArrEscaped['eventEnd'], PDO::PARAM_STR);
        $stmt->bindParam(':idOu', $eventArrEscaped['idOu'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            $lastId = $stmt = $dbh->getDbLink()->lastInsertId();
            $stmt = $dbh->getDbLink()->commit();
        }
    } catch (Exception $e) {
        $stmt = $dbh->getDbLink()->rollBack();
        $lastId = false;
        echo 'Chyba s DB  ', $e->getMessage(), "\n";
    }
    return $lastId;
}

/*
 * $interval je ve dnech
 */

function getArrActionsLogByLimit4Project($idProject, $limit = 100)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (is_numeric($limit) && is_numeric($idProject)) {
        $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogAll` JOIN rangeActionTypes USING (idActionType) WHERE idProject = $idProject ORDER BY created DESC LIMIT $limit");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function getArrActionsLogByInterval4Project($idProject, $interval = 30)
{
    $dbh = new DatabaseConnector();
    require_once __DIR__ . "/autoLoader.php";
    $arrActionLog = false;
    if (is_numeric($interval) && is_numeric($idProject)) {
        $query = $dbh->getDbLink()->query("SELECT * FROM `viewActionsLogAll` JOIN rangeActionTypes USING (idActionType) WHERE `created`  < DATE_ADD(now(), INTERVAL $interval DAY) AND created > DATE_ADD(now(), INTERVAL -$interval DAY) AND idProject = $idProject ORDER BY created DESC");
        $arrActionLog = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    return $arrActionLog;
}

function getProjectWithIncomingWarranty(string $month_or_week, int $interval_ahead, int $warranty_period_in_years)
{
    $deadline_type = 12;
    if ($month_or_week == 'MONTH' || $month_or_week == "WEEK") {
        $dbh = new DatabaseConnector();
        require_once __DIR__ . "/autoLoader.php";
        $arrProjectsWithincomingWarranty = false;
        if (is_numeric($interval_ahead) && is_numeric($warranty_period_in_years)) {
            $query = $dbh->getDbLink()->query("SELECT *, DATEDIFF((deadlines.value + INTERVAL constructionTime WEEK) + INTERVAL 5 YEAR, NOW()) as zbyva_dni, deadlines.value, DATE(deadlines.value + INTERVAL constructionTime WEEK) as predani_stavby, DATE((deadlines.value + INTERVAL constructionTime WEEK) + INTERVAL $warranty_period_in_years YEAR) as konec_zaruky 
                                                        FROM `projects` JOIN deadlines USING(idProject) 
                                                        WHERE idDeadlineType = $deadline_type AND
                                                        ((deadlines.value + INTERVAL constructionTime WEEK) + INTERVAL $warranty_period_in_years YEAR) > NOW() AND ((deadlines.value + INTERVAL constructionTime WEEK) + INTERVAL $warranty_period_in_years YEAR) < NOW() + INTERVAL $interval_ahead $month_or_week");
            $arrProjectsWithincomingWarranty = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $arrProjectsWithincomingWarranty;
    } else {
        return false;
    }
}

function createWarrantyTable(array $data_arr)
{
    $html = "<table id='tableWarranty' class='table table-striped table-no-bordered table-hover dtr-inline'>
                <thead class=>
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            ID
                        </th>
                         <th>
                            Název projektu
                        </th>
                                           <th>
                            Datum předání stavby
                        </th>
                        <th>
                            Datum konce záruky
                        </th>
                        <th>
                            Zbývá dní
                        </th>
                        <th>
                            Osoba
                        </th>
                        <th>
                            
                        </th>
                    </tr>";
    if (empty($data_arr)) {
        $html .= "<tr><th colspan='8'><h3>Žádná stavba nenalezena</h3></th></tr>";
    }
    $html .= "  </thead>";
    $html .= "<tbody>";
    if (!empty($data_arr)) {
        foreach ($data_arr as $key => $row) {
            $pom = $key + 1;
            $html .= "<tr>
                    <td>$pom</td>
                    <td>$row[idProject]</td>
                    <td>$row[name]</td>
                    <td>$row[predani_stavby]</td>
                    <td>$row[konec_zaruky]</td>
                     <td>$row[zbyva_dni]</td>
                    <td>$row[editor]</td>
                    <td><a href='detail.php?idProject=$row[idProject]'><i class='fa fa-sign-in-alt' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID $row[idProject]\"></i></a></td>
                </tr>";
        }
    }
    $html .= "</tbody>
             </table>";
    return $html;

}

function createHistoryTable($history)
{
    $html = "<table id='tableHistory' class='table table-striped table-no-bordered table-hover dtr-inline'>
                <thead class=>
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            ID
                        </th>
                         <th>
                            Název projektu
                        </th>
                        <th>
                            Stav
                        </th>
                        <th>
                            Datum změny
                        </th>
                        <th>
                            Akce
                        </th>
                        <th>
                            Osoba
                        </th>
                        <th>
                            
                        </th>
                    </tr>";
    if (empty($history)) {
        $html .= "<tr><th colspan='8'><h3>Žádná změna na stavbách v posledních 30 dnech</h3></th></tr>";
    }
    $html .= "  </thead>";
    $html .= "<tbody>";
    if (!empty($history)) {
        foreach ($history as $key => $row) {
            $pom = $key + 1;
            $html .= "<tr>
                    <td>$pom</td>
                    <td>$row[idProject]</td>
                    <td>$row[projectName]</td>
                    <td>$row[phaseName]</td>
                    <td>$row[created]</td>
                    <td>$row[actionName]</td>
                    <td>$row[username]</td>
                    <td><a href='detail.php?idProject=$row[idProject]'><i class='fa fa-sign-in-alt' data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID $row[idProject]\"></i></a></td>
                </tr>";
        }
    }
    $html .= "</tbody>
             </table>";
    return $html;
}

function circleByPhase($idPhase)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT phaseColorClass,name FROM rangePhases WHERE `idPhase` = :idPhase');
    $stmt->bindValue(':idPhase', $idPhase, PDO::PARAM_INT);
    $stmt->execute();
    $phaseColor = $stmt->fetchAll();
    $html = "<span class=\"fa fa-circle text-" . $phaseColor[0]['phaseColorClass'] . "\" data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $phaseColor[0]['name'] . "\"></span>";
    return $html;
}

function colorByPhase($idPhase)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT phaseColor FROM rangePhases WHERE `idPhase` = :idPhase');
    $stmt->bindValue(':idPhase', $idPhase, PDO::PARAM_INT);
    $stmt->execute();
    $phaseColor = $stmt->fetchAll();
    return $phaseColor[0]['phaseColor'];
}

function colorByPhaseName($phaseName)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT phaseColor FROM rangePhases WHERE `name` = :phaseName');
    $stmt->bindParam(':phaseName', $phaseName, PDO::PARAM_STR);
    $stmt->execute();
    $phaseColor = $stmt->fetch();
    return $phaseColor['phaseColor'];
}

function styleByPhase($idPhase)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT phaseColorClass FROM rangePhases WHERE idPhase = :idPhase');
    $stmt->bindValue(':idPhase', $idPhase, PDO::PARAM_INT);
    $stmt->execute();
    $phaseStyle = $stmt->fetchAll();
    return $phaseStyle[0]['phaseColorClass'];
}

function projectLastChangesText($idProject, $interval = 365)
{
    $posledniZmeny = getArrActionsLogByInterval4Project($idProject, $interval);
    if (empty($posledniZmeny)) {
        $html = "<b>Poslední změna:</b> Žádná změna za posledních " . $interval . " dní.";
    } else {
        $user = getUserAll($posledniZmeny[0]['username']);
        $html = "<b>Poslední změna:</b> " . $posledniZmeny[0]['actionName'] . ", provedl: " . $user[0]['name'] . " (" . date("d.m.Y H:i",
                strtotime($posledniZmeny[0]['created'])) . ")";
    }
    return $html;
}


function unicodeToUtf8($string)
{

    return html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $string), ENT_NOQUOTES, 'UTF-8');
}

function stripTags($decodedHtml)
{
    $stripString = strip_tags($decodedHtml, '<b>,<br>,<i>,<strong>,<em>,<ul>,<ol>,<li>,<span>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>');
    return $stripString;

}


function showUserProjects()
{
    $interval = 365; //limit pro nacitani posledni zmeny - pocet dni
    $projects = selectUserProjects();
    $html = "";
    foreach ($projects as $project) {
        $posledniZmeny = getArrActionsLogByInterval4Project($project['idProject'], $interval);
        $projectObject = new Project($project['idProject']);
        if (empty($posledniZmeny)) {
            $html .= "
                                    <div class=\"card-collapse\">
                                        <div class=\"card-header\" role=\"tab\" id=\"headingProject" . $project['idProject'] . "\">
                                            <h5 class=\"mb-0\">
                                            <!--<a class='float-right' href=\"detail.php?idProject=" . $project['idProject'] . "\"><i class=\"fa fa-sign-in-alt\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID " . $project['idProject'] . "\"></i></a>-->
                                                <a data-toggle=\"collapse\" href=\"#collapseProject" . $project['idProject'] . "\" aria-expanded=\"false\" aria-controls=\"collapseProject" . $project['idProject'] . "\" class=\"collapsed\">
                                                   " . circleByPhase($project['idPhase']) . " " . $project['name'] . " (ID " . $project['idProject'] . ")
                                                    <i class=\"material-icons\">keyboard_arrow_down</i>
                                                </a>                       
                                            </h5>
                                        </div>
                                        <div id=\"collapseProject" . $project['idProject'] . "\" class=\"collapse\" role=\"tabpanel\" aria-labelledby=\"headingProject" . $project['idProject'] . "\" data-parent=\"#accordion\" style=\"\">
                                            <div class=\"card-body\"><div class='row'><div class='col-md-10'>
                                            <b>Předmět stavby:</b> " . unicodeToUtf8(stripTags(htmlspecialchars_decode($project['subject']))) . "<br>
                                            <b>Termíny:</b> 
                                            " . showNearestTerm($project['idProject']) . "<br>
                                            <b>Poslední změna:</b> Žádná změna za posledních " . $interval . " dní.
                                            </div>
                                            <div class='col-md-2'>
                                            <a class='float-right btn btn-lg btn-rose' href=\"detail.php?idProject=" . $project['idProject'] . "\"  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID " . $project['idProject'] . "\"><i class=\"fa fa-sign-in-alt\"></i></a>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
        ";
        } else {
            $user = getUserAll($posledniZmeny[0]['username']);
            $html .= "
                                    <div class=\"card-collapse\">
                                        <div class=\"card-header\" role=\"tab\" id=\"headingProject" . $project['idProject'] . "\">
                                            <h5 class=\"mb-0\">
                                            <!--<a class='float-right' href=\"detail.php?idProject=" . $project['idProject'] . "\"><i class=\"fa fa-sign-in-alt\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID " . $project['idProject'] . "\"></i></a>-->
                                                <a data-toggle=\"collapse\" href=\"#collapseProject" . $project['idProject'] . "\" aria-expanded=\"false\" aria-controls=\"collapseProject" . $project['idProject'] . "\" class=\"collapsed\">
                                                   " . circleByPhase($project['idPhase']) . " " . $project['name'] . " (ID " . $project['idProject'] . ")
                                                    <i class=\"material-icons\">keyboard_arrow_down</i>
                                                </a>                       
                                            </h5>
                                        </div>
                                        <div id=\"collapseProject" . $project['idProject'] . "\" class=\"collapse\" role=\"tabpanel\" aria-labelledby=\"headingProject" . $project['idProject'] . "\" data-parent=\"#accordion\" style=\"\">
                                            <div class=\"card-body\"><div class='row'><div class='col-md-10'>
                                            <b>Předmět stavby:</b> " . unicodeToUtf8(stripTags(htmlspecialchars_decode($project['subject']))) . "<br>
                         
                                            " . showNearestTerm($project['idProject']) . "<br>
                                            <b>Poslední změna:</b> " . $posledniZmeny[0]['actionName'] . ", provedl: " . $user[0]['name'] . " (" . date("d.m.Y H:i",
                    strtotime($posledniZmeny[0]['created'])) . ")
                                            </div>
                                            <div class='col-md-2'>
                                            <a class='float-right btn btn-lg btn-rose' href=\"detail.php?idProject=" . $project['idProject'] . "\"  data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID " . $project['idProject'] . "\"><i class=\"fa fa-sign-in-alt\"></i></a>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
        ";
        }
    }
    if (empty($html)) {
        $html .= "Nemáme u vás vedenou žádnou stavbu.";
    }
    return $html;
}

function find_closest($array, $date)
{
    foreach ($array as $day) {
        $interval[] = abs(strtotime($date) - strtotime($day));
    }
    asort($interval);
    $closest = key($interval);
    return $array[$closest];
}

function showNearestTerm($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT MIN(`deadlines`.value) as deadline, rangeDeadlineTypes.name FROM `deadlines` JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE idProject=:idProject AND deadlines.value>NOW()');
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $terms = $stmt->fetch(PDO::FETCH_ASSOC);

    $html = "";
    if (empty($terms['deadline'])) {
        $html = "Žádné termíny nebyly zadány do databáze.";
    } else {
        if (strtotime($terms['deadline']) > strtotime(date("Y-m-d H:i:s"))) {
            $html .= "<b>Nejbližší termín:</b> " . $terms['name'] . ": " . date("d.m.Y", strtotime($terms['deadline']));
        } else {
            $stmt2 = $dbh->getDbLink()->prepare('SELECT MIN(`deadlines`.value) as deadline, rangeDeadlineTypes.name FROM `deadlines` JOIN rangeDeadlineTypes ON deadlines.idDeadlineType=rangeDeadlineTypes.idDeadlineType WHERE idProject=:idProject AND deadlines.value>NOW()');
            $stmt2->bindParam(':idProject', $idProject, PDO::PARAM_INT);
            $stmt2->execute();
            $terms2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $html .= "<b>Poslední termín:</b> " . $terms2['name'] . ": " . date("d.m.Y", strtotime($terms2['deadline']));
        }
    }

    return $html;
}

function random_color_part()
{
    $dt = '';
    for ($o = 1; $o <= 3; $o++) {
        $dt .= str_pad(dechex(mt_rand(0, 127)), 2, '0', STR_PAD_LEFT);
    }
    return $dt;
}

function getNotUsedColorForTag()
{
    do {
        $tagColor = random_color_part();
        require_once __DIR__ . "/autoLoader.php";
        $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare('SELECT tagColor FROM `rangeTags` WHERE tagColor=:tagColor');
        $stmt->bindParam(':tagColor', $tagColor, PDO::PARAM_INT);
        $stmt->execute();
    } while ($stmt->rowCount() > 0);
    return $tagColor;
}

function selectAllDeadlineTypes()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT * FROM rangeDeadlineTypes WHERE hidden is false');
    $stmt->execute();
    $deadlineTypes = $stmt->fetchAll();

    return $deadlineTypes;
}

// VRACÍ TERMÍNY, KTERÉ PROJEKT JEŠTĚ NEMÁ PŘIŘAZENÉ A ZÁROVEŇ JSOU DOSTUPNÉ PRO JEHO AKTUÁLNÍ FÁZI - NEVRACÍ HIDDEN FÁZE
function selectNotUsedDeadlineTypes($idProject)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idProject)) {
        (int)$projectPhaseId = getProjectById($idProject)[0]['idPhase'];
        $stmt = $dbh->getDbLink()->query("SELECT idDeadlineType, name FROM rangeDeadlineTypes WHERE hidden is false AND (availableInPhase = $projectPhaseId OR availableInPhase = 0) AND idDeadlineType NOT IN (SELECT idDeadlineType FROM deadlines WHERE idProject = $idProject)");
        $typesArr = $stmt->fetchAll();
    }
    return $typesArr;
}

function selectDeadlineTypesAvailableOnlyInPhase($idPhase)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($idPhase)) {
        $stmt = $dbh->getDbLink()->query("SELECT idDeadlineType, name FROM rangeDeadlineTypes WHERE hidden is false AND availableInPhase = $idPhase");
        $typesArr = $stmt->fetchAll();
    }
    return $typesArr;
}

function getWarrantyDeadline(DateTime $datimeNow, $warrantyPeriod)
{

    $datimeNow->modify("+$warrantyPeriod month"); // or you can use '-90 day' for deduct
    $datimeNow = $datimeNow->format('Y-m-d h:i:s');
    return $datimeNow;

}

function getWarrantiesByTypes(int $warrantyType)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if (is_numeric($warrantyType)) {
        $stmt = $dbh->getDbLink()->query("SELECT * FROM rangeWarranties JOIN rangeWarrantiesTypes USING(idWarrantyType) WHERE rangeWarranties.hidden is false AND idWarrantyType = $warrantyType ");
        $typesArr = $stmt->fetchAll();
    }
    return $typesArr;
}

function createHtmlSelectOptions($optionsArr, $valueIndexName, $optionIndexName)
{
    $html = "";
    foreach ($optionsArr as $option) {
        $html .= "<option value='$option[$valueIndexName]' >$option[$optionIndexName]</option>";
    }
    return $html;
}

function addWarrantyPeriodsToProject($hotovo, $techWarranty, $constWarranty)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('UPDATE projects SET constructionWarrantyPeriod = :constWarranty, technologyWarrantyPeriod = :techWarranty WHERE idProject = :idProject');
    $stmt->bindParam(':idProject', $hotovo, PDO::PARAM_INT);
    $stmt->bindParam(':techWarranty', $techWarranty, PDO::PARAM_INT);
    $stmt->bindParam(':constWarranty', $constWarranty, PDO::PARAM_INT);
    $stmt->execute();
}

function checkEditorInProjectHistory($username, $idProject)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT DISTINCT author FROM `projectVersions` WHERE idProject = :idProject');
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $editors = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (in_array($username, $editors))
        return true;
    else
        return false;
}

function getAllEditorsInProjectHistoryWithLastActionDate($idProject)
{

    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $stmt = $dbh->getDbLink()->prepare('SELECT author, MAX(created) as lastAction FROM `projectVersions` WHERE idProject = :idProject GROUP BY author');
    $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
    $stmt->execute();
    $editors = $stmt->fetchAll();
    return $editors;
}

function editorHistoryForTooltipOnProjectCard($idProject)
{
    $editors = getAllEditorsInProjectHistoryWithLastActionDate($idProject);
    $html = "<strong>Historie editorů projektu</strong><br>(poslední editace)";
    foreach ($editors as $editor) {
        $html .= "<br><strong>$editor[author]</strong> (" . date("j.n.Y", strtotime($editor['lastAction'])) . ")";
    }
    return $html;
}

// USED IN MAIL REPORTS
function getUpcomingWarranties($limit = 50, $interval = 30, $username = NULL) // interval is in DAYS
{
    if ($interval && is_numeric($interval)) {
        $intervalQuery = "AND loginTime > DATE_SUB(now(), INTERVAL $interval DAY)";
    } else {
        $intervalQuery = "";
    }
    if ($username) {
        $userQuery = "AND viewProjectsActive.idProject IN (SELECT idProject FROM `viewProjectsActive` WHERE editor = '$username')";
    } else {
        $userQuery = "";
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT deadlines.idProject, deadlines.value, viewProjectsActive.name as projectName, rangeDeadlineTypes.name as deadlineName FROM deadlines JOIN rangeDeadlineTypes ON (deadlines.idDeadlineType = rangeDeadlineTypes.idDeadlineType) JOIN viewProjectsActive ON (deadlines.idProject = viewProjectsActive.idProject) WHERE deadlines.idDeadlineType IN (25,26) AND deadlines.value < DATE_ADD(now(), INTERVAL $interval DAY) AND deadlines.value > now() $userQuery ORDER BY deadlines.value ASC LIMIT $limit");
    $warrantiesDataArr = $query->fetchAll(PDO::FETCH_ASSOC);
    return $warrantiesDataArr;
}

// USED IN MAIL REPORTS
function getTermsForReports($limit = 50, $interval = 30) // interval is in DAYS
{
    if ($interval && is_numeric($interval)) {
        $intervalQuery = "AND loginTime > DATE_SUB(now(), INTERVAL $interval DAY)";
    } else {
        $intervalQuery = "";
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT deadlines.idProject, deadlines.value, viewProjectsActive.name as projectName, rangeDeadlineTypes.name as deadlineName FROM deadlines JOIN rangeDeadlineTypes ON (deadlines.idDeadlineType = rangeDeadlineTypes.idDeadlineType) JOIN viewProjectsActive ON (deadlines.idProject = viewProjectsActive.idProject) WHERE rangeDeadlineTypes.hidden = 0 AND deadlines.value < DATE_ADD(now(), INTERVAL $interval DAY) AND deadlines.value > now() ORDER BY deadlines.value ASC LIMIT $limit");
    $deadlinesDataArr = $query->fetchAll(PDO::FETCH_ASSOC);
    return $deadlinesDataArr;
}

// USED IN MAIL REPORTS
function countActiveProjects($intervalVytvoreno = NULL, $intervalAktualizovano = NULL, $idPhase = NULL)
{
    if ($intervalVytvoreno) {
        $intervalVytvorenoQuery = "WHERE created > DATE_SUB(now(), INTERVAL $intervalVytvoreno DAY)";
    } else {
        $intervalVytvorenoQuery = "";
    }
    if ($intervalAktualizovano) {
        if ($intervalVytvoreno)
            $intervalAktualizovanoQuery = " AND ";
        else
            $intervalAktualizovanoQuery = "WHERE ";
        $intervalAktualizovanoQuery .= "updated > DATE_SUB(now(), INTERVAL $intervalAktualizovano DAY)";
    } else {
        $intervalAktualizovanoQuery = "";
    }
    if ($idPhase) {
        if ($intervalVytvoreno || $intervalAktualizovano)
            $phaseQuery = " AND ";
        else
            $phaseQuery = "WHERE ";
        $phaseQuery .= "idPhase = $idPhase";
    } else {
        $phaseQuery = "";
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT COUNT(idProject) AS pocet FROM `viewProjectsActive` $intervalVytvorenoQuery $intervalAktualizovanoQuery $phaseQuery");
    $numberOfProjects = $query->fetch(PDO::FETCH_ASSOC);
    return $numberOfProjects['pocet'];
}

// USED IN MAIL REPORTS
function getProjectCountsByPhase()
{
    $html = "";
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT * FROM `rangePhases`");
    $phases = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($phases as $phase) {
        $html .= "Počet projektů ve fázi " . $phase['name'] . ": " . countActiveProjects(NULL, NULL, $phase['idPhase']) . ".<br>";
    }
    return $html;
}

// USED IN MAIL REPORTS
function countPhaseChangesInInterval($interval = 30)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT COUNT(idAction) AS pocet FROM `actionsLogs` WHERE idActionType = 3 AND created > DATE_SUB(now(), INTERVAL $interval DAY)");
    $count = $query->fetch();
    return round($count[0]);
}

// USED IN MAIL REPORTS
function countUserActionsInInterval($username, $interval = 30)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT COUNT(idAction) AS pocet FROM `actionsLogs` WHERE username = '$username' AND created > DATE_SUB(now(), INTERVAL $interval DAY)");
    $count = $query->fetch();
    return $count[0];
}

// USED IN MAIL REPORTS
function countAllProjectPricesAsOf($interval = NULL)
{
    if ($interval && is_numeric($interval)) {
        $intervalQuery = "AND d.updated > DATE_SUB(now(), INTERVAL $interval DAY)";
    } else {
        $intervalQuery = "";
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT IFNULL(SUM(IFNULL(value, 0)), 0) FROM `viewProjectsActive` d JOIN prices USING(idProject) JOIN rangePriceTypes USING(idPriceType)
WHERE `idPriceType` IN (5,6,11) AND ordering = (SELECT MAX(ordering) FROM prices tmp JOIN rangePriceTypes USING(idPriceType) WHERE idPriceType IN (5,6,11) AND tmp.idProject = d.idProject ) $intervalQuery");
    $count = $query->fetch();
    return round($count[0]);
}

// USED IN MAIL REPORTS
function countSumPricesForPhases(array $ouArr = null, $username = NULL)
{
    $where = "";
    if (is_array($ouArr)) {
        $where = "d.idOu IN (" . implode(",", $ouArr) . ") AND";
    }
    if ($username) {
        $userQuery = "AND d.idProject IN (SELECT idProject FROM `viewProjectsActive` WHERE editor = '$username')";
    } else {
        $userQuery = "";
    }
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT phaseName, IFNULL(SUM(IFNULL(value, 0)), 0) as price FROM viewProjectsActive d JOIN prices USING(idProject) JOIN rangePriceTypes USING(idPriceType)
WHERE $where idPriceType IN (5,6,11) $userQuery AND ordering = (SELECT MAX(ordering) FROM prices tmp JOIN rangePriceTypes USING(idPriceType) WHERE idPriceType IN (5,6,11) AND tmp.idProject = d.idProject ) GROUP BY phaseName");
    $count = $query->fetchAll();
    return $count;
}

// USED IN MAIL REPORTS
function countPhasesToEditor($editor)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    $query = $dbh->getDbLink()->query("SELECT DISTINCT phaseName, COUNT(idPhase) as phaseCount, idPhase FROM `viewProjectsActive` WHERE editor = '$editor' GROUP BY phaseName ORDER BY idPhase DESC");
    $count = $query->fetchAll();
    return $count;
}

function getPhaseDetails($idPhase = NULL, $name = NULL)
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
    if ($idPhase === NULL && $name === NULL) {
        $stmt = $dbh->getDbLink()->prepare('SELECT * FROM `rangePhases` ORDER BY `level` ASC');
        $stmt->execute();
        $phases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        if ($idPhase != NULL) {
            $stmt = $dbh->getDbLink()->prepare('SELECT * FROM `rangePhases` WHERE idPhase = :idPhase');
            $stmt->bindParam(':idPhase', $idPhase, PDO::PARAM_INT);
        } elseif ($name != NULL) {
            $stmt = $dbh->getDbLink()->prepare('SELECT * FROM `rangePhases` WHERE name = :name');
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        }
        $stmt->execute();
        $phases = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($phases)
        return $phases;
    else
        return false;
}

function getRequestsStates()
{
    require_once __DIR__ . "/autoLoader.php";
    $dbh = new DatabaseConnector();
        $stmt = $dbh->getDbLink()->prepare("SELECT * FROM `rangeRequestStatuses` WHERE isenabled = 1 AND croseus != 1 ORDER by rank ASC");
        $stmt->execute();
        $phases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($phases)
        return $phases;
    else
        return false;
}

function listAllPhasesLegend()
{
    $phases = getPhaseDetails();
    $list = "";
    foreach ($phases as $phase) {
        $list .= '<b style="color: '.$phase['phaseColor'].'">⬤</b>&nbsp;'.$phase['name'].' ';
    }
    return $list;
}

function listAllRequestsStatesLegend()
{
    $phases = getRequestsStates();
    $list = "";
    foreach ($phases as $phase) {
        $list .= '<b style="color: '.$phase['statusColor'].'">⬤</b>&nbsp;'.$phase['name'].' ';
    }
    return $list;
}