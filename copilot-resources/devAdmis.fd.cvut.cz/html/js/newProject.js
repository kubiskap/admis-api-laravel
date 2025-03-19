window.load = function () {
    Loader.async = true;
    // Loader.load(null, {suggest: true}, createMap);
};

$(document).on('focusin', function(e) {
    if ($(e.target).closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
        e.stopImmediatePropagation();
    }
});

let numCommunications = ($('.selectCommunication').length) - 1;
console.log(numCommunications);
let numAreas = ($('.selectArea').length) - 1;
console.log(numAreas);
let openCommunicationModal = 0;
let selectedCommunicationNumber = null;

// LEAFLET MAP

    let map;
    if ($("#mapaLeaflet").length) {
        // ADMIS DEV MAP KEY (now works at *.admis.fd.cvut.cz)
        const API_KEY = 'cbbjbBrx1s8NIsHh4jwCCBgV_xNOJ952K5lU5a6OUP8';

        /*
        We create the map and set its initial coordinates and zoom.
        See https://leafletjs.com/reference.html#map
        */
        map = L.map('mapaLeaflet').setView([50.0490114, 14.4783250], 10);

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

        let $gpsE = $('#gpsE_' + openCommunicationModal);
        let $gpsN = $('#gpsN_' + openCommunicationModal);
        let $gpsE2 = $('#gpsE2_' + openCommunicationModal);
        let $gpsN2 = $('#gpsN2_' + openCommunicationModal);
        let $allPoints = $('#allPoints_' + openCommunicationModal);

        let gpsEVal = ($gpsE.val() != '') ? $gpsE.val() : 14.2943039;
        let gpsNVal = ($gpsN.val() != '') ? $gpsN.val() : 50.0349000;
        let gpsE2Val = ($gpsE2.val() != '') ? $gpsE2.val() : 14.6019211;
        let gpsN2Val = ($gpsN2.val() != '') ? $gpsN2.val() : 49.9801789;


        const modalGps1 = $('#souradniceMapa1');
        const modalGps2 = $('#souradniceMapa2');
        modalGps1.html('');
        modalGps2.html('');

        // Create an empty layer group to hold the markers
        const markersLayer = L.layerGroup().addTo(map);

        // Create a polyline to connect the markers
        let polylineCommunication = L.polyline([], {color: 'grey'}).addTo(map);
        let polylineSegmentHighlight = L.polyline([], {color: 'purple'}).addTo(map);
        let polyline = L.polyline([], {color: 'red'}).addTo(map);

        // Add already created markers (if any)
        function updateFromForm() {
            if ($allPoints.val() != '') {
                pointsArr = eval("[" + $allPoints.val().replaceAll("(", "[").replaceAll(")", "]") + "]");
                pointsArr.forEach(point => {
                    // Create a new draggable marker at the clicked position
                    const marker = L.marker([point[1], point[0]], {draggable: true}).addTo(markersLayer);

                    // Add a dragend event to log the new position
                    marker.on('dragend', function (e) {
                        const newLatLng = e.target.getLatLng();
                        const positions = getAllMarkerPositions(markersLayer);
                    });
                });
                const positions = getAllMarkerPositions(markersLayer);
                console.log("All marker positions:", positions);
                map.fitBounds(positions);
            } else if (($gpsE.val() != '') && ($gpsN.val() != '') && ($gpsE2.val() != '') && ($gpsN2.val() != '')) {
                // Create a new draggable marker at the clicked position
                const marker1 = L.marker([gpsNVal, gpsEVal], {draggable: true}).addTo(markersLayer);

                // Add a dragend event to log the new position
                marker1.on('dragend', function (e) {
                    const newLatLng = e.target.getLatLng();
                    console.log(`Marker moved to: ${newLatLng.lat}, ${newLatLng.lng}`);
                    const positions = getAllMarkerPositions(markersLayer);
                    console.log("All marker positions:", positions);
                });

                // Create a new draggable marker at the clicked position
                const marker2 = L.marker([gpsN2Val, gpsE2Val], {draggable: true}).addTo(markersLayer);

                // Add a dragend event to log the new position
                marker2.on('dragend', function (e) {
                    const newLatLng = e.target.getLatLng();
                    console.log(`Marker moved to: ${newLatLng.lat}, ${newLatLng.lng}`);
                    const positions = getAllMarkerPositions(markersLayer);
                    console.log("All marker positions:", positions);
                });

                const positions = getAllMarkerPositions(markersLayer);
                console.log("All marker positions:", positions);
                map.fitBounds(positions);
            }
            if ($allPoints.val() == '' && ($gpsE.val() == '') && ($gpsN.val() == '') && ($gpsE2.val() == '') && ($gpsN2.val() == '') && $('[name="communication[' + openCommunicationModal + '][stationingFrom]"]').val() != "" && $('[name="communication[' + openCommunicationModal + '][stationingTo]"]').val() != "" && selectedCommunicationNumber && selectedCommunicationNumber !== "Vyberte komunikaci") {
                // UPDATE GPS from staniceni
                console.log('Updatuju body dle staniceni')
                getGpsFromStaniceni().then(() => {
                    const positions = getAllMarkerPositions(markersLayer);
                    console.log("All marker positions:", positions);
                    map.fitBounds(positions);
                });
            }
            console.log(selectedCommunicationNumber);
            if (selectedCommunicationNumber && selectedCommunicationNumber !== "Vyberte komunikaci") {
                showSelectedCommunication();
            } else {
                polylineCommunication.setLatLngs([]);
            }
        }

        function showSelectedCommunication() {
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
                        seskupene[usek].push({ lat: item.X_COORD_WGS, lng: item.Y_COORD_WGS });

                    });
                    // console.log(seskupene);
                    let polylinePoints = [];
                    for (let usek in seskupene) {
                        if (seskupene.hasOwnProperty(usek)) {
                            // Vytvoříme pole souřadnic pro polyline
                            polylinePoints.push(seskupene[usek]);
                        }
                    }
                    console.log(polylinePoints);
                    polylineCommunication.setLatLngs(polylinePoints);
                    if (!$allPoints.val() && !$gpsE.val() && !$gpsN.val() && !$gpsE2.val() && !$gpsN2.val()) {
                        map.fitBounds(polylinePoints);
                    }
                    getAllMarkerPositions(markersLayer);
                }, error: function () {
                    alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                    polylineCommunication.setLatLngs([]);
                }
            });
        }

        async function getGpsFromStaniceni() {
            var staniceniFrom = $('[name="communication[' + openCommunicationModal + '][stationingFrom]"]').val();
            await $.ajax({
                url: 'https://staniceni.fd.cvut.cz/api/staniceni/getGpsFromStaniceni/'+selectedCommunicationNumber+'/'+staniceniFrom,
                type: "GET",
                dataType: 'json',
                success: function(res) {
                    $gpsE.val(res[0].Y_COORD_WGS);
                    gpsEVal = res[0].Y_COORD_WGS;
                    $gpsN.val(res[0].X_COORD_WGS);
                    gpsNVal = res[0].X_COORD_WGS;
                    const marker1 = L.marker([gpsNVal, gpsEVal], {draggable: true}).addTo(markersLayer);

                    // Add a dragend event to log the new position
                    marker1.on('dragend', function (e) {
                        const newLatLng = e.target.getLatLng();
                        console.log(`Marker moved to: ${newLatLng.lat}, ${newLatLng.lng}`);
                        const positions = getAllMarkerPositions(markersLayer);
                        console.log("All marker positions:", positions);
                    });
                }, error: function () {
                    // alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                }
            });
            var staniceniTo = $('[name="communication[' + openCommunicationModal + '][stationingTo]"]').val();
            await $.ajax({
                url: 'https://staniceni.fd.cvut.cz/api/staniceni/getGpsFromStaniceni/'+selectedCommunicationNumber+'/'+staniceniTo,
                type: "GET",
                dataType: 'json',
                success: function(res) {
                    $gpsE2.val(res[0].Y_COORD_WGS);
                    gpsE2Val = res[0].Y_COORD_WGS;
                    $gpsN2.val(res[0].X_COORD_WGS);
                    gpsN2Val = res[0].X_COORD_WGS;
                    const marker2 = L.marker([gpsN2Val, gpsE2Val], {draggable: true}).addTo(markersLayer);

                    // Add a dragend event to log the new position
                    marker2.on('dragend', function (e) {
                        const newLatLng = e.target.getLatLng();
                        console.log(`Marker moved to: ${newLatLng.lat}, ${newLatLng.lng}`);
                        const positions = getAllMarkerPositions(markersLayer);
                        console.log("All marker positions:", positions);
                    });

                }, error: function () {
                    // alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                }
            });
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
        function highlightSegment(start, end) {
            latlngs = polylineCommunication.getLatLngs().flat();
            //console.log(start);
            //console.log(end);
            point1 = findNearestPoint(start, latlngs);
            point2 = findNearestPoint(end, latlngs);
            //console.log(point1);
            //console.log(point2);
            var startIndex = latlngs.findIndex(p => p === point1);
            var endIndex = latlngs.findIndex(p => p === point2);
            //console.log(startIndex);
            //console.log(endIndex);

            if (startIndex > -1 && endIndex > -1 && startIndex !== endIndex) {
                var segment = latlngs.slice(Math.min(startIndex, endIndex), Math.max(startIndex, endIndex) + 1);
                polylineSegmentHighlight.setLatLngs(segment);
            }
        }


        // Function to get all marker positions
        function getAllMarkerPositions(layerGroup) {
            const positions = [];

            layerGroup.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    const {lat, lng} = layer.getLatLng();
                    positions.push({lat, lng});
                }
            });

            // Update start and end positions
            if (positions.length > 0) {
                modalGps1.html("Začátek: " + positions[0].lng + ", " + positions[0].lat);
                $gpsE.val(positions[0].lng).change();
                $gpsN.val(positions[0].lat).change();
                if (positions.length >= 2) {
                    modalGps2.html("Konec: " + positions[positions.length - 1].lng + ", " + positions[positions.length - 1].lat);
                    $gpsE2.val(positions[positions.length - 1].lng).change();
                    $gpsN2.val(positions[positions.length - 1].lat).change();
                    highlightSegment(positions[0], positions[positions.length - 1]);
                }
            }
            // Update allPoints hidden input
            var positionsOldStyle = '';
            positions.forEach((pos) => {
                positionsOldStyle += '(' + pos.lng + ',' + pos.lat + '),';
            })
            $allPoints.val(positionsOldStyle);
            // Update polyline
            polyline.setLatLngs(positions);
            return positions;
        }

