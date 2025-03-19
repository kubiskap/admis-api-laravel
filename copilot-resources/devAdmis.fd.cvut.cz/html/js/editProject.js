function generateSubtypesAjax($projectSubtype,val){
    returnValue = null;
    $.ajax({
        url: '/ajax/getSubtypesProject.php',
        type: "POST",
        cache: false,
        data: {
            projectType: val
        },
        success: function (data, status) {
            if (status === 'success') {
                $projectSubtype.prop('disabled', false);
                $projectSubtype.html(data);
                $projectSubtype.selectpicker("refresh");
            }
        }, error: function () {
            alert('CHYBA');
        }
    });

}

function generateSubtypesSelect(){
    const valSubtype = $("select[name='idProjectSubtype']").val();
    if(valSubtype == '') {
        const $projectSubtype = $("#idProjectSubtype");
        console.log('Refreshing subtypes select');
        const val = $("select[name='idProjectType']").val();
        if (val == 1 || val == 2 || val == 4) {
            $projectSubtype.val(null);
            generateSubtypesAjax($projectSubtype, val);

        } else {
            $projectSubtype.val(null);
            $projectSubtype.prop('disabled', true);
            $projectSubtype.selectpicker("refresh");
        }
    }

}

$(document).ready(function(){
    generateSubtypesSelect();
    $.getScript('../js/newProject.js');
    $.getScript('../js/formControls.js');


    $('[data-toggle="tooltip"]').on('mouseleave', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.tooltip').tooltip('dispose');
    });
    $('[data-toggle="tooltip"]').tooltip();


    $('#projectForm').submit(function (e) {
        e.preventDefault();
        console.log($('#projectForm')[0].checkValidity());
        if($('#projectForm')[0].checkValidity()){
            let formData = new FormData($('#projectForm')[0]);
            for (var pair of formData.entries()) {
                console.log(pair[0]+ ', ' + pair[1]);
            }
            $.ajax({
                url: '/submits/editProjectSubmit.php',
                type: "POST",
                cache: false,
                data: formData,
                contentType: false,
                processData: false,
                success: function (data,status) {
                    if (status === 'success' && $.isNumeric(data) == true) {
                        console.log(data);
                        swal({
                            title: "Hotovo",
                            showCancelButton: true,
                            confirmButtonText: 'Přejít na výpis',
                            cancelButtonText: 'Zůstat zde',
                            text: "Změny byly úspěšně uloženy, vrátit na výpis ? ",
                            type: "success"
                        })
                            .then(
                                result => {
                                    params = new URLSearchParams(uri.query())
                                    params.delete('idProject')
                                    params.delete('idProjectForEdit')

                                    window.location = 'vypis.php?' + params.toString();
                                },
                                dismiss => {
                                    window.location = 'editProject.php?idProjectForEdit='+ formData.get('idProject');
                                }
                            );
                    } else {
                        notify('bottom', 'right', 'danger', 'Někde se stala chyba, projekt nebyl uložen');
                        console.log(data);
                    }
                }, error: function () {
                    alert('CHYBA');
                }
            });
        }
    });

    var datesCounter = 0;

    $("#addDeadlineType").click(function(e) {
        var deadlineName = $("#deadlineTypesBox option:selected").text();
        var deadlineId = $("#deadlineTypesBox option:selected").val();
        $("#deadlines").append('     <div class="col-md-6" id="deadline'+datesCounter+'">\n' +
            '                            <div class="input-group form-control-lg">\n' +
            '                                <div class="input-group-prepend">\n' +
            '                                    <span class="input-group-text">\n' +
            '                                      <i class="material-icons">date_range</i>\n' +
            '                                    </span>\n' +
            '                                </div>\n' +
            '                                <div class="form-group col bmd-form-group">\n' +
            '                                    <span><span id="removedeadline'+datesCounter+'" deadline-id="'+datesCounter+'" deadline-type="'+deadlineId+'" deadline-name="'+deadlineName+'" class="close-button float-right">X\n' +
            '                                    </span></a>                                       \n' +
            '                                    <label class="bmd-label-static">'+deadlineName+'\n' +
            '                                    </label>                                       \n' +
            '                                    <input type="text" name="deadlines['+datesCounter+'][value]" required="" class="form-control datetimepicker dateEvidence" value="" style="">\n' +
            '                                    <input type="text" name="deadlines['+datesCounter+'][note]" placeholder="Poznámka" class="form-control dateEvidence" value="">\n' +
            '                                    <input type="hidden" name="deadlines['+datesCounter+'][idDeadlineType]" value="'+deadlineId+'">\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                        </div>');
        $("#removedeadline"+datesCounter).click(function(e) {
            var deadlineId = $(this).attr("deadline-id");
            $("#deadline"+deadlineId).remove();
        })
        datesCounter++;
        $("#noDeadline").hide();
        $('.datetimepicker').datetimepicker({format:'DD/MM/YYYY'});
    });
});

// Bootstrap autocomplete for Mapy.cz search
document.addEventListener('DOMContentLoaded', e => {
    $('#mapSearch').autoComplete({
        resolverSettings: {
            queryKey: 'query'
        },
        events: {
            searchPost: function (data) {
                // No modification needed in this example, but you can filter/sort here
                console.log(data);
                let items = [];
                data.items.forEach((item) => {
                    items.push({
                        value: item.position,
                        text: item.name
                    })
                })
                return items;
            },
        },
        renderItem: function(item, search) {
            // Customize how each item appears in the dropdown
            return `
        <div class="autocomplete-suggestion" data-value="${item.position}" data-category="${item.label}">
          ${item.name}
        </div>`;
        }
    })
});
$('#mapSearch').on('autocomplete.select', function(el, item){
    console.log(item);
    map.setView([item.value.lat, item.value.lon], 14);
})