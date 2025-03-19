<!-- Modal for Contact update -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="contactModalTitle">Vyplňte údaje kontaktu</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="contactAddEditForm" token="<?php echo generateHash(date('H')); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="contactName">Jméno</label>
                            <input required type="text" class="form-control" id="contactName" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="phone">Telefon</label>
                            <input type="text" class="form-control" id="phone" value="">
                        </div>
                        <input type="hidden" name="idContact" id="idContact" value="neni">
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