// Add a click event listener to the map
        map.on('click', function (event) {
            // Get the clicked position
            const {lat, lng} = event.latlng;

            // Create a new draggable marker at the clicked position
            const marker = L.marker([lat, lng], {draggable: true}).addTo(markersLayer);

            // Add a dragend event to log the new position
            marker.on('dragend', function (e) {
                const newLatLng = e.target.getLatLng();
                console.log(`Marker moved to: ${newLatLng.lat}, ${newLatLng.lng}`);
                const positions = getAllMarkerPositions(markersLayer);
                console.log("All marker positions:", positions);
            });

            console.log(`Marker added at: ${lat}, ${lng}`);
            // Example usage
            const positions = getAllMarkerPositions(markersLayer);
            console.log("All marker positions:", positions);
        });

        $('#resetMarkers').bind('click', function () {
            markersLayer.clearLayers();
            polyline.setLatLngs([]);
        });

        $('#modalMapa').on('shown.bs.modal', function (e) {
            markersLayer.clearLayers();
            polyline.setLatLngs([]);
            $gpsE = $('#gpsE_' + openCommunicationModal);
            $gpsN = $('#gpsN_' + openCommunicationModal);
            $gpsE2 = $('#gpsE2_' + openCommunicationModal);
            $gpsN2 = $('#gpsN2_' + openCommunicationModal);
            $allPoints = $('#allPoints_' + openCommunicationModal);

            gpsEVal = ($gpsE.val() != '') ? $gpsE.val() : 14.2943039;
            gpsNVal = ($gpsN.val() != '') ? $gpsN.val() : 50.0349000;
            gpsE2Val = ($gpsE2.val() != '') ? $gpsE2.val() : 14.6019211;
            gpsN2Val = ($gpsN2.val() != '') ? $gpsN2.val() : 49.9801789;

            map.invalidateSize();
            setTimeout(function() {
                updateFromForm();
                map.invalidateSize();
            }, 15);

        });

        $('#modalMapa').on('hidden.bs.modal', function (e) {
            console.log('Map modal closed');
            var isNewStaniceni = false;
            const positions = getAllMarkerPositions(markersLayer);
            var originalStaniceniFrom = parseFloat($('[name="communication[' + openCommunicationModal + '][stationingFrom]"]').val());
            var originalStaniceniTo = parseFloat($('[name="communication[' + openCommunicationModal + '][stationingTo]"]').val());
            var newStaniceniFrom = 0;
            var newStaniceniTo = 0;
            if (positions && positions.length > 1) {
                $.ajax({
                    url: 'https://staniceni.fd.cvut.cz/api/staniceni/getNearest/road/' + selectedCommunicationNumber + '/wgs_coords?gps_coords=' + positions[0].lat + 'N,' + positions[0].lng + 'E',
                    type: "GET",
                    dataType: 'json',
                    success: function (res) {
                        newStaniceniFrom = parseFloat(res[0].STANICENI_PREPOCET_KM);
                        if (parseFloat(parseFloat(newStaniceniFrom).toFixed(2)) !== parseFloat(parseFloat(originalStaniceniFrom).toFixed(2))) {
                            console.log('Staniceni pocatiecniho bodu se zmenilo');
                            isNewStaniceni = true;
                        }
                        $.ajax({
                            url: 'https://staniceni.fd.cvut.cz/api/staniceni/getNearest/road/' + selectedCommunicationNumber + '/wgs_coords?gps_coords=' + positions[positions.length-1].lat + 'N,' + positions[positions.length-1].lng + 'E',
                            type: "GET",
                            dataType: 'json',
                            success: function (resp) {
                                newStaniceniTo = parseFloat(resp[0].STANICENI_PREPOCET_KM);
                                if (parseFloat(parseFloat(newStaniceniTo).toFixed(2)) !== parseFloat(parseFloat(originalStaniceniTo).toFixed(2))) {
                                    console.log('Staniceni koncoveho bodu se zmenilo');
                                    isNewStaniceni = true;
                                }
                                if (isNewStaniceni) {
                                    if (newStaniceniFrom > newStaniceniTo) {
                                        var temp = newStaniceniFrom;
                                        newStaniceniFrom = newStaniceniTo;
                                        newStaniceniTo = temp;
                                        temp = $gpsE.val();
                                        $gpsE.val($gpsE2.val());
                                        $gpsE2.val(temp);
                                        temp = $gpsN.val();
                                        $gpsN.val($gpsN2.val());
                                        $gpsN2.val(temp);
                                        var positionsOldStyle = '';
                                        positions.reverse().forEach((pos) => {
                                            positionsOldStyle += '(' + pos.lng + ',' + pos.lat + '),';
                                        })
                                        $allPoints.val(positionsOldStyle);
                                    }
                                    console.log('Origo staniceni: ' + originalStaniceniFrom + ' - ' + originalStaniceniTo + '. Nove staniceni: ' + newStaniceniFrom + ' - ' + newStaniceniTo);
                                    if (originalStaniceniFrom || originalStaniceniTo) {
                                        $('#staniceniOrigo').html(originalStaniceniFrom + ' - ' + originalStaniceniTo);
                                        $('#staniceniNew').html(newStaniceniFrom.toFixed(3) + ' - ' + newStaniceniTo.toFixed(3));
                                        $('#modalZmenaStaniceni').modal('show');
                                        $('#keepStaniceni').bind('click', function () {
                                            $('#modalZmenaStaniceni').modal('toggle');
                                        });
                                        $('#updateStaniceni').bind('click', function () {
                                            $('#modalZmenaStaniceni').modal('toggle');
                                            $('[name="communication[' + openCommunicationModal + '][stationingFrom]"]').val(newStaniceniFrom.toFixed(3));
                                            $('[name="communication[' + openCommunicationModal + '][stationingTo]"]').val(newStaniceniTo.toFixed(3));
                                        });
                                    } else {
                                        $('[name="communication[' + openCommunicationModal + '][stationingFrom]"]').val(newStaniceniFrom.toFixed(3));
                                        $('[name="communication[' + openCommunicationModal + '][stationingTo]"]').val(newStaniceniTo.toFixed(3));
                                    }
                                }

                            }, error: function () {
                                // alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                            }
                        });
                    }, error: function () {
                        // alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                    }
                });
            }
        });

        updateFromForm();

    }

// END OF LEAFLET MAP

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
    map.setView([item.value.lat, item.value.lon], 12);
})

