<?php
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 25.07.2018
 * Time: 10:44
 */
/* VYTVOŘENO PRO PŘEPSÁNÍ SUBJEKTŮ U PROJEKTŮ, KDE BYLY HISTORICKY HTML ENTITY MÍSTO ČESKÝCH ZNAKŮ V UTF-8
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
//overUzivatele($pristup_zakazan);

$dbh = new DatabaseConnector();
$stmt = $dbh->getDbLink()->prepare("SELECT * FROM `projects` WHERE `subject` LIKE :subject");
$stmt->execute([':subject' => '%&%']);
$dataArr = $stmt->fetchAll();

foreach ($dataArr as $key => $project) {
    $decodedSubject = html_entity_decode($project['subject']);
    if (strlen($decodedSubject) > 0 && strlen($project['subject']) > 0) {
        echo "new". $project['idProject'] . ": ". $decodedSubject."<br>";
        echo "org" . $project['idProject'] . ": ". $project['subject']."<br>";

        $stmt = $dbh->getDbLink()->prepare("UPDATE `projects` SET `subject`=:subject WHERE idProject = :idProject");
        $stmt->bindValue(':idProject', $project['idProject'], PDO::PARAM_INT);
        $stmt->bindParam(':subject', $decodedSubject, PDO::PARAM_STR);
        if ($stmt->execute()) {
            echo "Opraveno<br><br>";
        } else {
            echo "Chyba komunikace<br><br>";
        }
    }
}

$projectData = getProjectById($idProject);
$decodedSubject = html_entity_decode($projectData[0]['subject']);
echo $decodedSubject;

$dbh = new DatabaseConnector();
$stmt = $dbh->getDbLink()->prepare("UPDATE `projects` SET `subject`=:subject WHERE idProject = :idProject");
$stmt->bindValue(':idProject', $idProject, PDO::PARAM_INT);
$stmt->bindParam(':subject', $decodedSubject, PDO::PARAM_STR);
if ($stmt->execute()) {
    echo "<br><br>Opraveno";
} else {
    echo "<br><br>Chyba komunikace";
}*/