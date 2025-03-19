<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 20.06.2018
 * Time: 15:37
 */
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);
$title = 'Detail';
$idProject = NULL;
$project = "";
$mapa = "";
/*$history = "
        <div class='col-lg-12 col-md-12'>
            <div class='card'>
                <div class='card-header card-header-icon card-header-rose'>
                    <div class='card-icon'>
                        <i class='material-icons'>assignment</i>
                    </div>
                    <h4 class='card-title '>Poslední změny</h4>
                </div>
                <div class='card-body'>
                    <div>
                        ".createHistoryTable(getArrActionsLogByInterval())."
                    </div>
                </div>
            </div>
        </div>";
$historyTable = createHistoryTable(getArrActionsLogByInterval());*/
if(isset($_GET['idProject']) && is_numeric($_GET['idProject'])){
    $idProject = $_GET['idProject'];

    if(Project::isActive($idProject)){
        $projectPhase = getProjectPhase($idProject)[0];
        $project = generateProjectsListing(getProject($projectPhase['idLocalProject']), true);
        $mapa = "
        <div class='col-lg-6 col-md-6'>
            <div class='card'>
                <div class='card-header card-header-icon card-header-success'>
                    <div class='card-icon'>
                        <i class='material-icons'>navigation</i>
                    </div>
                    <h4 class='card-title'>Mapa</h4>
                </div>
                <div class='card-body'>
                    <div id='leaflet-map' style='height:475px'>
                    </div>
                </div>
            </div>
        </div>";
        $history = "
        <div class='col-lg-6 col-md-6'>
            <div class='card'>
                <div class='card-header card-header-icon card-header-rose'>
                    <div class='card-icon'>
                        <i class='material-icons'>assignment</i>
                    </div>
                    <h4 class='card-title '>Poslední změny</h4>
                </div>
                <div class='card-body'>
                    <div >
                        ".createHistoryTable(getArrActionsLogByLimit4Project($idProject))."
                    </div>
                </div>
            </div>
        </div>";
    }else{
        $project = " ";
        $mapa = " ";
        $history = "
        <div class='col-lg-12 col-md-12'>
            <div class='card'>
                <div class='card-header card-header-icon card-header-rose'>
                    <div class='card-icon'>
                        <i class='material-icons'>assignment</i>
                    </div>
                    <h4 class='card-title '>Poslední změny</h4>
                </div>
                <div class='card-body'>
                    <div >
                    <h4>Nebyl nalezen žádný projekt</h4>                    
                    </div>
                </div>
            </div>
        </div>";
    }
} else {
    $history = "";
}

?>
<?php include PARTS."startPage.inc"; ?>
        <style>
            .box-mapa{
                position: absolute;
                top:0px;
                z-index:500;
                margin: 8px;
                padding: 10px;
                box-sizing:border-box;
                background-color: white;
                font-size: 13px;
                border: 1px solid #E0E0E0;
                border-radius: 2px;
                color:#6b7580;
            }
        </style>

        <div class="row">
            <div class="col-md-12">
                <form id="TypeValidation" class="form-horizontal" action="detail.php" method="GET">
                    <div class="card ">
                        <div class="card-header card-header-danger card-header-text">
                            <div class="card-text">
                                <h4 class="card-title">Hledat detail stavby</h4>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">ID stavby</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="idProject" number="true" required="true" value="<?php echo $idProject ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-danger">Hledat</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">

            <?php
                print_r($project);
            ?>

        </div>
        <div class="row">
            <?php
                print_r($history);
            ?>


            <?php
                print_r($mapa);
            ?>
        </div>

<div class="skupinaModalu">

    <?php  includeFilesFromDirectory(PARTS."/modals/vypis/*.inc",TRUE) ?>
    <?php  includeFilesFromDirectory(PARTS."/modals/zadanky/*.inc",TRUE) ?>

</div>

<?php
$customScripts = "";
$customScripts .= "";
?>


