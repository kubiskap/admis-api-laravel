$(document).ajaxComplete(function () {
    /*  $(".tooltip").tooltip("hide");
      $('.tooltip').tooltip('dispose');*/
    $('[data-toggle="tooltip"]').tooltip('dispose');
    setTimeout(function(){
    $('[data-toggle="tooltip"]').tooltip();
    }, 400);
/*$('[data-toggle="tooltip"]').on('mouseleave', function () {
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('.tooltip').tooltip('dispose');
});*/
});

function getInitialVal(element, id){
    var selectProject = element;
    var url = selectProject.data('ajaxurl');
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/ajax/'+ url +'?select=true&id=' + id
    }).then(function (data) {
        $.each(data, function() {
            console.log(this)
                var option = new Option(this.text, this.id, true, true);
                selectProject.append(option).trigger('change');
                // manually trigger the `select2:select` event
                selectProject.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });

        });

    });
}


$(document).ready(function () {
    $('.select2').select2({
        placeholder: 'Vyber možnost'
    });
$(".publishProject").bind('click', function (e) {
    buttonId = $(this).attr('id');
    buttonElement = $("#"+buttonId);
    console.log(buttonId)
    e.preventDefault();
    var endpoint = $(this).parent().attr('href');
    $.get((endpoint +"&currentState=true"), function (published, status) {
        if(!published) {
            swal({
                title: 'Publikovat',
                text: 'Chcete projekt publikovat do veřejného seznamu ?',
                type: 'info',
                showCancelButton: true,
                confirmButtonText: 'Publikovat',
                cancelButtonText: 'Zpět'
            }).then((result) => {
                if (result) {
                    $.get((endpoint+"&setState=1"), function (data, status) {
                        // console.log(data);
                        if (data == 1) {
                            buttonElement.html("public_off");
                            swal(
                                'Projekt publikován',
                                null,
                                'success'
                            )
                        } else {
                            swal(
                                'Chyba při zpracování požadavku',
                                null,
                                'error'
                            )
                        }
                    });
                }
            });
        }
        if(published) {
            swal({
                title: 'Zrušit publikování',
                text: 'Chcete zrušit publikování do veřejného seznamu?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ano zrušit',
                cancelButtonText: 'Zpět'
            }).then((result) => {
                if (result) {
                    $.get((endpoint+"&setState=0"), function (data, status) {
                        if (data == 1) {
                            buttonElement.html("public");
                            swal(
                                'Publikování zrušeno',
                                null,
                                'success'
                            )
                        } else {
                            swal(
                                'Chyba při zpracování požadavku',
                                null,
                                'error'
                            )
                        }
                    });
                }
            });
        }
    });
});
    $('#idProject').select2({
        ajax: {
            minimumInputLength: 3,
            url: '/ajax/getSelectProjects.php',
            delay: 200,
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true,
            placeholder: 'Search for a user...'
        }
    });
    $('.selectContact').select2({
        minimumInputLength: 3,
        placeholder: 'Search for a user...',
        ajax: {
            url: '/ajax/getContact.php',
            delay: 200,
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    select: true
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true,
        }
    });



    $('#buildCompanyId').select2({
        ajax: {
            url: '/ajax/getCompanies.php?idCompanyType=2',
            method: 'GET',
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: data
                };
            },
            placeholder: 'Vyberte zhotovitele',
            minimumInputLength: 3
        }
    });

    $('#supervisorCompanyId').select2({
        ajax: {
            url: '/ajax/getCompanies.php?idCompanyType=3',
            method: 'GET',
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: data
                };
            },
            placeholder: 'Vyberte projekt',
            minimumInputLength: 3
        }
    });

    $('#projectCompanyId').select2({
        ajax: {
            url: '/ajax/getCompanies.php?idCompanyType=1',
            method: 'GET',
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: data
                };
            },
            placeholder: 'Vyberte projekt',
            minimumInputLength: 3
        }
    });


    let uri = URI(window.location.href);
    $("#startFilter").bind('click', function () {
        $('select.filterSelect').each(function(){
            uri.removeSearch($(this).attr('id'));
            if($.isArray(($(this).val()))) {
                if ($(this).val() != "") {
                    uri.addSearch($(this).attr('id'), $(this).val().join(','));
                }
                var stateObj = {foo: "bar"};
                history.pushState(stateObj, "page 2", uri);
            }
            else{
                if ($(this).val() != "") {
                    uri.addSearch($(this).attr('id'), $(this).val());
                }
            }
        });
        uri.removeSearch('active');
        var stateObj = {foo: "bar"};
        history.pushState(stateObj, "page 2", uri);
        location.reload();

    });

