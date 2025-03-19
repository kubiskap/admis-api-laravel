$(document).ready(function () {
    $.getScript('../js/vypis.js');

    const uri = URI(window.location.href);

    let full;
    $.ajax({
        url: '/ajax/mapa.php',
        type: "POST",
        data: {
            filtr: uri.query()
        },
        async: false,
        success: function (data) {
            $projects = $.parseJSON(data);
            full = $.parseJSON(data);


        },
        error: function () {
            alert('CHYBA');
        }
    });

    $('[data-toggle="tooltip"]').on('mouseleave', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.tooltip').tooltip('dispose');
    });
    $('[data-toggle="tooltip"]').tooltip();


    // NEW LEAFLET MAP FROM HERE
    let map;
    if($("#mapLeaflet").length) {
        // ADMIS DEV MAP KEY (now works at *.admis.fd.cvut.cz)
        const API_KEY = 'cbbjbBrx1s8NIsHh4jwCCBgV_xNOJ952K5lU5a6OUP8';

        /*
        We create the map and set its initial coordinates and zoom.
        See https://leafletjs.com/reference.html#map
        */
        map = L.map('mapLeaflet').setView([49.8729317, 14.8981184], 16);

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
        var allLinesPoints = [];

        $.each(full, function( key, value ) {

            const svgIcon = L.divIcon({
                html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="'+$projects[key].phaseColor+'" fill-rule="nonzero" d="M32 5a21 21 0 0 0-21 21c0 17 21 33 21 33s21-16 21-33A21 21 0 0 0 32 5zm6.84 30.46-6.82-3-6.8 3 .7-7.4L21 22.54l7.25-1.61L32 14.51l3.78 6.4L43 22.49l-4.9 5.57z"/></svg>',
                className: '', // Removes the default divIcon styles
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });

            for (const [i,coomunication] of $projects[key].communication.entries()) {
                if (coomunication.allPoints) {
                    pointsArr = eval("[" + coomunication.allPoints.replaceAll("(", "[").replaceAll(")", "]") + "]");
                    body = [];
                    pointsArr.forEach(element => body.push([element[1], element[0]]));
                } else {
                    body = [[coomunication.gpsN1, coomunication.gpsE1], [coomunication.gpsN2, coomunication.gpsE2]];
                }

                var popupContent = "<h4 class='font-weight-bold'>"+$projects[key].name +" (ID "+$projects[key].idProject + ")</h4>" +
                    "<a class='btn btn-rose w-100' href='detail.php?idProject="+$projects[key].idProject+"'>Přejít na projekt ID "+$projects[key].idProject+" <i class='fa fa-sign-in' data-toggle='tooltip' data-placement='left' data-original-title='Přejít na detail projektu ID "+$projects[key].idProject+"'></i></a>" +
                    "<div>Řešitel:</div><p>"+$projects[key].editorName+"</p><div>Předmět stavby:</div> "+$projects[key].subject+"<div>Termíny:</div>" +"<p>"+$projects[key].nextTerm+"</p><p>"+$projects[key].change+"</p>";

                let showComSegment = false;
                if (coomunication.name) {
                    showComSegment = showSelectedCommunication(coomunication.name, body, popupContent);
                }
                var marker1 = L.marker(body[0], { icon: svgIcon }).addTo(map);
                var marker2 = L.marker(body[body.length-1], { icon: svgIcon }).addTo(map);
                // var polyline = L.polyline(body, {color: 'red'}).addTo(map);

                marker1.bindPopup(popupContent);
                marker2.bindPopup(popupContent);
                // polyline.bindPopup(popupContent);

                allLinesPoints.push(body);
            }

        });
        // zoom the map to the polylines
        map.fitBounds(allLinesPoints);
    }

    function showSelectedCommunication(selectedCommunicationNumber, allPoints, popupContent) {
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
                // console.log(seskupene);
                let polylinePoints = [];
                for (let usek in seskupene) {
                    if (seskupene.hasOwnProperty(usek)) {
                        // Vytvoříme pole souřadnic pro polyline
                        polylinePoints.push(seskupene[usek]);
                    }
                }

                var polylineCommunication = L.polyline(polylinePoints, {color: 'grey'}).addTo(map);
                highlightSegment(polylineCommunication, allPoints, popupContent);
                foundCom = true;

            }, error: function () {
                // alert('Pro vybranou komunikaci nebylo nalezeno staničení.');
                var polyline = L.polyline(allPoints, {color: 'red'}).addTo(map);
                polyline.bindPopup(popupContent);
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
    function highlightSegment(line, allPoints, popupContent) {
        // console.log(line.getLatLngs());
        latlngs = line.getLatLngs().flat();
        // console.log(latlngs);
        point1 = findNearestPoint(allPoints[0], latlngs);
        point2 = findNearestPoint(allPoints[allPoints.length -1], latlngs);
        var startIndex = latlngs.findIndex(p => p === point1);
        var endIndex = latlngs.findIndex(p => p === point2);

        if (startIndex > -1 && endIndex > -1 && startIndex !== endIndex) {
            var segment = latlngs.slice(Math.min(startIndex, endIndex), Math.max(startIndex, endIndex) + 1);
            var polylineSegmentHighlight = L.polyline(segment, {color: 'purple'}).addTo(map);
            polylineSegmentHighlight.bindPopup(popupContent);
        } else {
            var polyline = L.polyline(allPoints, {color: 'red'}).addTo(map);
            polyline.bindPopup(popupContent);
        }
        line.remove();
    }


});



