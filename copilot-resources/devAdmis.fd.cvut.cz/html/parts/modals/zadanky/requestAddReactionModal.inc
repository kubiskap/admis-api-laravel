<!-- Modal for Tasks Create and Update -->
<div class="modal fade" id="requestAddReactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="requestAddReactionFormTitle">Reakce k žádance</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form class="row" id="requestAddReactionForm" method="post">
                        <input type="hidden" value="" name="idRequest" id="requestAddReactionModalHiddenlIdReaction">
                        <input type="hidden" value="" name="idProject" id="requestAddReactionModalHiddenlIdProject">
                        <div class='form-group col-md-6'>
                            Stav
                            <div class='dropdown bootstrap-select show-tick dropup'>
                                <select class='selectpicker' id="requestAddReactionModalStatus"
                                        data-style='select-with-transition'
                                        name='idReactionStatus' required title='Vyberte stav žádanky'
                                        tabindex='-98'>
                                </select>
                            </div>
                        </div>
                        <div class='form-group col-md-12'>
                            Popis
                            <textarea placeholder="Vložte komentář" id="requestAddReactionModalComment" name='description' rows='5' class='form-control'></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Zavřít bez uložení</button>
                <button type="button" form="requestAddReactionForm" id="saveRequestReaction" class="btn btn-success">Uložit</button>

            </div>
        </div>
    </div>
</div>

<!-- Modal for Task History Browsing -->
<div class="modal fade" id="taskHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg hugeModal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="taskHistoryModalTitle">Vývoj řešení úkolu <span id="taskIdTitle"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="taskHistoryHere" class="row">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>