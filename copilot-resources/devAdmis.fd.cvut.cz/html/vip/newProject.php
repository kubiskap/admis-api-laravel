<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 20.06.2018
 * Time: 15:37
 */
require_once __DIR__ . "/../../conf/conf.php";
require_once __DIR__ . "/../conf/config.inc";
require_once SYSTEMINCLUDES . "authenticateUser.php";
overUzivatele($pristup_zakazan);
$allowedRoles = ['editor', 'adminEditor'];
if(!in_array($_SESSION['role'], $allowedRoles)) die("Role: ". $_SESSION['role']. " -> Nedostatečná oprávnění k zobrazení.");

if(isset($_GET['test'])){
    $arr = ['idProject' => 764, 'idFinSource' => 1 ];
    echo Project::insertProject($arr,2);
    die();
}
$relationTypesSelects = '';
foreach (getRlationTypes() as $key => $relationType){
    $relationTypesSelects .= relationSelectsNew($relationType,$key);
}
$togglePriceButton = '';
switch ($_GET['projectType']){
    case 'lite':
        $title = "Nový projekt údržby (bez PD)";
        $togglePriceButton = "                      
                                <input type='hidden' value='0' class='toggleMergePrice' name='mergePricePDAD'>";
        $technologicalProjectType = 'lite';
        $priceTemplate = array(
            array(
                "idPriceType" => 6,
                "dph" => false,
                "class" => ''
            ));
        break;
    case 'namet':
        $title = "Nový námět projektu";
        $technologicalProjectType = 'topic';
        $togglePriceButton = "                            <div class='togglebutton'>
                                <label>
                                    <input type='checkbox' class='togglePrice' id='togglePrePricePDAD'>
                                    <span class='toggle'></span>
                                    Sloučená cena PD a AD
                                </label>
                                <input type='hidden' value='' class='toggleMergePrice' name='mergePricePDAD'>
                            </div>";
        $priceTemplate = array(
            array(
                "idPriceType" => 6,
                "dph" => false,
                "class" => ''
            ),array(
                "idPriceType" => 8,
                "dph" => false,
                "class" => ''
            ),array(
                "idPriceType" => 4,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 3,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 13,
                "dph" => false,
                "class" => 'prePricePDAD'
            )
        );
        break;

    case 'projekt':
        $title = "Nový projekt stavby";

        $technologicalProjectType = 'normal';
        $togglePriceButton = "                            <div class='togglebutton'>
                                <label>
                                    <input type='checkbox' class='togglePrice' id='togglePrePricePDAD'>
                                    <span class='toggle'></span>
                                    Sloučená cena PD a AD
                                </label>
                                <input type='hidden' value='' class='toggleMergePrice' name='mergePricePDAD'>
                            </div>";
        $priceTemplate = array(
            array(
                "idPriceType" => 6,
                "dph" => false,
                "class" => ''
            ),array(
                "idPriceType" => 8,
                "dph" => false,
                "class" => ''
            ),array(
                "idPriceType" => 4,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 3,
                "dph" => false,
                "class" => 'mergedPrice'
            ), array(
                "idPriceType" => 13,
                "dph" => false,
                "class" => 'prePricePDAD'
            )
        );
        break;

}

$priceHTML = '';
foreach ($priceTemplate as $key => $template) {
    $price = new Price\Price(null,$template['idPriceType'], getVat());
    $price->getLabel(false);
    $priceHTML .= "<div class='col-md-6'>{$price->formRepresenation($key, $template['dph'], $template['class'])}</div>";
}

$phaseId = $_GET['projectType'] == "namet" ? 6: 5;
?>
<?php include PARTS . "startPage.inc"; ?>