tinymce.init({
    selector: '#assignments',
    plugins: "lists",
    toolbar: "undo redo | styleselect | bullist numlist | bold italic ",
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});


$("select[name='idRelationType']").bind('change', function () {
    const $idRelProject = $("select[name='idProjectOrigin']");
    if ($(this).val() == 0) {
        $idRelProject.prop('disabled', true);
        $idRelProject.selectpicker("refresh");
    } else {
        $idRelProject.prop('disabled', false);
        $idRelProject.selectpicker("refresh");
    }
});

$('#postNewProject').bind('click', function (e) {
    e.preventDefault();
    let formData = new FormData($('#newProjectForm')[0]);
    console.log(formData);
    console.log($('form').valid());
    if ($('form').valid()) {
        $.ajax({
            url: '/submits/newProjectSubmit.php',
            type: "POST",
            cache: false,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data);
                console.log(status);
                if (status === 'success' && $.isNumeric(data) == true) {
                    swal({
                        title: 'Projekt uložen',
                        text: 'Projekt byl uložen pod ID' + data + '. Chcete přejít do výpisu nebo zůstat na této stránce ?',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Přejít na výpis',
                        cancelButtonText: 'Zůstat zde'
                    }).then(
                        result => {
                            window.location.href = 'vypis.php?idProject=' + data;
                        },
                        dismiss => {
                            window.location.href = 'newProject.php';
                        }
                    );

                    // notify('bottom','right','success','Projekt byl uložen pod ID '+ data);
                } else {
                    notify('bottom', 'right', 'danger', 'Někde se stala chyba, projekt nebyl uložen');
                }
            }, error: function () {
                alert('CHYBA aaa');
            }
        });
    }
});


