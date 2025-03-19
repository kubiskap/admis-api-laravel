<!-- Modal for Tasks Create and Update -->
<div class="modal fade" id="projectTasksUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="projectTasksUpdateTitle">Úkol</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="../submits/projectTaskSubmit.php" class="row" id="projectTaskForm" method="post">
                        <input type="hidden" value="" name="idTask" id="taskModalHiddenlIdTask">
                        <input type="hidden" value="" name="idProject" id="taskModalHiddenlIdProject">
                        <div class='form-group col-md-6'>
                            Stav
                            <div class='dropdown bootstrap-select show-tick dropup'>
                                <select class='selectpicker' id="taskModalStatus"
                                        data-style='select-with-transition'
                                        name='idTaskStatus' required title='Vyberte stav úkolu'
                                        tabindex='-98'>
                                    <?php echo selectTaskStatuses(); ?>
                                </select>
                            </div>
                        </div>
                        <div class='form-group col-md-6'>
                            Termín
                            <input type='text' name='deadline' id="taskModalDeadline" placeholder="Termín" class='form-control datetimepicker'>
                        </div>
                        <div class='form-group col-md-12'>
                            Název
                            <input type='text' placeholder="Název úkolu" id="taskModalName" name='name' required class='form-control'>
                        </div>
                        <div class='form-group col-md-12'>
                            Popis
                            <textarea placeholder="Vložte komentář" id="taskModalDescription" name='description' rows='5' class='form-control'></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Zavřít bez uložení</button>
                <button type="submit" form="projectTaskForm" id="saveTask" class="btn btn-success">Uložit</button>
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