$('#spreadsheet').bind('click',function () {
    $('select.filterSelect').each(function(){
        uri.removeSearch($(this).attr('id'));
        if ($(this).val()!="") {
            uri.addSearch($(this).attr('id'), $(this).val().join(','));
        }
    });
    const checkedBoxes = $('.phaseCheckbox:checked').map(function () {
        return $(this).val();
    }).toArray();

    if (checkedBoxes!="") {
        uri.addSearch('idPhase', checkedBoxes.join(','));
    }
    window.open('spreadsheet.php?'+uri.query());
});

$('#prioritiesSpreadsheet').bind('click',function () {
    $('select.filterSelect').each(function(){
        uri.removeSearch($(this).attr('id'));
        if ($(this).val()!="") {
            uri.addSearch($(this).attr('id'), $(this).val().join(','));
        }
    });
    const checkedBoxes = $('.phaseCheckbox:checked').map(function () {
        return $(this).val();
    }).toArray();

    if (checkedBoxes!="") {
        uri.addSearch('idPhase', checkedBoxes.join(','));
    }
    window.open('../spreadsheets/export-priorities-spreadsheet.php?'+uri.query());
});

$("#myProjectsFilter").bind('click', function () {
    var request = $.ajax({
        url: "/ajax/getUsername.php",
        method: "POST",
        dataType: "html"
    });
    request.done(function( msg ) {
        uri.removeSearch('supervisorCompanyId');
        uri.removeSearch('buildCompanyId');
        uri.removeSearch('projectCompanyId');
        uri.removeSearch('idCommunication');
        uri.removeSearch('idProjectType');
        uri.removeSearch('idProjectSubtype');
        uri.removeSearch('idProject');
        uri.removeSearch('idArea');
        uri.removeSearch('contactSupervisor');
        uri.removeSearch('contactBuildManager');
        uri.removeSearch('contactDesigner');
        uri.removeSearch('idFinSource');
        uri.removeSearch('idPhase');
        uri.removeSearch('editor');
        uri.removeSearch('active');
        uri.addSearch('editor', msg);
        var stateObj = {foo: "bar"};
        history.pushState(stateObj, "page 2", uri);
        location.reload();
    });
});

$("#resetFilter").bind('click', function () {
    uri.removeSearch('supervisorCompanyId');
    uri.removeSearch('buildCompanyId');
    uri.removeSearch('projectCompanyId');
    uri.removeSearch('idCommunication');
    uri.removeSearch('idProjectType');
    uri.removeSearch('idProjectSubtype');
    uri.removeSearch('idProject');
    uri.removeSearch('idArea');
    uri.removeSearch('contactSupervisor');
    uri.removeSearch('contactBuildManager');
    uri.removeSearch('contactDesigner');
    uri.removeSearch('idFinSource');
    uri.removeSearch('idPhase');
    uri.removeSearch('editor');
    uri.removeSearch('active');
    var stateObj = {foo: "bar"};
    history.pushState(stateObj, "page 2", uri);
    location.reload();
});

var filters = uri.search(true);
    $.each(filters, function (k, v) {
        var values = v.split(',');
        console.log(k);
        console.log(values);
        if ($("#" + k).data('ajaxurl')) {
            $.each(values, function () {
                getInitialVal($("#" + k), this);
            })
        }
        $("#" + k).val(values);
        $("#" + k).trigger('change.select2')
    });


$(".publishProject").bind('click', function (e) {
    e.preventDefault();
    swal({
        title: 'Publikování projektu',
        text: 'Chcete projekt publikovat do veřejného seznamu staveb ?',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Publikovat',
        cancelButtonText: 'Zrušit'
    }).then((result) => {
        if (result) {
            $.get($(this).parent().attr('href'), function (data, status) {
                if ($.isNumeric(data) && data > 0) {
                    swal(
                        'Projekt publikován',
                        null,
                        'success'
                    ).then(result => {
                        location.reload()
                    });
                }
                else {
                    swal(
                        'Chyba při zpracování požadavku',
                        null,
                        'error'
                    )
                }
                //$('#projectList').load("/ajax/vypis.php");
            });
        }
    });
});


