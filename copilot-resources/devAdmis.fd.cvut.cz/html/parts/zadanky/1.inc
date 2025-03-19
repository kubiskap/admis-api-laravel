<form id="requestForm">
    <h2>
        ŽÁDOST O VYHLÁŠENÍ VEŘEJNÉ ZAKÁZKY (PD)
    </h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-icon card-header-danger">
                    <div class="card-text">
                        <h4 class="card-title"><i class="material-icons">task</i> <h4>Základní info projektu</h4>
                        </h4>
                    </div>
                </div>
                <input type="hidden" name="idProject" value="<?php echo $project['idProject']; ?>">
                <div class="card-body">
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="requestName" class="bmd-label-floating">Název veřejné zakázky:</label>
                            <input type="text" class="form-control"  id="requestName" name="requestName" required="" value="<?php echo $project['name']." - PD"; ?>">
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="projectSubject" class="bmd-label">Předmět díla:</label><br>
                            <textarea id="projectSubject"  rows="8" cols="50" name="projectSubject" required=""
                                      class="form-control mt-auto"><?php echo $project['subject']; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-icon card-header-danger">
                    <div class="card-text">
                        <h4 class="card-title"><i class="material-icons">task</i> Finance
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">account_balance</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="pricePDNoVat" class="bmd-label-floating">Předpoklad ceny PD bez DPH:</label>
                            <input type="text"   class="form-control" id="pricePDNoVat" name="pricePDNoVat" value="<?php echo $price->getValueFormated(false); ?>">
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                    <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">account_balance</i>
                    </span>
                    </div>
                    <div class="form-group col bmd-form-group">
                        <label for="pricePd" class="bmd-label-floating">Předpoklad ceny PD s DPH:</label>
                        <input type="hidden" name='vatRate' value="<?php echo getVat();?>">
                        <input type="text" readonly class="form-control" id="pricePd" name="pricePDVat" value="<?php echo $price->getValueFormated(true); ?>">
                    </div>
                </div>
                    <div class="nput-group form-control-lg">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" id="jeInvestice" name="jeInvestice">
                                <span class="toggle"></span>
                                Bude se jednat o investici?
                            </label>
                        </div>
                    </div>
                    <div class='input-group form-control-lg'>
                        <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>account_balance</i>
                        </span>
                        </div>
                        <div class='form-group col'>
                            <label>Zdroj financování PD
                            </label>
                            <select id='zdrojFinancePD' class='selectpicker' data-style='select-with-transition' required name='zdrojFinancePD' title='Vyberte druh financování' tabindex='-98'>
                                <?php echo selectFinancialSources($project->baseInformation['idFinSource']); ?>
                            </select>
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class='input-group-prepend'>
                        <span class='input-group-text'>
                          <i class='material-icons'>account_balance</i>
                        </span>
                        </div>
                        <div class='form-group col'>
                            <label>Zdroj financování příslušné stavby
                            </label>
                            <select id='zdrojFinanceStavba' required name='zdrojFinanceStavba' data-live-search='true' class='selectpicker' title='Zdroj financování příslušné stavby' data-style='select-with-transition'>
                                <?php echo selectFinancialSources($project->baseInformation['idFinSource']); ?>
                            </select>
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" id="spolufinancovanoSObci" name="spolufinancovanoSObci">
                                <span class="toggle"></span>
                                Spolufinancováno s obcí?
                            </label>
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" id="platbaPoFazich" name="platbaPoFazich">
                                <span class="toggle"></span>
                                Platební podmínky: platí se po fázích?
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-icon card-header-danger">
                    <div class="card-text">
                        <h4 class="card-title"><i class="material-icons">task</i> Požadavky a reference
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="input-group form-control-lg">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" id="pozadavekNaAutorskyDozor" name="pozadavekNaAutorskyDozor">
                                <span class="toggle"></span>
                                Požadavek na autorský dozor?
                            </label>
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" id="pozadavekNaMostniListAMostniProhlidku" name="pozadavekNaMostniListAMostniProhlidku">
                                <span class="toggle"></span>
                                Požadavek na Mostní list a I. mostní prohlídku?
                            </label>
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="togglebutton">
                            <label>
                                <input type="checkbox" id="budouVykupy" name="budouVykupy">
                                <span class="toggle"></span>
                                Budou výkupy?
                            </label>
                        </div>
                    </div>

                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="pocetReferenci" class="bmd-label-floating">Počet požadovaných referencí (musí být aspoň
                                1):</label>
                            <input type="number" min="1" class="form-control" id="pocetReferenci" name="pocetReferenci" required="">
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="pocetReferenciNovostavby" class="bmd-label-floating">Reference: služby v oblasti novostaveb</label>
                            <input type="number" min="0" class="form-control" id="pocetReferenciNovostavby" name="pocetReferenciNovostavby" required="">
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="referenceZahrnujici">Reference zahrnující (vyberte, příp. dopište vlastní)
                            <select class="form-control" multiple="multiple" id="referenceZahrnujici" name="referenceZahrnujici[]">
                                <option>2 pruhové pozemní komunikace</option>
                                <option>4 pruhové pozemní komunikace</option>
                                <option>most</option>
                                <option>most přes silniční komunikaci</option>
                                <option>most přes vodní tok</option>
                                <option>propustek</option>
                                <option>okružní křižovatku</option>
                                <option>sanace</option>
                                <option>opěrná zeď</option>
                            </select>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-icon card-header-danger">
                    <div class="card-text">
                        <h4 class="card-title"><i class="material-icons">task</i> Další informace
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="dalsiInfo" class="bmd-label-floating">Další informace:</label>
                            <input type="text" class="form-control" id="dalsiInfo" name="dalsiInfo" required="">
                        </div>
                    </div>
                    <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="material-icons">import_contacts</i>
                    </span>
                        </div>
                        <div class="form-group col bmd-form-group">
                            <label for="editor" class="bmd-label-floating">Odpovědná osoba (editor):</label>
                            <input type="text" class="form-control" id="editor" name="editor" required="" value="<?php echo $project['editor']; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