$('#mapCopyGps').bind('click', function () {
    $('#modalMapa').modal('toggle');
});

let uri = URI(window.location.href);

const $projectType = $("select[name='idProjectType']");
const $projectSubtype = $("#idProjectSubtype");
let areaSelectTemplate = $(".areaSelectWrap").first().clone();
let $roadCommunicationTemplate = $(".communicationFormGroup").first().clone();
let $cyclistCommunicationTemplate = null;

$.ajax({
    url: '/ajax/getCommunicationFormTemplate.php',
    type: "POST",
    cache: false,
    success: function (data) {
        const templates = $.parseJSON(data);
        $roadCommunicationTemplate = $($.parseHTML(templates['roadtCommunicationTemplate']));
        $cyclistCommunicationTemplate = $($.parseHTML(templates['cyclistCommunicationTemplate']));
    }, error: function () {
        alert('CHYBA');
    }
});

let previous = null;
const $communicationWrapper = $('#communicationWrapper');
$projectType.bind('change', function () {
    if ($(this).val() === '3') {
        if (previous !== '3') {
            $communicationWrapper.html($cyclistCommunicationTemplate.clone());
            $(".selectCommunication").last().selectpicker()
        }
        previous = $(this).val();

    } else {
        if (previous === '3') {
            $communicationWrapper.html($roadCommunicationTemplate.clone());
            $(".selectCommunication").last().selectpicker()
        }
        previous = $(this).val();
    }
});


