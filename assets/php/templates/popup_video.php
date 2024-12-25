<?php
require_once '../../../common/global.php';

$x = explode("|", $_REQUEST['id']);
$id = $x[0];
$r = $x[1];
$ruta = "events/$r/$id";

if (isset($_REQUEST['d3'])) {
    $file = "events/$r/$id-3D";
} 
elseif(file_exists("../../../events/$r/$id.mp4")) {
    $file = "events/$r/$id.mp4";
    $plugin = "https://www.apple.com/quicktime/download";
    $type = "image/x-macpaint";
    $ff = "<source src='$file' type='video/mp4'>";
} 
else{
    $file = "events/$r/$id.wmv";
    $plugin = "https://www.microsoft.com/Windows/MediaPlayer/download/default.asp";
    $type = "application/x-mplayer2";
    $ff = "<source src='$file'>";
}

echo <<<HTML
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='includes/logincss.css' type='text/css' media="screen and (min-width: 480px)" rel='stylesheet'>
    <link href="includes/logincss480.css" type="text/css" media="screen and (min-width:100px) and (max-width:480px)" rel="stylesheet"/>
HTML;

echo "<div style='margin:20px;'>";

include G_PATH . "includes/classes/Mobile.php";

if (Mobile::is_mobile()) {
    echo "<div id='popupClose' onclick='disablePopup();'></div>";
}
else{
     echo "<div id='popupClose' onclick='disablePopup();'></div>";
}
  
if (isset($_REQUEST['d3'])) {
    echo <<<HTML
        <video class="videoM" controls autoplay style='background-color:black;' id="videoM">
            <source src="{$file}.mp4"  type="video/mp4" />
            <source src="{$file}.ogg"  type="video/ogg" />
            <object width="100"  type="application/x-shockwave-flash" data="assets/swf/dewplayer-mini.swf">
                <param name="movie" value="assets/swf/dewplayer-mini.swf" />
                <param name="flashvars" value="autostart=true&amp;controlbar=over&amp;file={$file}.mp4" />
            </object>
        </video>
HTML;

    $file = $file . ".mp4";
} 
else {
    echo <<<HTML
        <video width="600"  controls autoplay style='background-color:black;' id="videoM">
            <source src="{$file}"  type="video/mp4" />
            <object width="100"  type="application/x-shockwave-flash" data="assets/swf/dewplayer-mini.swf">
                <param name="movie" value="assets/swf/dewplayer-mini.swf" />
                <param name="flashvars" value="autostart=true&amp;controlbar=over&amp;file={$file}" />
            </object>
        </video>
HTML;
}


echo "<p style='color:white;margin:0px;text-align: center;width:199%;'>If you can not play the video click <a href='{$file}' style='color:green;'>here</a></p>";
echo "</div>";

echo <<<HTML
    <script>
        function detectmob() {
            if (navigator.userAgent.match(/Android/i)
                    || navigator.userAgent.match(/webOS/i)
                    || navigator.userAgent.match(/BlackBerry/i)
                    || navigator.userAgent.match(/Windows Phone/i)
                    ) {

                return true;
            }
            else {
                return false;
            }
        }
    </script>
    <!--
       $(document).ready(function() {
            if (detectmob()) {
                document.getElementById("videoM").addEventListener("click", function() {
                    window.location.href = "{$file}";
                });
            }
        });
        <p style='color:white;margin:0px;text-align: center;width:150%;'>If you can not play the video click <a href="{$file}" style='color:green;'>here</a></p>
        <p style='color:white;margin:0px;text-align: center;width:150%;'>You can also activate the plugin ( right click on the video - > Enable VLC Plugin)</p>
    -->
HTML;
