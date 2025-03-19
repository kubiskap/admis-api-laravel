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
$title = 'Nastavení';
$hashToken = generateHash(date('H'));
?>
<?php include PARTS."startPage.inc"; ?>

<?php  if (isset($_GET['firma'])) { $company = getCompanyAll((int)$_GET['firma']); } ?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header card-header-danger card-header-text">
                        <div class="card-text">
                            <h4 class="card-title"><i class="material-icons">business</i> Údaje o firmě</h4>
                        </div>
                        <?php  if (!isset($_GET['firma']))
                                   echo'<button class="btn btn-primary float-right mt-3" data-toggle="modal" data-target="#aresModal"><i class="fa fa-search-plus"></i> Hledat firmu v ARES MF ČR</button>';
                               else
                                   echo '<button class="btn btn-primary float-right mt-3" data-toggle="modal" data-target="#aresUpdateModal" id="checkARES"><i class="fa fa-check"></i> Zkontrolovat údaje firmy v ARES MF ČR</button>';
                        ?>
                    </div>
                    <div class="card-body">
                        <form id="companyEditForm">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="name">Název</label>
                                    <input required class="form-control" id="name" placeholder="Příšerky s.r.o." value="<?php if (isset($company['name'])) echo $company['name']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Adresa</label>
                                <input required type="text" class="form-control" id="address" placeholder="Jeskyňářská 24, 120 00 Praha" value="<?php if (isset($company['address'])) echo $company['address']; ?>">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="ic">IČ</label>
                                    <input required type="text" class="form-control" id="ic" value="<?php if (isset($company['ic'])) echo $company['ic']; ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="dic">DIČ</label>
                                    <input type="text" class="form-control" id="dic" value="<?php if (isset($company['dic'])) echo $company['dic']; ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="www">Web</label>
                                    <input type="text" class="form-control" id="www" placeholder="http://www.priserky.cz" value="<?php if (isset($company['www'])) echo $company['www']; ?>">
                                </div>
                            </div>
                            <input type="hidden" id="idCompany" value="<?php if (isset($company['idCompany'])) echo $company['idCompany']; else echo 'neni'; ?>">
                        </form>
                    </div>
                    <div class="card-footer">
                        <button id="resetForm" class="btn btn-light ml-auto" onclick="location.reload();">Vrátit změny</button>
                        <button id="saveCompany" type="submit" form="companyEditForm" class="btn btn-danger">Uložit</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" <?php if (!isset($_GET['firma'])) { echo 'style="visibility: hidden"'; } ?>>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon card-header-danger">
                        <div class="card-text">
                            <h4 class="card-title"><i class="material-icons">group</i> Přehled kontaktů firmy
                            </h4>
                        </div>
                        <button class="btn btn-primary float-right plusButton" id="newContact" data-toggle="modal" data-target="#contactModal" style="margin-top: 15px"><i class="fa fa-plus"></i>Přidat nový kontakt<div class="ripple-container"></div></button>
                    </div>
                    <div class="card-body" id="contactsTable">
                        <?php if (isset($_GET['firma'])) { getContactsTable((int)$_GET['firma']); } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="skupinaModalu">
    <?php  includeFilesFromDirectory(PARTS."/modals/settings/company-settings/*.inc",TRUE) ?>
</div>

<?php
$customScripts = "";
$customScripts .= "
<script src=\"/js/nastaveni.js\"></script>
<script src=\"/js/nastaveniFirma.js\"></script>
<script type=\"text/javascript\">Loader.load()</script>
";
?>

<?php include PARTS."endPage.inc"; ?>

