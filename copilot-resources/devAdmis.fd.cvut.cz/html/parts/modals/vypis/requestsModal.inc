<!-- Modal for Request Create and Update -->
<div class="modal fade" id="projectRequestUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg hugeModal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="projectRequestsUpdateTitle">Žádanka</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" value="" name="idRequest" id="requestModalHiddenlIdRequest">
                <input type="hidden" value="" name="idProject" id="requestModalHiddenlIdProject">
                <div class='form-group col-md-12' id="requestTypeDiv">
                    Typ žádanky
                    <div class='dropdown bootstrap-select show-tick dropup'>
                        <select class='selectpicker' id="requestModalRequestType"
                                data-style='select-with-transition'
                                name='idRequestType' required title='Vyberte typ žádosti'
                                tabindex='-98'
                        >
                        </select>
                    </div>
                </div>
                <div class='togglebutton' id='crosseusToggleButton'>
                     <label>
                         <input type='checkbox' id='crosseusToggle' checked disabled>
                         <span class='toggle' ></span>
                         <span style='color: #333'>Založit žádanku v systému CROSEUS</span>
                     </label>
                 </div>
                 <!--<div class="form-group">
                     <label for="cisloJednaci" class="bmd-label-floating">Číslo VZ:</label>
                     <input name="cisloJednaci" id="cisloJednaci" type="text" class="form-control" value="">
                 </div>-->
                <div class="form-group">
                    <div id="dynamicRequestFormHere"></div>
                </div>
            </div>
            <div class="modal-footer" id="requestModalFooterButtons">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Zavřít bez uložení</button>
                <button type="submit" form="projectRequestForm" id="saveRequest" class="btn btn-success">Uložit</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Request History Browsing -->
<div class="modal fade" id="requestHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg hugeModal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="requestHistoryModalTitle">Vývoj schvalování žádosti <span id="requestIdTitle"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="requestHistoryHere" class="row">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>