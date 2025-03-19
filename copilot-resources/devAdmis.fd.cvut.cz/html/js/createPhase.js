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
    const $projectSubtype = $("#idProjectSubtype");
    console.log('Refreshing subtypes select');
    const val = $("select[name='idProjectType']").val();
    if (val == 1 || val == 2 || val == 4) {
        $projectSubtype.val(null);
        generateSubtypesAjax($projectSubtype,val);

    } else {
        $projectSubtype.val(null);
        $projectSubtype.prop('disabled', true);
        $projectSubtype.selectpicker("refresh");
    }

}

$(document).ready(function(){
    swal("Vyvolal jsi etapizaci projektu", "Právě jsi započal etapizaci projektu, vyplň údaje níže o nové etapě projektu. Dej pozor na ceny, v případě že chceš evidovat etapu a její cenu zvlášť, odečti ji od ceny celého projektu!");
    $.getScript('../js/newProject.js');
    $.getScript('../js/formControls.js');


    $('[data-toggle="tooltip"]').on('mouseleave', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.tooltip').tooltip('dispose');
    });
    $('[data-toggle="tooltip"]').tooltip();
    generateSubtypesSelect();
    $('#projectFormPhasing').submit(function (e) {

        e.preventDefault();
        console.log($('#projectFormPhasing')[0].checkValidity());
        if($('#projectFormPhasing')[0].checkValidity()){
            let formData = new FormData($('#projectFormPhasing')[0]);
            for (var pair of formData.entries()) {
                console.log(pair[0]+ ', ' + pair[1]);
            }

            $.ajax({
                url: '/submits/createPhaseProjectSubmit.php',
                type: "POST",
                cache: false,
                data: formData,
                contentType: false,
                processData: false,
                success: function (data,status) {
                    if (status === 'success') {
                        console.log(data);
                        console.log(status);
                        swal({
                            title: "Hotovo",
                            text: "Etapa byla úspěšně uložena. ",
                            type: "success"
                        })
                            .then(function(){
                                window.location.href = 'vypis.php?idProject=' + data;                            });
                    } else {
                        notify('bottom', 'right', 'danger', 'Někde se stala chyba, projekt nebyl uložen');
                        console.log(data);
                        console.log(status);
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
    $("#formSubmit").click(function(e) {

        if(!($('#projectFormPhasing')[0].checkValidity())){
            // console.log($(this).attr('title'))
            $.notify('Nejsou vyplněna všechna pole formuláře', { title: 'Chyba validace' });

        }
    });
});

