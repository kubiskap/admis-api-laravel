<?php
if (isset($_GET['zadanka']) && (is_numeric($_GET['zadanka']) || $_GET['zadanka'] === "new")) {
    require PARTS . 'zadankySettingsEdit.inc';
} else {
    require PARTS . 'zadankySettingsTable.inc';
}
