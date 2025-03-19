<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 25.07.2018
 * Time: 10:44
 */
require_once __DIR__."/../../conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once SYSTEMINCLUDES . "autoLoader.php";
overUzivatele($pristup_zakazan);


$dbh = new DatabaseConnector();
$stmt = $dbh->getDbLink()->prepare('SELECT projectVersions.idProject, projectVersions.assignments, fnStripTags(projectVersions.assignments) AS striped, projectVersions.author, projectVersions.created, projectVersions.idPhase FROM `projectVersions` JOIN projects USING (idProject) WHERE projectVersions.created IN (SELECT MAX(created) as lastVersion from projectVersions GROUP BY idProject) AND projectVersions.assignments IS NOT NULL AND projectVersions.assignments != "" AND projectVersions.assignments != "Projekt nemá přiřazený žádný úkol" AND projectVersions.assignments != "&lt;p&gt;Projekt nem&amp;aacute; přiřazen&amp;yacute; ž&amp;aacute;dn&amp;yacute; &amp;uacute;kol&lt;/p&gt;" AND projectVersions.assignments != "&lt;p&gt;Projekt nemá přiřazený žádný úkol&lt;/p&gt;" AND projectVersions.assignments != "&lt;p&gt;&amp;uacute;kol&lt;/p&gt;" AND projects.deletedDate IS NULL ORDER BY projectVersions.`idLocalProject` DESC ');
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tasks as $task) {
    echo "Project ID: ".$task['idProject'];
    echo "<br>";
    echo "Úkol: ".htmlspecialchars_decode($task['striped']);
    echo "<br>";
    //echo "Ass: ".$task['assignments'];
    //echo "<br>";
    echo "Zadal: ".$task['author'];
    echo "<br>";
    echo "Kdy: ".$task['created'];
    echo "<br>";
    echo "<br>";

    /*$stmt = $dbh->getDbLink()->prepare("INSERT INTO `tasksProject`(`createdBy`,`created`, `relatedToProjectId`) VALUES (:createdBy,:created,:relatedToProjectId)");
    $stmt->bindParam(':createdBy', $task['author'], PDO::PARAM_STR);
    $stmt->bindParam(':created', $task['created'], PDO::PARAM_STR);
    $stmt->bindParam(':relatedToProjectId', $task['idProject'], PDO::PARAM_INT);
    if ($stmt->execute()) {
        $lastId = $dbh->getDbLink()->lastInsertId();
        $deadline = NULL;
        $name = "Úkol ze starší verze Admisu";
        $description = htmlspecialchars_decode($task['striped']);
        $idTaskStatus = 1;
        $stmt = $dbh->getDbLink()->prepare("INSERT INTO `taskVersions`(`idTask`,`name`, `description`,`createdBy`,`created`,`idTaskStatus`,`deadlineTo`) VALUES (:idTask,:name,:description,:createdBy,:created,:idTaskStatus,:deadlineTo)");
        $stmt->bindParam(':idTask', $lastId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':createdBy', $task['author'], PDO::PARAM_STR);
        $stmt->bindParam(':created', $task['created'], PDO::PARAM_STR);
        $stmt->bindParam(':idTaskStatus', $idTaskStatus, PDO::PARAM_INT);
        $stmt->bindParam(':deadlineTo', $deadline, PDO::PARAM_STR);
        $stmt->execute();
    }*/
}




?>