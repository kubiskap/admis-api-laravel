 /* GRAFY SE V ŽÁDANKÁCH ZATÍM NEPOUŽÍVAJÍ
 dashboard = {
        misc: {
            navbar_menu_visible: 0,
            active_collapse: true,
            disabled_collapse_init: 0
        },
        initRequestsCharts: function() {
            $.get("../ajax/getPieGraphRequestsPerStates.php", function (data, status) {
                dataPieGraphPhase2Projects = JSON.parse(data);

                var optionsPieGraphPhase2Projects = {
                    plugins: [
                        Chartist.plugins.tooltip()
                    ],
                    showPoint: false,
                    showLine: false,
                    showArea: true,
                    fullWidth: true,
                    showLabel: false,
                    axisX: {
                        showGrid: false,
                        showLabel: false,
                        offset: 0
                    },
                    axisY: {
                        showGrid: false,
                        showLabel: false,
                        offset: 0
                    },
                    chartPadding: 0,
                    low: 0
                };

                var PieGraphPhase2Projects = Chartist.Pie('#PieGraphRequestsPerState', dataPieGraphPhase2Projects, optionsPieGraphPhase2Projects);

                //start animation for the Emails Subscription Chart
                dashboard.startAnimationForPieChart(PieGraphPhase2Projects);
            });
        },
     startAnimationForPieChart: function(chart) {
     }
 };

    $( document ).ready(function() {
        setTimeout(function () {
            dashboard.initRequestsCharts();
        }, 500);


    });
  */


 $("#datatableZadanky").DataTable({
     // "language": {
     //     "url": "dashboard/assets/js/plugins/locale/Czech.json"
     // },
     "order": [[ 8, "desc" ]],
     responsive: true
 });

 $('[data-toggle="tooltip"]').on('mouseleave', function () {
     $('[data-toggle="tooltip"]').tooltip('hide');
     $('.tooltip').tooltip('dispose');
     $('[data-toggle="tooltip"]').tooltip();
 });
 $('[data-toggle="tooltip"]').tooltip();

 $(".show-request-form").bind('click', function (e) {
     var idRequest = $(this).attr('id-request');
     $("#requestModalHiddenlIdProject").val($(this).attr('project-id'));
     $("#requestModalHiddenlIdRequest").val(idRequest);
     $('#requestTypeDiv').hide();
     $('#saveRequest').hide();
     $('#crosseusToggleButton').hide();
     $('#projectRequestUpdateModal').modal('show');
     $('#dynamicRequestFormHere').html("<i class='fa fa-spinner fa-spin'></i>");
     $.ajax({
         url: '/ajax/getLastRequestVersion.php',
         type: 'POST',
         cache: false,
         data: {idRequest},
         success: function (lastProjectVersionData, status) {
             console.log(lastProjectVersionData);
             if (status === 'success') {
                 /*formRenderInstance = $('#dynamicRequestFormHere').html('').formRender({
                     dataType: 'json',
                     formData: data
                 });*/
                 $.ajax({
                     url: '/ajax/getRequestForm.php',
                     type: 'POST',
                     cache: false,
                     data: {idRequestType: lastProjectVersionData.idRequestType, idProject: lastProjectVersionData.idProject},
                     success: function (data1, status1) {
                         if (status1 === 'success') {
                             $('#dynamicRequestFormHere').html(data1);
                             $('#zdrojFinancePD > option').each(function() {
                                 this.value = this.text;
                             });
                             $('#zdrojFinanceStavba > option').each(function() {
                                 this.value = this.text;
                             });
                             $('#zdrojFinancePD').selectpicker();
                             $('#zdrojFinanceStavba').selectpicker();
                             $("#referenceZahrnujici").select2({
                                 tags: true
                             });
                             let referenceZahrnujici = [];
                             lastProjectVersionData.formData.forEach((element) => {
                                 if (element.value === 'on') {
                                     $("[name=" + element.name + "]").prop('checked', true);
                                 } else if (element.name === 'referenceZahrnujici[]') {
                                     referenceZahrnujici.push(element.value);
                                 } else {
                                     $("[name=" + element.name + "]").val(element.value);
                                 }
                             });
                             $('#zdrojFinancePD').selectpicker('refresh');
                             $('#zdrojFinanceStavba').selectpicker('refresh');
                             $('#referenceZahrnujici').val(referenceZahrnujici);
                             $('#referenceZahrnujici').trigger('change');
                         }
                     }
                 });
             }
         }
     });
 });

 $(document).on('click', '.croseus_status', function() {
     // Získá ID tlačítka
     let elementId = $(this).attr('extIdent');
     console.log("Going query for request with extId "+ elementId);
     // Extrakce části ID (např. 123)
    // let extractedPart = elementId ;
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
                     console.log(data);

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