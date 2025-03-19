<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 04.07.2018
 * Time: 13:38
 */

require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
$title = 'Mapa';
?>
<?php include PARTS."startPage.inc"; ?>
<style>
    .box-mapa{
        position: absolute;
        top:0px;
        z-index:500;
        margin: 8px;
        padding: 10px;
        box-sizing:border-box;
        background-color: white;
        font-size: 13px;
        border: 1px solid #E0E0E0;
        border-radius: 2px;
        color:#6b7580;
    }
</style>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <?php include PARTS."filter-select2.inc"; ?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon card-header-danger">
                        <div class="card-icon">
                            <i class="material-icons">map</i>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['editor']) && $_GET['editor']===$_SESSION['username']) echo '<div class="alert alert-info alert-with-icon" data-notify="container">
                    <i class="material-icons" data-notify="icon">notifications</i>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <i class="material-icons">close</i>
                    </button>
                    <span data-notify="icon" class="now-ui-icons ui-1_bell-53"></span>
                    <span data-notify="message">Pro urychlení načítání stránky byly předvybrány do mapy jen vaše projekty. Pro hledání projektů ostatní editorů je třeba své jméno odznačit ve výběru "Osoba KSÚS".</span>
                  </div>'; ?>
                        <div id="mapLeaflet" style="height: 80vh">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$customScripts = "";
$customScripts .= "
<script src=\"/js/mapa.js\"></script>
<script type=\"text/javascript\">Loader.load()</script>
";
?>

<?php include PARTS."endPage.inc"; ?>

