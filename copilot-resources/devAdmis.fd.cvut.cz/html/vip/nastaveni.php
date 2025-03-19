<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 04.07.2018
 * Time: 13:38
 */

require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
/*if ($_SESSION['role'] != 'adminEditor' ) {
    $pristup_zakazan = TRUE;
}*/
overUzivatele($pristup_zakazan);

$title = 'Nastavení';
?>
<?php include PARTS . "startPage.inc"; ?>

<div class="content">
    <div class="container-fluid">
        <?php
        if (isset($_GET['sprava'])) {
            switch ($_GET['sprava']) {
                case "firmy":
                    require PARTS . 'companiesSettings.inc';
                    break;
                case "kontakty":
                    require PARTS . 'contactsSettings.inc';
                    break;
                case "uzivatele":
                    require PARTS . 'usersSettings.inc';
                    break;
                case "logy":
                    require PARTS . 'logsSettings.inc';
                    break;
                case "soubory":
                    require PARTS . 'filesSettings.inc';
                    break;
                case "priority":
                    require PARTS . 'prioritySettings.inc';
                    break;
                case "zadanky":
                    require PARTS . 'zadankySettings.inc';
                    break;
                default:
                    echo "<b>Tato sekce neexistuje, nebo zatím není přístupná.</b>";

            }

        }


        ?>
        <!--
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon card-header-danger">
                        <div class="card-text">
                            <h4 class="card-title"><i class="material-icons">group</i> Správa uživatelů</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        Tady muze byt treba sprava uzivatelu
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
</div>

<div class="skupinaModalu">
    <?php
    if (isset($_GET['sprava'])) {
        if ($_GET['sprava']=="uzivatele") {
            includeFilesFromDirectory(PARTS."/modals/settings/user-settings/*.inc",TRUE);
        }
        if ($_GET['sprava']=="kontakty") {
            includeFilesFromDirectory(PARTS."/modals/settings/contact-settings/*.inc",TRUE);
        }
        if ($_GET['sprava']=="firmy") {
            includeFilesFromDirectory(PARTS."/modals/settings/company-settings/*.inc",TRUE);
        }
        if ($_GET['sprava']=="zadanky") {
            includeFilesFromDirectory(PARTS."/modals/settings/zadanky-settings/*.inc",TRUE);
        }
    }

    ?>
</div>

<?php
if (!isset($customScripts)) {
    $customScripts = "";
}
$customScripts .= "
<script src=\"/js/nastaveni.js\"></script>
<script src=\"/js/nastaveniFirma.js\"></script>
<script type=\"text/javascript\">Loader.load()</script>
";
?>

<?php include PARTS . "endPage.inc"; ?>

