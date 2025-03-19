<div class="row" >
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-danger">
                <div class="card-text">
                    <h4 class="card-title"><i class="material-icons">group</i> Přehled kontaktů firmy
                    </h4>
                </div>
                <button class="btn btn-primary float-right plusButton" id="newContact" data-toggle="modal" data-target="#contact" style="margin-top: 15px"><i class="fa fa-plus"></i>Přidat nový kontakt<div class="ripple-container"></div></button>
            </div>
            <div class="card-body" id="contactsTable">
                <?php getContactsTable(2) ?>
            </div>
        </div>
    </div>
</div>
