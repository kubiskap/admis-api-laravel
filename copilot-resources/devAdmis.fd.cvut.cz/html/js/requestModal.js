$(document).ready(function () {
    $(document).on('change', 'input[name="pricePDNoVat"]', function() {
            console.log('Price change detected');
            var pricePDNoVat = $(this).val().replace(/\s+/g, ''); // Odstranění mezer
            pricePDNoVat = parseFloat(pricePDNoVat);
            var vatRate = parseFloat($('input[name="vatRate"]').val());
            var pricePDVat = pricePDNoVat * (1+vatRate);
            $('input[name="pricePDVat"]').val(pricePDVat.toFixed(2));
        });

    function generateTableFromJson(jsonData) {
        let table = '<table border="1" cellpadding="10" cellspacing="0">';

        // Vytvoření řádku s klíči
        table += '<tr>';
        for (let key in jsonData) {
            table += `<th>${key}</th>`;
        }
        table += '</tr>';

        // Vytvoření řádku s hodnotami
        table += '<tr>';
        for (let key in jsonData) {
            table += `<td>${jsonData[key]}</td>`;
        }
        table += '</tr>';

        table += '</table>';
        return table;
    }

    var opt = {
        margin: 1,
        filename: 'json_data.pdf',
        image: {type: 'jpeg', quality: 0.98},
        html2canvas: {scale: 0.5}, // Zmenšení měřítka na 50%
        jsPDF: {unit: 'in', format: 'letter', orientation: 'portrait'}
    };

   /* document.getElementById('showRequestPdf').addEventListener('click', function () {
        var userData = $('#requestForm').serializeArray();
        const {jsPDF} = window.jspdf;
        const doc = new jsPDF();
        console.log(doc.getFontList());
        var fieldNames = {
            idProject: 'Admis ID projektu',
            projectName: 'Název projektu zakázky',
            projectSubject: 'Předmět díla',
            pricePDNoVat: 'Cena PD bez DPH',
            pricePDVat: 'Cena PD s DPH',
            zdrojFinancePD: 'Zdroj financování PD',
            zdrojFinanceStavba: 'Zdroj financování stavby',
            pocetReferenci: 'Počet referencí',
            pocetReferenciNovostavby: 'Počet referencí novostavby',
            referenceZahrnujici: 'Reference zahrnující',
            dalsiInfo: 'Další informace',
            editor: 'Editor'

            // Přidej další pole podle potřeby
        };

        let y = 20;
        userData.forEach(function (item) {
            if (item.value === 'on') {
                item.value = 'ANO';
            }
            var fieldName = fieldNames[item.name] || item.name; // Použij uživatelsky přívětivý název nebo původní název
            doc.setFontSize(12);
            doc.text(`${fieldName}:`, 10, y);
            y += 10; // Posuň y pozici pro hodnotu
            var splitText = doc.splitTextToSize(item.value, 180); // Rozděl text na více řádků
            doc.text(splitText, 10, y);
            y += splitText.length * 10; // Posuň y pozici podle počtu řádků
        });

        // Uložení PDF
        doc.save('form_data.pdf');
    });*/

    var formRenderInstance;

    function getRequestForm(idRequestType, idProject) {
        $.ajax({
            url: '/ajax/getRequestForm.php',
            type: 'POST',
            cache: false,
            data: {idRequestType, idProject},
            success: function (data, status) {
                if (status === 'success') {
                    $('#dynamicRequestFormHere').html(data);
                }
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
            }
        });
    }

    $('#projectRequestUpdateModal').on('shown.bs.modal', function (e) {
        if ($("#requestModalRequestType").val()) {
            console.log('formular uz ma vybrany typ zadanky (asi z minula)');
            var idRequestType = $("#requestModalRequestType").val();
            var idProject = $("#requestModalHiddenlIdProject").val();
            console.log(idRequestType);
            getRequestForm(idRequestType, idProject);
        }
    });

    $(".add-new-request").bind('click', function (e) {

        idPhase = $(this).attr('project-idPhase');
        console.log("Fetching requests types for idPhase "+ idPhase);
        $.ajax({
            url: '/ajax/getRequestsTypes.php',
            type: 'POST',
            cache: false,
            data: {idPhase},
            success: function (data, status) {
                if (status === 'success') {
                    console.log('Creating list of requests' + data);
                    $('#requestModalRequestType').html(data);
                    $("#requestModalRequestType").selectpicker("refresh");
                }
            }
        });

        $("#requestModalHiddenlIdProject").val($(this).attr('project-id'));
        $("#requestModalHiddenlIdRequest").val('');
        $("#requestModalStatus").val('1').selectpicker("refresh");
        $("#referenceZahrnujici").select2({
            tags: true
        });
        $("#requestModalDeadline").val('');
        $("#requestModalName").val('');
        $("#requestModalDescription").val('');
        $('#requestTypeDiv').show();
        $('#saveRequest').show();
        $('#dynamicRequestFormHere').html('');
        $('#crosseusToggleButton').show();
        $('#requestModalFooterButtons').show();
    });

    $(".edit-request").bind('click', function (e) {
        var idRequest = $(this).attr('request-id');
        $("#requestModalHiddenlIdProject").val($(this).attr('project-id'));
        $("#requestModalHiddenlIdRequest").val(idRequest);
        $('#requestTypeDiv').hide();
        $('#saveRequest').hide();
        $('#crosseusToggleButton').hide();
        $('#requestModalFooterButtons').hide();
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
                        data: {
                            idRequestType: lastProjectVersionData.idRequestType,
                            idProject: lastProjectVersionData.idProject
                        },
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

    $("#requestModalRequestType").change(function () {
        console.log('zmena formulare');
        var idRequestType = $(this).val();
        var idProject = $("#requestModalHiddenlIdProject").val();
        getRequestForm(idRequestType, idProject);
    });

        $("#saveRequest").click(function () {
        console.log('Ulozit zadanku button clicked');
        var userData = $('#requestForm').serializeArray(); //formRenderInstance.userData;
        $('#dynamicRequestFormHere').html("<i class='fa fa-spinner fa-spin'></i> Probíhá ukládání žádanky a komunikace se systémem CROSEUS...");
        $('#crosseusToggleButton').hide();
         $('#requestModalFooterButtons').hide();
        var idProject = $("#requestModalHiddenlIdProject").val();
        var idRequestType = $("#requestModalRequestType").val();
        var idFinSourceStavba = $('select[name="zdrojFinanceStavba"]').val();
        var idFinSourcePD = $('select[name="zdrojFinancePD"]').val();
        var idRequestType = $("#requestModalRequestType").val();
        var croseus = $("#crosseusToggle").prop("checked");
        var idRequestStatus = 2;
        console.log(idFinSourceStavba + ' ' + idFinSourcePD);
        console.log("Data:");
        console.log(userData);
        $.ajax({
            url: '/ajax/saveRequestFormJson.php',
            type: 'POST',
            cache: false,
            data: {idProject, idRequestType, croseus: croseus, userData: JSON.stringify(userData), idRequestStatus},
            success: function (data, status) {
                if (status === 'success') {
                    if (data) {
                        swal({
                            title: 'Uloženo',
                            text: 'Žádanka byla uložena a předána. Pokračujte odesláním ke schválení v systému CROSEUS',
                            type: 'success',

                        }).then((result) => {
                            $.ajax({
                                url: '/ajax/setFinancialSources.php',
                                type: 'POST',
                                cache: false,
                                data: {idProject, idFinSourceStavba: idFinSourceStavba, idFinSourcePD: idFinSourcePD},
                                success: function (data, status) {
                                    if (status === 'success') {
                                        console.log(idFinSourceStavba + ' ' + idFinSourcePD);
                                        console.log('Financial sources has been updated');
                                    }
                                    }
                            });
                            console.log(result);
                            $('#projectRequestUpdateModal').modal('hide');
                            // if (result) {
                            //     window.location.href = 'nastaveni.php?sprava=zadanky';
                            // } else {
                            //     if (data > 1) {
                            //         window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                            //     }
                            // }
                        }).catch((res) => {
                            console.log(res);
                            $('#projectRequestUpdateModal').modal('hide');
                            // if (data > 1) {
                            //     window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                            // }
                        });
                    }
                } else {
                    swal({
                        title: 'CHYBA',
                        text: 'Při ukládání žádanky došlo k chybě. Zkuste to znovu za chvíli, pokud to nepomůže, kontaktujte správce aplikace.',
                        type: 'error',

                    }).then((result) => {
                        console.log(result);
                        console.log(status);
                        console.log(data);
                        $('#projectRequestUpdateModal').modal('hide');
                        // if (result) {
                        //     window.location.href = 'nastaveni.php?sprava=zadanky';
                        // } else {
                        //     if (data > 1) {
                        //         window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                        //     }
                        // }
                    }).catch((res) => {
                        console.log(res);
                        $('#projectRequestUpdateModal').modal('hide');
                        // if (data > 1) {
                        //     window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                        // }
                    });
                }
            },
            error: function (data, status) {
                swal({
                    title: 'CHYBA',
                    text: 'Při ukládání žádanky došlo k chybě. Zkuste to znovu za chvíli, pokud to nepomůže, kontaktujte správce aplikace.',
                    type: 'error',
                }).then((result) => {
                    console.log(result);
                    console.log(status);
                    console.log(data);
                    $('#projectRequestUpdateModal').modal('hide');
                    // if (result) {
                    //     window.location.href = 'nastaveni.php?sprava=zadanky';
                    // } else {
                    //     if (data > 1) {
                    //         window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                    //     }
                    // }
                }).catch((res) => {
                    console.log(res);
                    $('#projectRequestUpdateModal').modal('hide');
                    // if (data > 1) {
                    //     window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                    // }
                });
            },
            complete: function (data, status) {
                // REFRESH TABULKY ZADANEK (Pokud existuje)
                if ( $('#requestsTableBody').length ) {
                    $.ajax({
                        url: '/ajax/getRequestsOverviewTableBody.php',
                        type: 'GET',
                        cache: false,
                        success: function (data, status) {
                            if (status === 'success') {
                                if (data) {
                                    $('#datatableZadanky').DataTable().destroy();
                                    $('#requestsTableBody').html(data);
                                    $("#datatableZadanky").DataTable({
                                        "order": [[ 8, "desc" ]],
                                        responsive: true
                                    });
                                }
                            }
                        }
                    });
                }
                console.log('finally refresh');
                if ( $('#requests'+idProject).length ) {
                    $.ajax({
                        url: '/ajax/getRequestsForProject.php',
                        type: 'POST',
                        cache: false,
                        data: {idProject: idProject},
                        success: function (data, status) {
                            if (status === 'success') {
                                if (data) {
                                    $('#requestsRow'+idProject).html(data);
                                }
                            }
                        }
                    });
                }
            }
        });
    });

    $(".add-request-reaction").bind('click', function (e) {
        rank = $(this).attr('request-rank');
        $.ajax({
            url: '/ajax/getRequestsStatuses.php',
            type: 'POST',
            cache: false,
            data: {rank},
            success: function (data, status) {
                if (status === 'success') {
                    console.log('Creating list of statuses' + data);
                    $('#requestAddReactionModalStatus').html(data);
                    $("#requestAddReactionModalStatus").selectpicker("refresh");
                }
            }
        });
        $("#requestAddReactionModalHiddenlIdReaction").val($(this).attr('id-request'));
        $("#requestAddReactionModalHiddenlIdProject").val($(this).attr('project-id'));
        $("#requestAddReactionModalComment").val('');
        $('#requestAddReactionModal').modal('show');
    });

    $("#saveRequestReaction").click(function () {
        console.log('Ulozit reakci k zadance button clicked');
        var idRequest = $("#requestAddReactionModalHiddenlIdReaction").val();
        var requestComment = $("#requestAddReactionModalComment").val();
        var idNewRequestStatus = $("#requestAddReactionModalStatus").val();
        $.ajax({
            url: '/ajax/saveRequestReaction.php',
            type: 'POST',
            cache: false,
            data: {idRequest, requestComment, idNewRequestStatus},
            success: function (data, status) {
                if (status === 'success') {
                    if (data) {
                        swal({
                            title: 'Uloženo',
                            text: 'Reakce k žádance byla uložena.',
                            type: 'success',

                        }).then((result) => {
                            console.log(result);
                            $('#requestAddReactionModal').modal('hide');
                            // if (result) {
                            //     window.location.href = 'nastaveni.php?sprava=zadanky';
                            // } else {
                            //     if (data > 1) {
                            //         window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                            //     }
                            // }
                        }).catch((res) => {
                            console.log(res);
                            $('#requestAddReactionModal').modal('hide');
                            // if (data > 1) {
                            //     window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                            // }
                        });

                        // REFRESH TABULKY ZADANEK (Pokud existuje)
                        if ( $('#requestsTableBody').length ) {
                            $.ajax({
                                url: '/ajax/getRequestsOverviewTableBody.php',
                                type: 'GET',
                                cache: false,
                                success: function (data, status) {
                                    if (status === 'success') {
                                        if (data) {
                                            $('#datatableZadanky').DataTable().destroy();
                                            $('#requestsTableBody').html(data);
                                            $("#datatableZadanky").DataTable({
                                                "order": [[ 8, "desc" ]],
                                                responsive: true
                                            });
                                        }
                                    }
                                }
                            });
                        }
                        var idProject = $("#requestAddReactionModalHiddenlIdProject").val();
                        if ( $('#requests'+idProject).length ) {
                            $.ajax({
                                url: '/ajax/getRequestsForProject.php',
                                type: 'POST',
                                cache: false,
                                data: {idProject: idProject},
                                success: function (data, status) {
                                    if (status === 'success') {
                                        if (data) {
                                            $('#requests'+idProject).html(data);
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
            }
        });
    });

});
