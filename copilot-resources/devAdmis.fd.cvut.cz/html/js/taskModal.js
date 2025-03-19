$(document).ready(function () {
    $(".add-new-task").bind('click', function (e) {
        $("#taskModalHiddenlIdProject").val($(this).attr('project-id'));
        $("#taskModalHiddenlIdTask").val('');
        $("#taskModalStatus").val('1').selectpicker("refresh");
        $("#taskModalDeadline").val('');
        $("#taskModalName").val('');
        $("#taskModalDescription").val('');
    });

    $('#projectTaskForm').on('submit',function(e){
        e.preventDefault();
        let formData = new FormData($('#projectTaskForm')[0]);
        $("#saveTask").html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>").attr("disabled", 'disabled');
        $.ajax({
            url: '/submits/projectTaskSubmit.php',
            type: "POST",
            cache: false,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                if (status === 'success') {
                    if(data){
                        notify('bottom','right','success','Úkol byl uložen');
                        idProject = $("#taskModalHiddenlIdProject").val();
                        $.ajax({
                            url: '/ajax/getTasksForProject.php',
                            type: "POST",
                            cache: false,
                            data: {idProject},
                            success: function (data, status) {
                                if (status === 'success') {
                                    $("#tasks"+idProject).html(data);
                                    setTimeout(function(){ bindTaskButtons(); }, 200);
                                }
                            }
                        });
                        $('#projectTasksUpdateModal').modal('hide');
                    }else{
                        notify('bottom','right','danger','Úkol nebyl uložen');
                    }
                    // notify('bottom','right','success','Projekt byl uložen pod ID '+ data);
                } else {
                    notify('bottom','right','danger','Někde se stala chyba, úkol nebyl uložen');
                }
            }, error: function () {
                alert('CHYBA aaa');
            }, complete: function () {
                $("#saveTask").html("Uložit").removeAttr('disabled');
            }
        });
    });

    function bindTaskButtons() {
        $('.remove-task').on('click',function(e){
            var idTask = $(this).attr('task-id');
            var idProject = $(this).attr('project-id');
            removeTask(idTask, idProject);
        });
        $(".edit-task").bind('click', function (e) {
            var idTask = $(this).attr('task-id');
            $("#taskModalHiddenlIdProject").val($(this).attr('project-id'));
            $("#taskModalHiddenlIdTask").val(idTask);
            $("#taskModalStatus").val($(this).attr('status-id')).selectpicker("refresh");
            $("#taskModalDeadline").val($(this).attr('deadline'));
            $("#taskModalName").val($("#taskName"+idTask).html());
            $("#taskModalDescription").val($("#taskDescription"+idTask).html());
            $('#projectTasksUpdateModal').modal('show');
        });
        $(".task-history").bind('click', function (e) {
            var idTask = $(this).attr('task-id');
            $.ajax({
                url: '/ajax/getTaskHistory.php',
                type: "POST",
                cache: false,
                data: {idTask},
                success: function (data, status) {
                    if (status === 'success') {
                        $('#taskHistoryModal').modal('show');
                        $("#taskHistoryHere").html(data);
                    }
                }
            });
        });
        $('.add-task-comment').on('click',function(e){
            var idProject = $(this).attr('project-id');
            var idTask = $(this).attr('task-id');
            if ($("#newTaskComment"+idTask).length) {
                $("#newTaskCommentGroup"+idTask).remove();
            } else {
                var html = "                 <div class='input-group col-md-12' id=\"newTaskCommentGroup" + idTask + "\">\n" +
                    "                            <input type='text' placeholder=\"Napište komentář k úkolu\" id=\"newTaskComment" + idTask + "\" name='newTaskComment" + idTask + "' class='form-control'>\n" +
                    "                            <div class=\"input-group-append\">\n" +
                    "                               <button class=\"btn btn-sm btn-primary btn-just-icon\" id=\"newTaskCommentSubmitButton" + idTask + "\">\n" +
                    "                                  <i class=\"material-icons\">send</i>\n" +
                    "                                </span>\n" +
                    "                            </div>" +
                    "                        </div>";

                $('#taskReactions' + idTask).append(html);
                setTimeout(function(){ $("#newTaskComment"+idTask).focus().on('keyup', function (event) { if (event.key === "Enter") { submitTaskReaction(idTask, idProject, $("#newTaskComment"+idTask).val()); } }); },200);
                $("#newTaskCommentSubmitButton"+idTask).click( function () {
                    submitTaskReaction(idTask, idProject, $("#newTaskComment"+idTask).val());
                });
            }
        });
        $('.delete-reaction').on('click',function(e){
            var idProject = $(this).attr('project-id');
            var idTask = $(this).attr('task-id');
            var created = $(this).attr('created');
            removeTaskReaction(idTask, idProject, created);
        });
    }

    function submitTaskReaction(idTask, idProject, reaction) {
        console.log("Ukladam komentar k projektu " +idProject);
        $.ajax({
            url: '/submits/projectTaskReactionSubmit.php',
            type: "POST",
            data: {idTask, reaction},
            success: function (data, status) {
                if (status === 'success') {
                    if (data){
                        notify('bottom','right','success','Reakce k úkolu uložena');
                        $.ajax({
                            url: '/ajax/getTasksForProject.php',
                            type: "POST",
                            cache: false,
                            data: {idProject},
                            success: function (data, status) {
                                if (status === 'success') {
                                    console.log("Komentar k projektu ulozen " +idProject);
                                    $("#tasks"+idProject).html(data);
                                    setTimeout(function(){ bindTaskButtons(); }, 200);
                                }
                            }
                        });
                    } else {
                        notify('bottom','right','danger','Reakci k úkolu se nepodařilo uložit');
                    }
                    // notify('bottom','right','success','Projekt byl uložen pod ID '+ data);
                } else {
                    notify('bottom','right','danger','Reakci k úkolu se nepodařilo uložit');
                }
            }, error: function () {
                alert('Reakci k úkolu se nepodařilo uložit');
            }
        });
    }

    function removeTask(idTask, idProject) {
        swal({
            title: 'Smazat úkol',
            text: 'Opravdu chcete smazat úkol?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ano, smazat',
            cancelButtonText: 'Nechat být'
        }).then((result) => {
            if (result) {
                $.ajax({
                    url: '/ajax/deleteTask.php',
                    type: "POST",
                    data: {idTask: idTask},
                    success: function (data, status) {
                        if (status === 'success') {
                            if (data){
                                notify('bottom','right','success','Úkol byl smazán');
                                $.ajax({
                                    url: '/ajax/getTasksForProject.php',
                                    type: "POST",
                                    cache: false,
                                    data: {idProject},
                                    success: function (data, status) {
                                        if (status === 'success') {
                                            $("#tasks"+idProject).html(data);
                                            setTimeout(function(){ bindTaskButtons(); }, 200);
                                        }
                                    }
                                });
                            }else{
                                notify('bottom','right','danger','Úkol nebyl smazán');
                            }
                            // notify('bottom','right','success','Projekt byl uložen pod ID '+ data);
                        } else {
                            notify('bottom','right','danger','Někde se stala chyba, úkol nebyl smazán');
                        }
                    }, error: function () {
                        alert('Úkol nebyl smazán');
                    }
                });
            }
        });
    }

    function removeTaskReaction(idTask, idProject, created) {
        swal({
            title: 'Smazat reakci',
            text: 'Opravdu chcete smazat reakci?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ano, smazat',
            cancelButtonText: 'Nechat být'
        }).then((result) => {
            if (result) {
                $.ajax({
                    url: '/ajax/deleteTaskReaction.php',
                    type: "POST",
                    data: {idTask: idTask, created: created},
                    success: function (data, status) {
                        if (status === 'success') {
                            if (data){
                                notify('bottom','right','success','Reakce byla smazána');
                                $.ajax({
                                    url: '/ajax/getTasksForProject.php',
                                    type: "POST",
                                    cache: false,
                                    data: {idProject},
                                    success: function (data, status) {
                                        if (status === 'success') {
                                            $("#tasks"+idProject).html(data);
                                            setTimeout(function(){ bindTaskButtons(); }, 200);
                                        }
                                    }
                                });
                            }else{
                                notify('bottom','right','danger','Reakce nebyla smazána');
                            }
                            // notify('bottom','right','success','Projekt byl uložen pod ID '+ data);
                        } else {
                            notify('bottom','right','danger','Někde se stala chyba, reakce nebyla smazána');
                        }
                    }, error: function () {
                        alert('Reakce k úkolu se nepodařilo smazat');
                    }
                });
            }
        });
    }

    bindTaskButtons();
});