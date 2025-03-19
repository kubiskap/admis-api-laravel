<!-- Modal for ARES -->
<div class="modal fade" id="aresModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="aresModalTitle">Najít firmu v ARES MF ČR</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group md-form form-sm form-1 pl-0">
                                <label for="aresInput" class="bmd-label-floating">Zadejte IČO nebo název
                                    firmy</label>
                                <input type="text" name="aresInput" id="aresInput" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-primary input-group-btn" id="aresSearch">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pt-2 pb-2">
                    <div class="col-md-12 text-center">
                        <div id="resARES">Zde se objeví výsledky vyhledávání v ARES.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="errorMsg" style="color: red; width: 100%;" class="float-right"></div>
                        <button class="btn btn-light float-right" data-dismiss="modal">Zavřít</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for ARES update -->
<div class="modal fade" id="aresUpdateModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="aresModalTitle">Kontrola údajů firmy v ARES MF ČR</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row pt-2 pb-2">
                    <div class="col-md-12 text-center">
                        <div id="resUpdateARES">Kontroluji údaje v ARES...</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="errorMsg" style="color: red; width: 100%;" class="float-right"></div>
                        <button class="btn btn-light float-right" data-dismiss="modal">Zavřít</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>