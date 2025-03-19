<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 20.06.2018
 * Time: 15:37
 */
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
require_once(CLASSES. "Enums.php");
overUzivatele($pristup_zakazan);
$title = 'Žádanky';

$tableData = getRequestsTable(getRequests());


?>
<?php include PARTS."startPage.inc"; ?>
<!--<div id="progress-bar" style="display: none;">
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;"></div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">verified</i>
                </div>
                <p class="card-category">Uzavřených</p>
                <h3 class="card-title">0</h3>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="material-icons">date_range</i> Schválené nebo odložené žádanky
                </div>
            </div>
        </div>
        <div class="card card-stats">
            <div class="card-header card-header-success card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">add_task</i>
                </div>
                <p class="card-category">Neřešené </p>
                <h3 class="card-title">6 </h3>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="material-icons">date_range</i>Žádanky ve stavu zadáno
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-rose card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">
assignment_return
</i>
                </div>
                <p class="card-category">Vrácené</p>
                <h3 class="card-title">0</h3>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="material-icons">date_range</i> Žádanky ve stavu vráceno k doplnění
                </div>
            </div>
        </div>
        <div class="card card-stats">
            <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">task</i>
                </div>
                <p class="card-category">Celkem</p>
                <h3 class="card-title">6</h3>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="material-icons">date_range</i>Počet celkových žádanek
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-chart">
            <div class="card-header card-header-icon card-header-danger">
                <div class="card-icon">
                    <i class="material-icons">pie_chart</i>
                </div>
                <h4 class="card-title">Počet žádanek dle stavu</h4>
            </div>
            <div class="card-body">
                <div id="PieGraphRequestsPerState" class="ct-chart"></div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="card-category">Legenda</h6>
                    </div>
                    <div class="col-md-12">
                        <?php echo listAllRequestsStatesLegend(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>-->
<div class="row" >
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-danger">
                <div class="card-text">
                    <h4 class="card-title"><i class="material-icons">task</i> Seznam žádanek
                    </h4>
                </div>
            </div>
            <div class="card-body" id="requestsTable">
<div class="material-datatables">
    <table id="datatableZadanky" class="table table-striped table-no-bordered table-hover">
    <thead>
    <tr>
        <th>ID</th>
        <th>C link</th>
        <th>C status</th>
        <th>Projekt</th>
        <th>Typ</th>
        <th>Zadal</th>
        <th>Zadáno</th>
        <th>Status</th>
        <th>Reakce</th>
        <th>Počet reakcí</th>
        <th>Akce</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th>ID</th>
        <th>C link</th>
        <th>C status</th>
        <th>Projekt</th>
        <th>Typ</th>
        <th>Zadal</th>
        <th>Zadáno</th>
        <th>Status</th>
        <th>Reakce</th>
        <th>Počet reakcí</th>
        <th>Akce</th>
    </tr>
    </tfoot>
    <tbody id="requestsTableBody">
   <?php echo $tableData; ?>
    </tbody>
    </table>
</div>
    </div>
        </div></div>

<div class="skupinaModalu">

    <?php  includeFilesFromDirectory(PARTS."/modals/zadanky/*.inc",TRUE) ?>
    <?php  include PARTS."/modals/vypis/requestsModal.inc"; ?>

</div>


<?php
$customScripts = "<script src=\"/js/requestsOverview.js\"></script>
";

?>



<?php include PARTS."endPage.inc"; ?>

