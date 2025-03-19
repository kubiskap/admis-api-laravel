$(document).ready(function () {

    $('#editProfileSubmit').bind('click',function (e) {
        e.preventDefault();
        $.post("/submits/editProfileSubmit.php",
            {
                editProfile: true,
                username: $("#editProfileForm input[name='username']").val(),
                email: $("input[name='email']").val(),
                name: $("input[name='name']").val(),
                idOu: $("select[name='idOu']").val(),
            },
            function(data, status){
                if(data === '1'  && status === 'success'){
                    notify('bottom','right','success','Údaje byly změněny')
                }else{
                    notify('bottom','right','danger','Někde se stala chyba údaje nebyly změněny')
                }
            });
    });

    $('#passwordChangeForm').validate({
        rules: {
            oldPass: {required:true,
                minlength: 6},
            newPass: {required:true,
                minlength: 6},
            newPassVerify: {
                equalTo: "#newPass",
                minlength: 6
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
        },
        success: function(element) {
            $(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
        },
        errorPlacement : function(error, element) {
            $(element).append(error);
        }
    });

    $('#changePasswordSubmit').bind('click',function (e) {
        e.preventDefault();
        if($('#passwordChangeForm').valid()){
            $.post("/submits/changePasswordSubmit.php",
                {
                    changePassword: true,
                    username: $("#passwordChangeForm input[name='username']").val(),
                    oldPass: $("input[name='oldPass']").val(),
                    newPass: $("input[name='newPass']").val(),
                    newPassVerify: $("input[name='newPassVerify']").val(),
                },
                function(data, status){
                    data = $.parseJSON(data);
                    console.log(data);
                    if(status === 'success' && data['status'] == true){
                        notify('bottom','right','success','Heslo bylo změněno, musíte se znovu přihlásit');
                        setTimeout(function(){window.location.replace(data['baseUrl'])},2000)
                    }else if(data['status'] == false){
                        notify('bottom','right','danger','Někde se stala chyba heslo nebyly změněno, zkontrolujte zadané údaje.')
                    }
                });
        }
    });

    $('#editorReport').change(function() {
        if(this.checked) {
            var returnVal = "ON";
            $(this).prop("checked", returnVal);
        } else {
            var returnVal = "OFF";
        }
        var request = $.ajax({
            url: "/ajax/updateEditorReportSettings.php",
            method: "POST",
            data: {editorReports: returnVal},
            dataType: "html"
        });

        request.done(function (msg) {
            if (msg == 1) {
                var notifyMessage = "Editorské emaily aktivovány";
                var notifyColor = "success";
            } else if (msg == 0) {
                var notifyMessage = "Editorské emaily vypnuty";
                var notifyColor = "success";
            } else {
                var notifyMessage = msg;
                var notifyColor = "warning";
            }
            notify('bottom', 'right', notifyColor, notifyMessage)
        });

        request.fail(function (jqXHR, textStatus) {
            notify('bottom-right', 'left', 'primary', textStatus)
        });
    });

    $('#idManagerReport').change(function() {

        var request = $.ajax({
            url: "/ajax/updateManagerReportSettings.php",
            method: "POST",
            data: {idManagerReport: this.value},
            dataType: "html"
        });

        request.done(function (msg) {
            if (msg == 'Nastavení manažerských emailoveých reportů změněno.') {
                var notifyMessage = msg;
                var notifyColor = "success";
            } else {
                var notifyMessage = msg;
                var notifyColor = "warning";
            }
            notify('bottom', 'right', notifyColor, notifyMessage)
        });

        request.fail(function (jqXHR, textStatus) {
            notify('bottom-right', 'left', 'primary', textStatus)
        });
    });

    $('#testEditorReport').click(function() {

        var request = $.ajax({
            url: "/ajax/sendTestMailReport.php",
            method: "POST",
            data: {reportType: 'editor'},
            dataType: "html"
        });

        request.done(function (msg) {
            if (msg == 1) {
                var notifyMessage = "Testovací email úspěšně odeslán.";
                var notifyColor = "success";
            } else {
                var notifyMessage = "Chyba při odesílání testovacího emailu.";
                var notifyColor = "warning";
            }
            notify('bottom', 'right', notifyColor, notifyMessage)
        });

        request.fail(function (jqXHR, textStatus) {
            notify('bottom-right', 'left', 'primary', textStatus)
        });
    });

    $('#testManagerReport').click(function() {

        var idManagerReport = $('#idManagerReport').val();
        var request = $.ajax({
            url: "/ajax/sendTestMailReport.php",
            method: "POST",
            data: {reportType: "manager", idManagerReport: idManagerReport},
            dataType: "html"
        });

        request.done(function (msg) {
            if (msg == 1) {
                var notifyMessage = "Testovací email úspěšně odeslán.";
                var notifyColor = "success";
            } else {
                var notifyMessage = "Chyba při odesílání testovacího emailu.";
                var notifyColor = "warning";
            }
            notify('bottom', 'right', notifyColor, notifyMessage)
        });

        request.fail(function (jqXHR, textStatus) {
            notify('bottom-right', 'left', 'primary', textStatus)
        });
    });
});