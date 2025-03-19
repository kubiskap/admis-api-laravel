<?php
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
if (isset($_GET['changeAllOrMine'])) {
    if ($_GET['moje'] == 1)
        $_SESSION['jenMojeProjekty'] = 1;
    else
        $_SESSION['jenMojeProjekty'] = 0;
}
if (isset($_GET['interval']))
    $interval = $_GET['interval'];
else
    $interval = null;
if (isset($_GET['report']))
    $username = $_SESSION['username'];
else
    $username = null;
$table = "
<thead>
     <tr>
                        <th scope=\"col\">Termín</th>
                        <th scope=\"col\">Projekt</th>
                        <th scope=\"col\">ID</th>
                        <th scope=\"col\">Typ termínu</th>
                        <th scope=\"col\"></th>
     </tr>
</thead>
";
$table .= showTermsInNextWeek($interval, $username);
echo $table;
?>