<?php include PARTS."endPage.inc"; ?>
<script src="/js/files.js"></script>
<script src="/js/detail.js"></script>
<script src="/js/relationModal.js"></script>
<script src="/js/suspensionModal.js"></script>
<script src="/js/priorityModal.js"></script>
<script src="/js/taskModal.js"></script>
<script src="https://formbuilder.online/assets/js/form-render.min.js"></script>

<script type="text/javascript">Loader.load()</script>
<script>

$(document).ready(function() {

    $('[data-toggle="tooltip"]').on('mouseleave', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.tooltip').tooltip('dispose');
    });
    $('[data-toggle="tooltip"]').tooltip();
    $("#blokStavbaNew").show('slow');

    let map;
    if($("#leaflet-map").length) {
        // ADMIS DEV MAP KEY (now works at *.admis.fd.cvut.cz)
        const API_KEY = 'cbbjbBrx1s8NIsHh4jwCCBgV_xNOJ952K5lU5a6OUP8';

        /*
        We create the map and set its initial coordinates and zoom.
        See https://leafletjs.com/reference.html#map
        */
        map = L.map('leaflet-map').setView([49.8729317, 14.8981184], 16);

        /*
        Then we add a raster tile layer with REST API Mapy.cz tiles
        See https://leafletjs.com/reference.html#tilelayer
        */
        const tileLayers = {
            'Základní Mapy.cz': L.tileLayer(`https://api.mapy.cz/v1/maptiles/basic/256/{z}/{x}/{y}?apikey=${API_KEY}`, {
                minZoom: 0,
                maxZoom: 19,
                attribution: '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
            }),
            'Turistická Mapy.cz': L.tileLayer(`https://api.mapy.cz/v1/maptiles/outdoor/256/{z}/{x}/{y}?apikey=${API_KEY}`, {
                minZoom: 0,
                maxZoom: 19,
                attribution: '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
            }),
            'Zimní Mapy.cz': L.tileLayer(`https://api.mapy.cz/v1/maptiles/winter/256/{z}/{x}/{y}?apikey=${API_KEY}`, {
                minZoom: 0,
                maxZoom: 19,
                attribution: '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
            }),
            'Letecká Mapy.cz': L.tileLayer(`https://api.mapy.cz/v1/maptiles/aerial/256/{z}/{x}/{y}?apikey=${API_KEY}`, {
                minZoom: 0,
                maxZoom: 19,
                attribution: '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
            }),
            'OpenStreetMap': L.tileLayer(`https://tile.openstreetmap.org/{z}/{x}/{y}.png`, {
                minZoom: 0,
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }),
        };

        tileLayers['Základní Mapy.cz'].addTo(map);
        L.control.layers(tileLayers).addTo(map);

        /*
        We also require you to include our logo somewhere over the map.
        We create our own map control implementing a documented interface,
        that shows a clickable logo.
        See https://leafletjs.com/reference.html#control
        */
        const LogoControl = L.Control.extend({
            options: {
                position: 'bottomleft',
            },

            onAdd: function (map) {
                const container = L.DomUtil.create('div');
                const link = L.DomUtil.create('a', '', container);

                link.setAttribute('href', 'http://mapy.cz/');
                link.setAttribute('target', '_blank');
                link.innerHTML = '<img src="https://api.mapy.cz/img/api/logo.svg" />';
                L.DomEvent.disableClickPropagation(link);

                return container;
            },
        });

        new LogoControl().addTo(map);

        points1 = [];
        let uri = URI(window.location.href);
        $.ajax({
            url: '/ajax/mapa.php',
            type: "POST",
            data: {
                idProject: uri.search(true).idProject
            },
            async: true,
            success: function (data) {
                const d = $.parseJSON(data);
                //console.log(d);
                var allLinesPoints = [];

                const svgIcon = L.divIcon({
                    html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="'+d.phaseColor+'" fill-rule="nonzero" d="M32 5a21 21 0 0 0-21 21c0 17 21 33 21 33s21-16 21-33A21 21 0 0 0 32 5zm6.84 30.46-6.82-3-6.8 3 .7-7.4L21 22.54l7.25-1.61L32 14.51l3.78 6.4L43 22.49l-4.9 5.57z"/></svg>',
                    className: '', // Removes the default divIcon styles
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                });

                for (const [i,coomunication] of d.communication.entries()) {
                    if (coomunication.allPoints) {
                        pointsArr = eval("[" + coomunication.allPoints.replaceAll("(", "[").replaceAll(")", "]") + "]");
                        body = [];
                        pointsArr.forEach(element => body.push([element[1], element[0]]));
                    } else {
                        body = [[coomunication.gpsN1, coomunication.gpsE1], [coomunication.gpsN2, coomunication.gpsE2]];
                    }

                    let showComSegment = false;
                    if (coomunication.name) {
                        showComSegment = showSelectedCommunication(coomunication.name, body);
                    }
                    var marker1 = L.marker(body[0], { icon: svgIcon }).addTo(map);
                    var marker2 = L.marker(body[body.length-1], { icon: svgIcon }).addTo(map);
                    var polyline = L.polyline(body, {color: 'red'}).addTo(map);

                    allLinesPoints.push(body);
                }

                // zoom the map to the polylines
                map.fitBounds(allLinesPoints);

            },
            error: function () {
                alert('CHYBA');
            }
        });
    }

    function showSelectedCommunication(selectedCommunicationNumber, allPoints) {
        let foundCom = false;
        $.ajax({
            url: 'https://staniceni.fd.cvut.cz/api/staniceni/road/'+selectedCommunicationNumber,
            type: "GET",
            dataType: 'json',
            success: function(res) {
                let seskupene = {};
                $.each(res, function(index, item) {
                    let usek = item.USEK_HASH;
                    if (!seskupene[usek]) {
                        seskupene[usek] = [];
                    }
                    seskupene[usek].push({ lat: item.X_COORD_WGS, lng: item.Y_COORD_WGS, staniceni: item.STANICENI_PREPOCET });

                });
                console.log(seskupene);
                let polylinePoints = [];
                for (let usek in seskupene) {
                    if (seskupene.hasOwnProperty(usek)) {
                        // Vytvoříme pole souřadnic pro polyline
                        polylinePoints.push(seskupene[usek]);
                    }
                }

                var polylineCommunication = L.polyline(polylinePoints, {color: 'grey'}).addTo(map);
                highlightSegment(polylineCommunication, allPoints[0], allPoints[allPoints.length-1]);
                foundCom = true;

            }, error: function () {
                // alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                var polyline = L.polyline(allPoints, {color: 'red'}).addTo(map);
                foundCom = false;
            }
        });
        return foundCom;
    }

    // Utility function to find the nearest point on a polyline
    function findNearestPoint(latlng, latlngs) {
        var nearestPoint = null;
        var minDistance = Infinity;

        latlngs.forEach(function(point) {
            var distance = map.distance(latlng, point);
            if (distance < minDistance) {
                minDistance = distance;
                nearestPoint = point;
            }
        });
        return nearestPoint;
    }

    // Highlight the selected segment
    function highlightSegment(line, start, end) {
        console.log(line.getLatLngs());

        latlngs = line.getLatLngs().flat();
        console.log(latlngs);
        point1 = findNearestPoint(start, latlngs);
        point2 = findNearestPoint(end, latlngs);
        var startIndex = latlngs.findIndex(p => p === point1);
        var endIndex = latlngs.findIndex(p => p === point2);

        if (startIndex > -1 && endIndex > -1 && startIndex !== endIndex) {
            var segment = latlngs.slice(Math.min(startIndex, endIndex), Math.max(startIndex, endIndex) + 1);
            var polylineSegmentHighlight = L.polyline(segment, {color: 'purple'}).addTo(map);
        }
    }

    $("#tableHistory").DataTable({
        // "language": {
        //     "url": "/dashboard/assets/js/plugins/locale/Czech.json"
        // },
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 7] }
        ],
        "order": [[ 4, "desc" ]],
        responsive: true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        }
    });

});


</script>


