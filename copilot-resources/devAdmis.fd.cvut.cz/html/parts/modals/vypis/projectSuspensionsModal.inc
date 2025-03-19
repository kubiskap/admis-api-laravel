<!-- Modal for Suspensions Browsing -->
<div id="projectSuspensionsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Přerušení projektu</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <button class="btn btn-primary" style="float: right" id="newSuspensionButton" data-toggle="modal" data-target="#suspensionsUpdateModal"><i class="fa fa-plus"></i>  Přidat nové přerušení</button>
                    </div>
                    <table class="table" id="suspensionTable">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Původce</th>
                            <th scope="col">Důvod</th>
                            <th scope="col">Od</th>
                            <th scope="col">Do</th>
                            <th scope="col">Operace</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zavřít</button>
                </div>
        </div>
    </div>
</div>

<!-- Modal for Suspensions Update -->
<div class="modal fade" id="suspensionsUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="suspensionsUpdateTitle">Přerušení projektu</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="../submits/suspensionSubmit.php" class="row" id="suspensionForm" method="post">
                        <input type="hidden" value="" name="idProject">
                        <input type="hidden" value="" name="idSuspension">
                        <div class='form-group col-md-12'>
                            <div class='dropdown bootstrap-select show-tick dropup'>
                                <select class='selectpicker'
                                        data-style='select-with-transition'
                                        name='idSuspensionSource' data-live-search='true' required title='Vyberte původce přerušení stavby'
                                        tabindex='-98'>
                                    <?php echo selectSuspensionSources(); ?>
                                </select>
                            </div>
                        </div>
                        <div class='form-group col-md-12'>

                            <select class='selectpicker'
                                    data-style='select-with-transition'
                                    name='idSuspensionReason' data-live-search='true' required title='Vyberte důvod přerušení stavby'
                                    tabindex='-98'>
                                <?php echo selectSuspensionReasons(); ?>
                            </select>
                        </div>
                        <div class='form-group col-md-6'>
                            <input type='text' name='dateFrom' placeholder="Datum od" required class='form-control datetimepicker'>
                        </div>
                        <div class='form-group col-md-6'>
                            <input type='text' name='dateTo' placeholder="Datum do" required class='form-control datetimepicker'>
                        </div>
                        <div class='form-group col-md-12'>
                            Komentář
                            <textarea placeholder="Vložte komentář" name='comment' rows='5' class='form-control'></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Zavřít bez uložení</button>
                <input type="submit" form="suspensionForm" id="saveSuspension" class="btn btn-success" value="Uložit">
            </div>
        </div>
    </div>
</div>