const $objectSelect = $('#objectSelect');
$removeArea = $('#removeArea');
$removeCommunication = $('#removeCommunication');
const $selectArea = $(".selectArea");
$selectArea.selectpicker();
$(".selectCommunication").selectpicker();

$("#addArea").bind('click', function () {
    $("#areaForm").append(areaSelectTemplate.clone());
    numAreas++;
    $(".selectArea").last().val(null);
    $(".selectArea").last().selectpicker();


    if (numAreas > 0) {
        $removeArea.addClass('active');
        $removeArea.removeClass('not-active');
    } else {
        $removeArea.removeClass('active');
        $removeArea.addClass('not-active');
    }

});

if (numAreas > 0) {
    $removeArea.addClass('active');
    $removeArea.removeClass('not-active');
} else {
    $removeArea.removeClass('active');
    $removeArea.addClass('not-active');
}

if (numCommunications > 0) {
    $removeCommunication.addClass('active');
    $removeCommunication.removeClass('not-active');
} else {
    $removeCommunication.removeClass('active');
    $removeCommunication.addClass('not-active');
}


$removeArea.bind('click', function () {
    if (numAreas === 0) {
        e.preventDefault()
    } else {
        $(".areaSelectWrap").last().remove();
        numAreas--;
    }

    if (numAreas > 0) {
        $removeArea.addClass('active');
        $removeArea.removeClass('not-active');
    } else {
        $removeArea.removeClass('active');
        $removeArea.addClass('not-active');
    }
});

