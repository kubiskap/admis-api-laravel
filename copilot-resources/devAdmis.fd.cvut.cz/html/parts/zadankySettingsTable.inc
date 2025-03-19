<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header card-header-danger card-header-icon">
                <div class="card-text">
                    <h4 class="card-title"><i class="material-icons">task</i> Typy žádanek a jejich nastavení
                    </h4>
                </div>
                <a href="nastaveni.php?sprava=zadanky&zadanka=new" class="btn btn-primary float-right plusButton" id="newZadanka" style="margin-top: 15px"><i class="fa fa-plus"></i>Přidat nový typ žádanky<div class="ripple-container"></div></a>
            </div>
            <div class="card-body">
                <?php getZadankyTable(); ?>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
</div>
