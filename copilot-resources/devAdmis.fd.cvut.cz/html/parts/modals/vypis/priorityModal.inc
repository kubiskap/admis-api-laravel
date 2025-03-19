<!-- Modal for Adding Deadline Types -->
<div class="modal fade " id="projectPrioritymodal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="../submits/editPriority.php" id="priorityForm" method="post">
            <div class="modal-header">
                <h3 class="modal-title" id="priorityModalTitle">Priorita projektu</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="idProject">
                <h4>Zvolte atributy priority:</h4>
                <div class="form-group row">
                    <?php
                    require_once(CLASSES. "Enums.php");

                    foreach (Priorita_selecty::SELECT as $eachSelect) {

                        $className = "Priorita_" . $eachSelect;
                        if(class_exists($className,false)) {
                            echo "<div class='form-group col-4'>";
                            $selectedValue = null;
                            print_r(createSelect($className::VALUES, $className::FORM_NAME, $className::NAME, $selectedValue, false, 'prioritySelecty required'));
                            echo "</div>";
                            }
                    }
                    ?>
                    <div class='form-group col-4'>
                        <br>
                        <ul class='list-unstyled align-self-center' id="priorityResultModal">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Zavřít</button>
                <input class="btn btn-success" type="submit" value="Uložit">
            </div>
            </form>
        </div>
    </div>
</div>
