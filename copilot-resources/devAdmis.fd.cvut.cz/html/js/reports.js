function loadReportTermsTable() {

    var request = $.ajax({
        url: "/ajax/getTermsOverview.php",
        method: "GET",
        data: {interval: 14, report: true},
        dataType: "html"
    });

    request.done(function (msg) {

        $("#termsReportTable").html(msg).DataTable({
            // "language": {
            //     "url": "/dashboard/assets/js/plugins/locale/Czech.json"
            // },
            "columnDefs": [
                { "orderable": false, "targets": 4 }
            ]
        });
    });

    request.fail(function (jqXHR, textStatus) {
        $("#termsReportTable").html("Nepodařilo se načíst termíny projektů :( Chyba: " + textStatus);
    });
}

loadReportTermsTable();

function loadReportChangesTable() {

    var request = $.ajax({
        url: "/ajax/getReportProjectChanges.php",
        method: "GET",
        dataType: "html"
    });

    request.done(function (msg) {

        $("#projectReportChangesTBody").html(msg);

        $("#projectReportChangesTable").DataTable({
            // "language": {
            //     "url": "/dashboard/assets/js/plugins/locale/Czech.json"
            // },
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });
    });

    request.fail(function (jqXHR, textStatus) {
        $("#termsReportTable").html("Nepodařilo se načíst změny projektů :( Chyba: " + textStatus);
    });
}

loadReportChangesTable();


// TODO: Ještě je potřeba nastavit, aby dala byla vždy jen pro daného uživatele
$.get("../ajax/getPieGraphPhase2Projects.php", function (data, status) {
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

    var PieGraphPhase2Projects = Chartist.Pie('#PieGraphPhase2Projects', dataPieGraphPhase2Projects, optionsPieGraphPhase2Projects);

    //start animation for the Emails Subscription Chart
    dashboard.startAnimationForPieChart(PieGraphPhase2Projects);
});