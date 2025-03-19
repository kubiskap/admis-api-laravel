window.load = function () {
    Loader.async = true;
    Loader.load(null, null, createMap);
};

function hideLoading() {
    $('#loading').modal('hide');
}



$(document).ajaxComplete(function () {
    /*  $(".tooltip").tooltip("hide");
      $('.tooltip').tooltip('dispose');*/
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="tooltip"]').on('mouseleave', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.tooltip').tooltip('dispose');
    });
});


$(document).ready(function () {
    // $("#loading").modal();

    $(".publishProject").bind('click', function (e) {
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
                                $(".publishProject").closest("i").html("public_off");
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
                                $(".publishProject").closest("i").html("public");
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
                    if (data > 0) {
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

    $calendars = $('.fullCalendar');

    today = new Date();

    $(".deadlineSP").bind('click', function () {
        let idProject = $(this).data('id');
        $calendar = $('#fullCalendar' + idProject);
        $calendar.fullCalendar({
            viewRender: function (view, element) {
            },
            height: 800,
            header: {
                left: 'title',
                center: 'month,agendaWeek,agendaDay,listYear',
                right: 'prev,next,today'
            },
            locale: 'cs',
            lang: 'cs',
            defaultView: 'listYear',
            defaultDate: today,
            selectable: true,
            selectHelper: true,
            views: {
                month: { // name of view
                    titleFormat: 'MMMM YYYY'
                    // other view-specific options here
                },
                week: {
                    titleFormat: " MMMM D YYYY"
                },
                day: {
                    titleFormat: 'D MMM, YYYY'
                }
            },

            editable: false,
            eventLimit: true, // allow "more" link when too many events
            events: {
                url: "/ajax/getDates.php",
                type: 'POST',
                data: {
                    idProject: idProject,
                    dates: true
                },
                error: function () {
                    alert('there was an error while fetching events!');
                }
            }
        });
    });
    $('[data-toggle="tooltip"]').on('mouseleave', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.tooltip').tooltip('dispose');
    });
    $('[data-toggle="tooltip"]').tooltip();
    $("#blokStavbaNew").show('slow');


    if(window.location.hash) {
        $(""+window.location.hash).show('slow');
    }
});

function urldecode(url) {
    return decodeURIComponent(url.replace(/\+/g, ' '));
}