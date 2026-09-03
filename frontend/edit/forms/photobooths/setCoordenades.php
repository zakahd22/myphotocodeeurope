<?php
$ID = $_POST['id'];

$x = false;
$y = false;

$baseController->createModel('App_booths');

function url_get_contents($Url) {
    if (!function_exists('curl_init')) {
        
    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

$pb = $baseController->App_boothsModel->getBoothWhereid($ID);

if($pb){
    $lat = $pb[0]["latitude"];
    $lon = $pb[0]["longitude"]; 
}

if (!empty($lat) && !empty($lon)) {
    $lat = $lat / 1000000;
    $x = true;
    $lon = $lon / 1000000;
    $y = true;
} else {
    $lat2 = 27.391278;
    $lon2 = -22.917481;
    $zm2 = 1;
}
$title = "GPS Position";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "These GPS Position will be used on the MyPhotoCode APP so the user can locate the nearest PhotoBooth.<br/>";
    $content .= "Click on the map to set a latitude and longitude, or to change the current one.";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col' style='height:300px; width:300px;'>";
        $content .= "<div id='map' style='height:100%; width:100%;'></div>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<div class='popup-row'>";
            $content .= "<h2>Current GPS Position </h2>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<p>Latitude:  $lat</p>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<p>Longitude: $lon</p>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<h2>New GPS Position <input type='button' class='miniTrash'  onclick='earseCoords();' title='Delete Coordinates'></h2>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<p id='lat'>Latitude:  - </p>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<p id='lon'>Longitude: - </p>";
            $content .= "<input type='hidden' id='newLat' value='$lat'>";
            $content .= "<input type='hidden' id='newLon' value='$lon'>";
        $content .= "</div>";
    $content .= "</div>";
$content .= "</div>";

$buttons .= "<button type='button' class='popup-confirm' onclick='setCoords($ID); hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    var mapa;
    var marker = new google.maps.Marker();
    var loc;
HTML;

if ($x && $y) {
    $content .= "map($lat , $lon , 8);";
} else {
    $content .= "map2($lat2 , $lon2 , $zm2);";
}
$content .= <<<HTML
    function map(latitude, longitude, zm) {
        loc = new google.maps.LatLng(latitude, longitude);
        var mapOptions = {
            center: loc,
            zoom: zm
        };
        mapa = new google.maps.Map(document.getElementById("map"), mapOptions);
        marker = new google.maps.Marker({
            position: loc,
            map: mapa
        });
        google.maps.event.addListener(mapa, 'click', function(event) {
            changeMarker(event.latLng);
        });
    }
    function map2(latitude, longitude, zm2) {
        loc = new google.maps.LatLng(latitude, longitude);
        var mapOptions = {
            center: loc,
            zoom: zm2
        };
        mapa = new google.maps.Map(document.getElementById("map"), mapOptions);
        google.maps.event.addListener(mapa, 'click', function(event) {
            changeMarker(event.latLng);
        });
    }
    function changeMarker(loc) {
        marker.setMap(null);
        $("#lat").html("Latitude " + (Math.floor(loc.lat() * 1000000) / 1000000));
        $("#lon").html("Longitude " + (Math.floor(loc.lng() * 1000000) / 1000000));
        $("#newLat").val((Math.floor(loc.lat() * 1000000) / 1000000));
        $("#newLon").val((Math.floor(loc.lng() * 1000000) / 1000000));
        marker = new google.maps.Marker({
            position: loc,
            map: mapa
        });
    }
    function earseCoords() {
        $("#newLat").val("");
        $("#newLon").val("");
        $("#lat").html("Longitude - Undefined");
        $("#lon").html("Latitude - Undefined");
        marker.setMap(null);
    }
    function setCoords(id) {
        var lat = $("#newLat").val();
        var lon = $("#newLon").val();
        var ajaxData = {lat: lat, long: lon, id: id};
        $.ajax({
            url: 'edit/functions/photobooths/setCoords.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("photobooths", "info", id);
                } else {
                    alert(data);
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });

    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);