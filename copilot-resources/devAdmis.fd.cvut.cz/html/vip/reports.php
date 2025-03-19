<?php
$title = "Reporty";
/**
 * Created by PhpStorm.
 * User: petros
 * Date: 23.09.2022
 * Time: 16:07
 */
require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
overUzivatele($pristup_zakazan);
//$userInfo = getUserDetails($_SESSION['username']);
?>
<?php include PARTS."startPage.inc"; ?>


<div class="row">
    <div class="col-md-12">
        <div class="card card-profile">
            <div class="card-avatar">
                <a href="#pablo">
                    <img
                        src="data:image/png;base64,<?php print createAvatar(getInitialsFromName($_SESSION['jmeno'])) ?>"/>
                </a>
            </div>
            <div class="card-body">
                <h6 class="card-category text-gray"><?php echo $_SESSION['ou']; ?></h6>
                <h2 class="card-title"><?php echo $_SESSION['jmeno']; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <h1>Editorské reporty</h1>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-primary">
                <div class="card-icon">
                    <i class="material-icons">account_balance</i>
                </div>
                <h4 class="card-title">Moje projekty
                </h4>
            </div>
            <div class="card-body">
                <div id="PieGraphPhase2Projects" class="ct-chart"></div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="card-category">Legenda</h6>
                    </div>
                    <div class="col-md-12">
                        <i class="fa fa-circle text-danger"></i> Záměr
                        <i class="fa fa-circle text-rose"></i> V přípravě
                        <i class="fa fa-circle text-warning"></i> Připraveno
                        <i class="fa fa-circle text-info"></i> V realizaci
                        <i class="fa fa-circle text-success"></i> Zrealizováno
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">date_range</i>
                </div>
                <h4 class="card-title">Přehled termínů u mých staveb v nadcházejících 14 dnech
                </h4>
            </div>
            <div class="card-body" id="termsTableHere">
                <table class="table table-striped" id="termsReportTable">

                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">view_timeline</i>
                </div>
                <h4 class="card-title">Přehled blížících se záruk
                </h4>
            </div>
            <div class="card-body" id="collaboratorTableHere">
                TODO
                <?php
                // getCollaboratorsTableForMe($_SESSION['username']);
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Posledních 20 změn na mých projektech
                </h4>
            </div>
            <div class="card-body" id="lastChangesTableHere">
                <table class="table table-striped" id="projectReportChangesTable">
                    <thead>
                    <tr>
                        <th scope="col">Datum a čas</th>
                        <th scope="col">Projekt</th>
                        <th scope="col">Událost</th>
                        <th scope="col">Operace</th>
                    </tr>
                    </thead>
                    <tbody id="projectReportChangesTBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <h1>Manažerské reporty</h1>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Uvidí distribuci projektů mezi editory (graf i počet)
                </h4>
            </div>
            <div class="card-body">
                <?php print_r(getStatsEditor2Projects()); ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Uvidí počet staveb dle fáze + informaci o zmeně
                </h4>
            </div>
            <div class="card-body">
                <?php echo getProjectCountsByPhase(); ?>
                Počet změn fází za poslední týden: <?php echo countPhaseChangesInInterval(7); ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Počet projektů + informace kolik jich přibylo
                </h4>
            </div>
            <div class="card-body">
                Počet projektů: <?php echo countActiveProjects(); ?>,<br>
                <?php echo countActiveProjects(7); ?> projektů přidáno za poslední týden,<br>
                <?php echo countActiveProjects(30); ?> projektů přidáno za poslední měsíc.
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Seznam bližících se záruk VŠECH projektů
                </h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Datum</th>
                        <th scope="col">ID Projekt</th>
                        <th scope="col">Projekt</th>
                        <th scope="col">Typ záruky</th>
                        <th scope="col">Akce</th>
                    </tr>
                    </thead>
                    <tbody id="projectReportChangesTBody">
                    <?php
                    $warranties = getUpcomingWarranties();
                    $warrantiesTable = "";
                    foreach ($warranties as $warranty) {
                        $warrantiesTable .= "<tr><td>".date("d. m. Y", strtotime($warranty["value"]))."</td><td>$warranty[idProject]</td><td>$warranty[projectName]</td><td>$warranty[deadlineName]</td><td><a href='detail.php?idProject=$warranty[idProject]'><i class=\"fa fa-sign-in-alt\" data-toggle=\"tooltip\" data-placement=\"left\" data-original-title=\"Přejít na detail projektu ID $warranty[idProject]\"></i></td></tr>";
                    }
                    echo $warrantiesTable;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Počet přihlášení uživatelů
                </h4>
            </div>
            <div class="card-body">
                Počet přihlášení za poslední týden: <?php echo count(getLastLogins(1000, 7)); ?>. <a href="nastaveni.php?sprava=logy#logins">Zobrazit detailní přehled přihlašování</a>.
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Výhled termínů pro nadcházející týden
                </h4>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="termsInNextWeekOverviewTable">
                    <thead>
                    <tr>
                        <th scope="col">Datum</th>
                        <th scope="col">Projekt</th>
                        <th scope="col">Projekt ID</th>
                        <th scope="col">Typ termínu</th>
                        <th scope="col">Akce</th>
                    </tr>
                    </thead>
                    <?php echo showTermsInNextWeek(7, NULL, FALSE); ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-rose">
                <div class="card-icon">
                    <i class="material-icons">edit_note</i>
                </div>
                <h4 class="card-title">Hodnota projektu + změna orpoti minulému období
                </h4>
            </div>
            <div class="card-body">
                Celková hodnota projektů: <?php echo countAllProjectPricesAsOf(); ?><br>
                Zvýšení hodnoty projektů za poslední týden: <?php echo countAllProjectPricesAsOf(7); ?>
            </div>
        </div>
    </div>
</div>


<?php
$customScripts = "";
$customScripts .= "
<script src=\"/js/reports.js\"></script>
";
?>


<?php include PARTS."endPage.inc"; ?>
