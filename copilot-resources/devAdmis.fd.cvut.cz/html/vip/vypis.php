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
$title = 'Výpis';



$numberProjectsPage = (isset($_GET['projectsPerPage']))? $_GET['projectsPerPage'] : 10;
$numberOfProjects = getNumberOgFilteredProjects($_GET);
$numberOfPages = numberOfPages(getNumberOgFilteredProjects($_GET)[0], $numberProjectsPage);
if (isset($_GET['active'])){
    $active = $_GET['active'];
}else{
    $active = 1;
}
$strankovacEcho = strankovac(10, $active, $numberOfPages);

$buttonZobrazMoje = $_SESSION['role'] != 'view' ? "<button id='myProjectsFilter' class='btn btn-primary ml-auto''>Zobraz moje</button> ": '';


?>
<?php include PARTS."startPage.inc"; ?>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header card-header-danger card-header-text">
                                <div class="card-text">
                                    <h4 class="card-title">Filtrační parametry</h4>
                                </div>
                            </div>
                            <div class="card-body ">
                                <form method="get" id="filterForm" action="/" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row" >
                                                <div class="col">
                                                    <label style="width: 100%" for="idProject">Projekt
                                                <select id="idProject" class="select2 filterSelect ajax"  data-ajaxurl = "getSelectProjects.php" style="width: 100%" multiple="multiple" title="Vyberte projekty">

                                                        </select>
                                                    </label>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="editor">Osoba
                                                        <select id="editor" class="select2 filterSelect" style="width: 100%" multiple="multiple" title="Vyberte odpovědnou osobu" >
                                                            <?php echo selectActiveEditors();?>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="ou">Organizační jednotka
                                                        <select id="ou" class="select2 filterSelect" style="width: 100%" multiple="multiple" title="Vyberte organizační jednotku">
                                                            <?php echo selectActiveOus();?>
                                                        </select> </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="idProjectType">Druh stavby
                                                        <select id="idProjectType" class="select2 filterSelect"   style="width: 100%" multiple="multiple" title="Vyberte druh stavby" >
                                                            <?php echo selectFilterProjectTypes();?>
                                                        </select> </label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="idProjectSubtype">Specifikace
                                                        <select id="idProjectSubtype" class="select2 filterSelect" style="width: 100%" multiple="multiple" title="Vyberte specifikaci stavby" >
                                                            <?php echo selectFilterProjectSubtypes();?>
                                                        </select> </label>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="idPhase">Fáze
                                                        <select id="idPhase" class="select2 filterSelect" style="width: 100%" multiple="multiple">
                                                <?php echo selectPhases(); ?>
                                                            </select> </label>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="idCommunication">Komunikace
                                                        <select id="idCommunication" class="select2 filterSelect" style="width: 100%" multiple="multiple" title="Vyberte komunikace" >
                                                            <?php echo selectFilterRoads();?>
                                                        </select>
                                                    </label>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label style="width: 100%" for="idArea">Okres
                                                        <select id="idArea" class="select2 filterSelect" style="width: 100%" multiple="multiple" title="Vyberte okres" >
                                                            <?php echo selectFilterAreas();?>
                                                        </select>
                                                    </label>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                <label style="width: 100%" for="supervisorCompanyId">Dozor
                                                        <select id="supervisorCompanyId" class="select2 filterSelect " data-ajaxurl = "getCompanies.php"style="width: 100%" multiple="multiple" title="Vyberte dozor" >
                                                        </select>
                                                </label>
                                                    </div>
                                            </div>
                                            <div class="row">

                                                <div class="col">
                                                <label style="width: 100%" for="buildCompanyId">Zhotovitel stavby
                                                        <select id='buildCompanyId' class="select2 filterSelect " data-ajaxurl = "getCompanies.php" style="width: 100%" multiple="multiple" title="Vyberte firmu zhotovitele stavby" >

                                                        </select>
                                                </label>
                                                    </div>

                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                <label style="width: 100%" for="projectCompanyId">Zhotovitel projektu
                                                        <select id='projectCompanyId' class="select2 filterSelect ajax" data-ajaxurl = "getCompanies.php" style="width: 100%" multiple="multiple" title="Vyberte firmu zhotovitele projektu" >

                                                        </select>
                                                </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                <label style="width: 100%" for="idFinSource">Financování
                                                        <select id="idFinSource" class="select2 filterSelect" style="width: 100%" multiple="multiple" title="Vyberte zdroj financování" >
                                                            <?php echo selectFilterFinSource();?>
                                                        </select>
                                                </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <div class="col-md-3">
                                    <label style="width: 100%" for="projectsPerPage">Počet záznamů
                                    <select form="filterForm" class="select2 filterSelect" id="projectsPerPage"  name="numberProjectsPerPage" style="width: 100%">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    </label>
                                </div>
                                <div class="col-md-3">
                                    <label style="width: 100%" for="projectsOrder">Řazení
                                    <select form="filterForm" class="select2 filterSelect" id="projectsOrder"  name="projectsOrder"  style="width: 100%">
                                        <option value="priorita_desc">Priorita (sestupně)</option>
                                        <option value="priorita_asc">Priorita (vzestupně)</option>
                                        <option value="ID_desc">ID (sestupně)</option>
                                        <option value="ID_asc">ID (vzestupně)</option>
                                        <option value="faze_desc">Fáze (sestupně) </option>
                                        <option value="faze_asc">Fáze (vzestupně) </option>
                                    </select>
                                    </label>
                                </div>
                                <div>
                                    <?php echo $buttonZobrazMoje    ?>
                                    <button id="resetFilter" class="btn btn-light">Zobraz vše</button>
                                    <button id="dropdownSpreadsheetButton" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Export excel</button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownSpreadsheetButton">
                                        <h6 class="dropdown-header">Vyberte typ Excelu (pro vyhledané projekty)</h6>
                                        <a class="dropdown-item" id="spreadsheet" href="#">Projekty</a>
                                        <a class="dropdown-item" id="prioritiesSpreadsheet" href="#">Detail atributů priorit</a>
                                    </div>
                                    <button id="startFilter" class="btn btn-danger">Hledat</button>
                                </div>
                            </div>
                        </div>
                    </div>
                 <!--   <div class="col-lg-12 col-md-12">
                        <div class="d-flex justify-content-center">
                            <a href="newProject.php" class="btn btn-danger">
                                Nový projekt
                            </a>
                        </div>
                    </div>!-->
                    <div class="col-lg-12 col-md-12 mt-2">
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination ">
                                <?php
                                    echo $strankovacEcho;
                                ?>
                            </ul>
                        </nav>
                    </div>
                    <div id="projectList" class="col-lg-12 col-md-12">
                        <?php
                        print_r(generateProjectsListing(getFilteredProjects($_GET,$numberProjectsPage,$active)));
                        ?>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php
                                    echo $strankovacEcho;
                                ?>
                            </ul>
                        </nav>
                    </div>


<div class="skupinaModalu">

    <?php  includeFilesFromDirectory(PARTS."/modals/vypis/*.inc",TRUE) ?>
    <?php  includeFilesFromDirectory(PARTS."/modals/zadanky/*.inc",TRUE) ?>

</div>


<?php
$customScripts = "";
$customScripts .= "
<script src=\"/js/relationModal.js\"></script>
<script src=\"/js/priorityModal.js\"></script>
<script src=\"/js/suspensionModal.js\"></script>
<script src=\"/js/vypis.js\"></script>
<script src=\"/js/files.js\"></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js'></script>






";
?>



<?php include PARTS."endPage.inc"; ?>

