<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
$userTasks = getTasksForUser();

$data = array();
//$events = selectProjectTermsForFullCalendar($_POST['start'],$_POST['end']);
foreach ($userTasks as $task) {
    if ($task['deadline']) {
        $description = $task['description']."<br><b>Stav:</b> ".$task['status']."<br><b>Zadal:</b> ".getUserAll($task['createdBy'])[0]['name']."<br><b>Projekt:</b> ".$task['projectName']." (ID ".$task["idProject"].")";
        $data[] = [
            'id'   => 'task'.$task["idTask"],
            'title' => '⚑ '.$task['name'],
            'start' => date('Y-m-d H:i', strtotime($task['deadline'])),
            'end' => date('Y-m-d H:i', strtotime($task['deadline'])),
            'description' => $description,
            'color' => $task['statusColor'],
            'allDay' => TRUE,
            'url' => 'detail.php?idProject=' . $task["idProject"],
        ];
    }
}
echo json_encode($data);