$("#addCommunication").bind('click', function () {
    console.log("Add communiaction button pressed.");
    if ($projectType.val() === '3') {
        console.log("Cyklostezka");
        numCommunications++;
        $communicationWrapper.append($cyclistCommunicationTemplate.clone());
        $(".selectCommunication").last().selectpicker().attr('name', 'communication[' + numCommunications + '][idCommunication]');
        $(".gpsN1").last().attr('name', 'communication[' + numCommunications + '][gpsN1]');
        $(".gpsN1").last().attr('id', 'gpsN_' + numCommunications);
        $(".gpsE1").last().attr('name', 'communication[' + numCommunications + '][gpsE1]');
        $(".gpsE1").last().attr('id', 'gpsE_' + numCommunications);
        $(".gpsN2").last().attr('name', 'communication[' + numCommunications + '][gpsN2]');
        $(".gpsN2").last().attr('id', 'gpsN2_' + numCommunications);
        $(".gpsE2").last().attr('name', 'communication[' + numCommunications + '][gpsE2]');
        $(".gpsE2").last().attr('id', 'gpsE2_' + numCommunications);
        $(".allPoints").last().attr('name', 'communication[' + numCommunications + '][allPoints]');
        $(".allPoints").last().attr('id', 'allPoints_' + numCommunications);
        $(".modalMapButton").last().attr('data-idOrderCommunication', numCommunications);

    } else {
        console.log("Komunikace pro motorová vozidla");
        numCommunications++;
        $(".communicationFormGroup").last().after($roadCommunicationTemplate.clone());
        $(".selectCommunication").last().selectpicker().attr('name', 'communication[' + numCommunications + '][idCommunication]');
        $(".stationingFrom").last().selectpicker().attr('name', 'communication[' + numCommunications + '][stationingFrom]');
        $(".stationingTo").last().selectpicker().attr('name', 'communication[' + numCommunications + '][stationingTo]');
        $(".gpsN1").last().attr('name', 'communication[' + numCommunications + '][gpsN1]');
        $(".gpsN1").last().attr('id', 'gpsN_' + numCommunications);
        $(".gpsE1").last().attr('name', 'communication[' + numCommunications + '][gpsE1]');
        $(".gpsE1").last().attr('id', 'gpsE_' + numCommunications);
        $(".gpsN2").last().attr('name', 'communication[' + numCommunications + '][gpsN2]');
        $(".gpsN2").last().attr('id', 'gpsN2_' + numCommunications);
        $(".gpsE2").last().attr('name', 'communication[' + numCommunications + '][gpsE2]');
        $(".gpsE2").last().attr('id', 'gpsE2_' + numCommunications);
        $(".allPoints").last().attr('name', 'communication[' + numCommunications + '][allPoints]');
        $(".allPoints").last().attr('id', 'allPoints_' + numCommunications);
        $(".modalMapButton").last().attr('data-idOrderCommunication', numCommunications);
    }

    if (numCommunications > 0) {
        $removeCommunication.addClass('active');
        $removeCommunication.removeClass('not-active');
    } else {
        $removeCommunication.removeClass('active');
        $removeCommunication.addClass('not-active');
    }

});