$(".deactivateProject").bind('click', function (e) {
    e.preventDefault();
    swal({
        title: 'Jste si jistý?',
        text: 'Chystáte se smazat projekt!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Deaktivovat',
        cancelButtonText: 'Ponechat'
    }).then((result) => {
        if (result) {
            $.get($(this).parent().attr('href'), function (data, status) {
                if ( data > 0) {
                    swal(
                        'Záznam smazán!',
                        null,
                        'success'
                    ).then(result => {
                        location.reload()
                    });
                }
                if (data == 0) {
                    swal(
                        'Nemáte oprávnění ke smazání tohoto záznamu!',
                        null,
                        'error'
                    )
                }
                if (!$.isNumeric(data) || data < 0 || status == 'error') {
                    swal(
                        'Chyba při zpracování požadavku',
                        null,
                        'error'
                    );
                    // alert(data);
                }
                //$('#projectList').load("/ajax/vypis.php");
            });

        }
    });
});

$('.deadlineSP').each(function () {
    initCalendar($(this))
});

$('[data-toggle="tooltip"]').on('mouseleave', function () {
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('.tooltip').tooltip('dispose');
    $('[data-toggle="tooltip"]').tooltip();
});
$('[data-toggle="tooltip"]').tooltip();
$("#blokStavbaNew").show('slow');


$(document).on('click','.ukoly',function() {
    var idProject = $(this).attr('id').substring(5);
    $("#hiddenAssignmentsUpdateProjectId").val(idProject);
    $("#assignmentsUpdateData").val($(this).children("p").html());
    $("#assignmentsUpdateProjectId").html(idProject);
    $("#assignmentsUpdateModal").modal("show");
});

$(document).on('click','#saveAssignment',function() {
    var assignments = $("#assignmentsUpdateData").val();
    var idProject = $("#hiddenAssignmentsUpdateProjectId").val();
    $(this).html("<i class='fa fa-spinner fa-spin'></i>");
    var button = $(this);

    var request = $.ajax({
        url: "/ajax/updateAssignments.php",
        method: "POST",
        data: { idProject: idProject, assignments: assignments },
        dataType: "html"
    });

    request.done(function( msg ) {
        $("#ukoly"+idProject).children("p").html(assignments);
        button.html("<i class='fa fa-check'></i>");
        setTimeout(function() {
            $("#assignmentsUpdateModal").modal('hide');
            button.html("Uložit");
        }, 700);
    });

    request.fail(function( jqXHR, textStatus ) {
        button.html("<i class='fa fa-cross'></i>");
        setTimeout(function() {
            $("#assignmentsUpdateModal").modal('hide');
            button.html("Uložit");
        }, 1500);
    });
});
$.unblockUI();
});

function urldecode(url) {
return decodeURIComponent(url.replace(/\+/g, ' '));
}

$(document).on('click', '.croseus_status', function() {
    // Získá ID tlačítka
    let elementId = $(this).attr('extIdent');
    // Extrakce části ID (např. 123)
    // Zobrazení SweetAlert
    swal({
        title: 'Stav žádanky v CROSEUS',
        text: 'Chcete zjistit aktuální stav žádanky v systému CROSEUS?',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ano',
        cancelButtonText: 'Zrušit',
    }).then((result) => {
        if (result) {
            $.blockUI();
            $.get('../ajax/getCroseusRequestStatus.php?idProject='+elementId, function (data, status) {
                data = $.parseJSON(data);
                if (data.STAV_POPIS) {
                    $.unblockUI();
                    swal(
                        'Stav: '+ data.STAV_POPIS,
                        "Pro více informací klikni  <a href='" + data.LINK +"'>zde</a>",
                        'info'
                    ).then(result => {
                        location.reload()
                    });
                }
                else {
                    console.log(data);
                    console.log(data.STAV_POPIS);
                    $.unblockUI();
                    swal(
                        'Chyba při zpracování požadavku',
                        null,
                        'error'
                    )
                }
                //$('#projectList').load("/ajax/vypis.php");
            });
        }
    });
});