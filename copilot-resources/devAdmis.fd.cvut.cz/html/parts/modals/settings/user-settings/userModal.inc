<!-- Modal for User update -->
<div class="modal fade" id="user" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="userModalTitle">Vyplňte údaje uživatele</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userAddEditForm" token="<?php echo generateHash(date('H')); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="name">Jméno</label>
                            <input required type="text" class="form-control" id="name" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="username">Uživatelské jméno</label>
                            <input disabled required type="text" class="form-control" id="username" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="idOu" class="bmd-label-floating"></label>
                            <select name="idOu" id="idOu" class="selectpicker" data-style="select-with-transition"
                                    data-live-search="true"
                                    title="Vyberte organizační jednotku" tabindex="-98">
                                <?php echo selectOu(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="idRoleType" class="bmd-label-floating"></label>
                            <select name="idRoleType" id="idRoleType" class="selectpicker" data-style="select-with-transition"
                                    data-live-search="true"
                                    title="Vyberte roli" tabindex="-98">
                                <?php echo selectRoleTypes(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="errorMsg" style="color: red; width: 100%;" class="float-right"></div>
                            <button class="btn btn-light" data-dismiss="modal">Zavřít</button>
                            <button class="btn btn-success float-right" type="submit">Uložit</button>
                        </div>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>