$removeCommunication.bind('click', function (e) {
    if (numCommunications === 0) {
        e.preventDefault()
    } else {
        $(".communicationFormGroup").last().remove();
        numCommunications--;
    }

    if (numCommunications > 0) {
        $removeCommunication.addClass('active');
        $removeCommunication.removeClass('not-active');
    } else {
        $removeCommunication.removeClass('active');
        $removeCommunication.addClass('not-active')
    }
});

const $addObject = $('#addObject');
$objectSelect.bind('change', function () {
    if ($(this).val() != '') {
        $addObject.addClass('active');
        $addObject.removeClass('not-active');
    } else {
        $addObject.removeClass('active');
        $addObject.addClass('not-active')
    }
});

let numObjects = ($('.objectWrapper').length) - 1;
$addObject.bind('click', function () {
    numObjects++;
    if ($objectSelect.val() != '') {
        $.ajax({
            url: '/ajax/getObjectFormTemplate.php',
            type: "POST",
            cache: false,
            data: {
                objectType: $objectSelect.val(),
                idPhase: $("input[name='idPhase']").val(),
                numObjects: numObjects
            },
            success: function (data, status) {
                const $objectTemplate = $($.parseHTML(data));
                $('#objectWrapper').append($objectTemplate.clone());

            }, error: function () {
                alert('CHYBA');
            }
        });
    }
});


$('#objectWrapper').on('click','.removeObject', function () {
    console.log('bla');
    $(this).parent().parent().remove();
});

$('#modalMapa').on('show.bs.modal', function (e) {
    openCommunicationModal = $(e.relatedTarget).attr('data-idOrderCommunication');
    selectedCommunicationNumber = $("[name='communication["+openCommunicationModal+"][idCommunication]'] option:selected").text();
    setTimeout(function () {
        load();
    }, 400);
});


$projectType.bind('change', function () {
    console.log($projectSubtype.attr('name'));
    const val = $(this).val();

    console.log(val);
    //TODO - MP - make it general if it returns empty string...
    if (val == 1 || val == 2 || val == 4) {
        $projectSubtype.val(null);

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
    } else {
        $projectSubtype.val(null);
        $projectSubtype.prop('disabled', true);
        $projectSubtype.selectpicker("refresh");
    }
});

$projectSubtype.bind('change', function () {
    const val = $projectSubtype.val();
    console.log(val);
    $.ajax({
        url: '/ajax/getObjects.php',
        type: "POST",
        cache: false,
        data: {
            idSubtype: val
        },
        success: function (data, status) {
            if (data != '') {
                $objectSelect.prop('disabled', false);

                $objectSelect.html(data);
                $objectSelect.selectpicker();
                $objectSelect.selectpicker("refresh");
            } else {
                $objectSelect.prop('disabled', true);
                $objectSelect.selectpicker("refresh");
            }
        }, error: function () {
            alert('CHYBA');
        }
    });
});

$projectSubtype.trigger("change");

const $togglePrePricePDAD = $('#togglePrePricePDAD');
const $mergedPrePricePDAD = $('.prePricePDAD');
const $toMerge = $('.mergedPrice');

$togglePrePricePDAD.on('change',function (){
    if($(this).is(':checked')){
        $toMerge.prop('disabled', true);
        $toMerge.val(null);
        $toMerge.parent().parent().hide(100);
        $mergedPrePricePDAD.prop('disabled', false);
        $mergedPrePricePDAD.parent().parent().show(100);
        //$toMerge.parent().parent().addClass('d-none');
    }else{
        $toMerge.prop('disabled', false);
        $toMerge.parent().parent().show(100);
        $mergedPrePricePDAD.prop('disabled', true);
        $mergedPrePricePDAD.val(null);
        $mergedPrePricePDAD.parent().parent().hide(100);
        //$toMerge.parent().parent().removeClass('d-none');
    }
});


$('.togglePrice').on('change',function() {
    if($(this).is(':checked')){
        $(this).closest('.togglebutton').find("input[type='hidden']").val(1);
    }else{
        $(this).closest('.togglebutton').find("input[type='hidden']").val(0);
    }
});



$togglePrePricePDAD.trigger('change');