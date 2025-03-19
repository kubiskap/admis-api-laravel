$('#projectPrioritymodal').on('click',function(e){
    $("#priorityForm").validate(
        ({
            submitHandler: function() {
                $('select').prop('disabled', false);
                formData = new FormData($('#priorityForm')[0]);
                $('select').prop('disabled', true);

                $.ajax({
                    url: '/submits/editPriority.php',
                    type: "POST",
                    cache: false,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data, status) {
                        if (status === 'success') {
                            console.log(data)
                            var vys = JSON.parse(data);
                            var skore = Math.round(vys['priorityScore'] * 100) / 100
                            var correction = Math.round(vys['correctionValue'] * 100) / 100
                            var maxScore = Math.round(vys['maxScore'] * 100) / 1000
                            var projectId = vys['projectId']
                            $("#priorityScoreCard"+projectId).html(skore)
                            $("#priorityCorrectionCard"+projectId).html(correction)
                            var htmlResult = "<li>Skóre: " + skore +"</li><li>Maximální: " + maxScore+"</li><li>Po korekci: " + correction +"</li>";
                            $("#priorityResultModal").html(htmlResult)
                            notify('bottom','right','success','Prioritizace spočítaná');
                        } else {
                            notify('bottom','right','danger','Někde se stala chyba, relace nebyly uloženy');
                        }
                    }, error: function () {
                        alert('CHYBA aaa');
                    }
                });

            }
        })

 )
});



$('#projectPrioritymodal').on('show.bs.modal', function(e) {
    console.log('initializing modal priority')
    $('.prioritySelecty').selectpicker('deselectAll');
    $("option:selected").removeAttr("selected");
    $('.prioritySelecty option').prop("selected", false);
    $('.prioritySelecty').selectpicker('refresh');

    var projId = $(e.relatedTarget).data('id');
    $("#priorityForm input[name='idProject']").val(projId);
    var atts = urldecode($(e.relatedTarget).data('atts'));
    var allowedatts = urldecode($(e.relatedTarget).data('allowedatts'));
    if (allowedatts !== '') {
        const jsonAtts = JSON.parse(allowedatts);
        Object.entries(jsonAtts).forEach(([key, value]) => {
            console.log('disabling atts for priority');
            if(!value){
                swal("Parametry prioritizace","Pro projekt v této fázi, jsou nedostupné některé parametry prioritizace, při změně fáze tyto parametry zaktualizuj!");
                $('select[name="' + `${key}` + '"]').val(0).change();
                $('select[name="' + `${key}` + '"]').prop("disabled", true);
            }
        });
    }

    if (atts !== '') {
        const json = JSON.parse(atts);
        Object.entries(json).forEach(([key, value]) => {
            console.log('selecting score values');

                $('select[name="' + `${key}` + '"]').val(value).change();
        });
    }
});