<div class="modal fade " id="modalMapa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">clear</i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <h4 class="modal-title col text-center">Vyberte souřadnice</h4>
                </div>
                <div class="row">
                    <div class="form-group col mb-2">
                        <input id="mapSearch" class="col form-control" type="text" value="" placeholder="Vyhledávání" data-url="https://api.mapy.cz/v1/suggest?lang=cs&limit=5&type=regional&apikey=cbbjbBrx1s8NIsHh4jwCCBgV_xNOJ952K5lU5a6OUP8" autocomplete="off" />
                    </div>
                </div>
                <div id="mapaLeaflet" style="height: 600px"></div>
                <div class="col-md-12 mt-2 text-center">
                    <button id="resetMarkers" class="btn btn-light">Začít znovu</button><button id="mapCopyGps" class="btn btn-danger">Kopírovat GPS</button>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="d-flex justify-content-center">
                        <h5><span id="souradniceMapa1"></span></h5>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="d-flex justify-content-center">
                        <h5><span id="souradniceMapa2"></span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade " id="modalZmenaStaniceni" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">clear</i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <h4 class="modal-title col text-center"><i class="material-icons">warning</i> Došlo ke změně trasy v mapě a staničení tak neodpovídá zadaným hodnotám ve formuláři:</h4>
                </div>
                <div class="row">
                    <div class="form-group col mt-1">
                        Původní staničení ve formuláři: <b><span id="staniceniOrigo"></span></b>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col mt-1">
                        Nové staničení vypočtené podle bodů v mapě: <b><span id="staniceniNew"></span></b>
                    </div>
                </div>
                <div class="col-md-12 mt-2 text-center">
                    <button id="keepStaniceni" class="btn btn-light">Zachovat původní staničení</button><button id="updateStaniceni" class="btn btn-danger">Upravit staničení dle mapy</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header card-header-danger card-header-text">
                <div class="card-text">
                    <h4 class="card-title"><?php echo $title ?></h4>
                </div>
            </div>
            <div class="card-body">
                <form method='post' action='/submits/newProjectSubmit.php' id="newProjectForm" name='newProject' class='form-horizontal'>
                    <input type="hidden" name="idPhase" value=<?php echo $phaseId ?> >
                    <input type="hidden" name="technologicalProjectType" value='<?php echo $technologicalProjectType ?>'>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>
                                Základní definice projektu
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="material-icons">import_contacts</i>
                                    </span>
                                </div>
                                <div class="form-group col">
                                    <label for="name" class="bmd-label-floating">Název projektu</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="material-icons">import_contacts</i>
                                    </span>
                                </div>
                                <div class="form-group col">
                                    <label for="subject" class="bmd-label">Předmět projektu</label><br>
                                    <textarea id='subject' rows="8" cols="50" name="subject" required class='form-control mt-auto'></textarea>

                                </div>
                            </div>
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="material-icons">build</i>
                                    </span>
                                </div>
                                <div class="form-group col">
                                    <div class="dropdown bootstrap-select show-tick dropup">
                                        <select required class="selectpicker" data-style="select-with-transition"
                                                name="idProjectType" id="idProjectType" data-live-search="true" title="Druh stavby"
                                                tabindex="-98">
                                            <?php echo selectProjectTypes(null); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="material-icons">build</i>
                                    </span>
                                </div>
                                <div class="form-group col">
                                    <div class="dropdown bootstrap-select show-tick dropup">
                                        <select required class="selectpicker" data-style="select-with-transition"
                                                name="idProjectSubtype" disabled id="idProjectSubtype" data-live-search="true"
                                                title="Specifikace druhu stavby" tabindex="-98">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php echo $relationTypesSelects ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>
                                Kontaktní osoba KSÚS
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">face</i>
                                    </span>
                                </div>
                                <div class="form-group col">
                                    <div class="dropdown bootstrap-select show-tick dropup">
                                        <select required class="selectpicker" data-style="select-with-transition"
                                                name="editor" data-live-search="true" title="Vyberte dozor"
                                                tabindex="-98">
                                            <?php echo selectEditors(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>
                                Lokalizace
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group form-control-md">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="material-icons">location_city</i>
                                    </span>
                                </div>
                                <div class="form-group col" id="areaForm">
                                    <span>Lokace</span>
                                    <div class="dropdown bootstrap-select show-tick dropup areaSelectWrap">
                                        <select class="selectArea" data-style="select-with-transition" required
                                                name="idArea[]" data-live-search="true" title="Vyberte okres"
                                                tabindex="-98">
                                            <?php echo selectArea(null); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <i id="addArea" class="material-icons pointer active">add</i>
                                <i id="removeArea" class="material-icons pointer not-active">remove</i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="communicationWrapper">
                                <div class="communicationFormGroup">
                                    <div class="input-group form-control-md">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                              <i class="material-icons">theaters</i>
                                            </span>
                                        </div>
                                        <div class="form-group col">
                                            <div class="dropdown bootstrap-select show-tick dropup">
                                                <select class="selectCommunication" data-style="select-with-transition"
                                                        name="communication[0][idCommunication]" required
                                                        data-live-search="true" title="Vyberte komunikaci"
                                                        tabindex="-98">
                                                    <?php echo selectRoads(null, "1,2"); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group form-control-md">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                              <i class="material-icons">navigation</i>
                                            </span>
                                        </div>
                                        <div class="form-group col">
                                            <label for="stationingFrom" class="bmd-label-floating">Staničení od</label>
                                            <input step="any" type="number" class="form-control stationingFrom"
                                                   name="communication[0][stationingFrom]" required>
                                        </div>
                                        <div class="form-group col">
                                            <label for="stationingTo" class="bmd-label-floating">Staničení do</label>
                                            <input step="any" type="number" class="form-control stationingTo"
                                                   name="communication[0][stationingTo]" required>
                                        </div>
                                    </div>
                                    <div class="input-group form-control-md">
                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                              <i class="material-icons">gps_fixed</i>
                                                            </span>
                                        </div>
                                        <div class="form-group col">
                                            <label class="bmd-label-floating">GPS E 1</label>
                                            <input type="number" id="gpsE_0" class="form-control gpsE1"
                                                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                                                   name="communication[0][gpsE1]" required>
                                        </div>
                                        <div class="form-group col">
                                            <label class="bmd-label-floating">GPS N 1</label>
                                            <input type="number" id="gpsN_0" class="form-control gpsN1"
                                                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                                                   name="communication[0][gpsN1]" required>
                                        </div>
                                    </div>
                                    <div class="input-group form-control-md">
                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                              <i class="material-icons">gps_fixed</i>
                                                            </span>
                                        </div>
                                        <div class="form-group col">
                                            <label class="bmd-label-floating">GPS E 2</label>
                                            <input type="number" id="gpsE2_0" class="form-control gpsE2"
                                                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                                                   name="communication[0][gpsE2]" required>
                                        </div>
                                        <div class="form-group col">
                                            <label class="bmd-label-floating">GPS N 2</label>
                                            <input type="number" id="gpsN2_0" class="form-control gpsN2"
                                                   pattern='[0-9]+([\.,][0-9]+)?' step='any'
                                                   name="communication[0][gpsN2]" required>
                                        </div>
                                    </div>
                                    <input type='hidden' id='allPoints_0' class='form-control allPoints' name='communication[0][allPoints]' value=''>
                                    <div class="m-4 btn btn-danger modalMapButton" data-toggle="modal"
                                         data-idOrderCommunication=0 data-num="0" data-target="#modalMapa">
                                        Mapa
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <i id="addCommunication" class="material-icons active">add</i>
                                <i id="removeCommunication" class="material-icons not-active">remove</i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>
                                Sdružené objekty
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group form-control-md">
                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="material-icons">gps_fixed</i>
                                                    </span>
                                </div>
                                <div class="form-group col">
                                    <div class="dropdown bootstrap-select show-tick dropup">
                                        <select disabled class="selectObject selectpicker"
                                                data-style="select-with-transition" id="objectSelect"
                                                name="selectObject" data-live-search="true" title="Vyberte typ objektu"
                                                tabindex="-98">
                                            <?php echo selectObjects(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <i id="addObject" class="material-icons not-active">add</i>
                        </div>
                    </div>
                    <div class="row" id="objectWrapper">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>
                                Finance
                            </h4>
                        </div>
                        <div class='col-md-12 d-flex align-items-center'>
                           <?php echo $togglePriceButton ?>
                        </div>
                        <?php print_r($priceHTML)?>

                    </div>

                    <div class='row'>
                        <div class='col-md-12'>
                            <h3>Předpokladané zdroje financování</h3>
                        </div>
                        <div class='col-md-12 d-flex align-items-center'>
                            <div class='col-md-6'>
                                <div class='input-group form-control-lg'>
                                    <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>account_balance</i>
                                </span>
                                    </div>
                                    <div class='form-group col'>
                                        <div class='dropdown bootstrap-select show-tick dropup'>
                                            <label>Stavba</label>
                                            <select class='selectpicker' data-style='select-with-transition' required
                                                    name='idFinSource' title='Předpoklad zdroje financování stavby' tabindex='-98'>
                                                <?php echo selectFinancialSources(null, false) ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class='input-group form-control-lg'>
                                    <div class='input-group-prepend'>
                                <span class='input-group-text'>
                                <i class='material-icons'>account_balance</i>
                                </span>
                                    </div>
                                    <div class='form-group col'>
                                        <div class='dropdown bootstrap-select show-tick dropup'>
                                            <label>PD</label>
                                            <select class='selectpicker' data-style='select-with-transition' required
                                                    name='idFinSourcePD' title='Předpoklad zdroje financování PD'
                                                    tabindex='-98'>
                                                <?php echo selectFinancialSources(null, true) ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class='row float-right'>
                        <div class='col'>
                            <input id="postNewProject" type="submit" value="Vytvořit projekt" class="btn btn-danger">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$customScripts = "";
$customScripts .= "<script>function showLoading() {
    $('#loading').modal('show');
}
</script>
<script src=\"/js/files.js\"></script>
<script src=\"/js/newProject.js\"></script>
";
?>


<?php include PARTS . "endPage.inc"; ?>

