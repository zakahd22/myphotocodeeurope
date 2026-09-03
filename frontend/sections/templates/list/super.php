<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$html = <<<HTML
    <link rel="stylesheet" type="text/css" href="sections/templates/resources/templates.css">
    <script type="text/javascript" src="sections/templates/functions/functions.js"></script>
    <div class="download">
        <div class="button">
            <a href="https://www.myphotocode.com/downloadFrames/CustomCollage2017.zip" download>
                <div class="DLayout"></div>
                <div class="layout"></div>
            </a>
        </div>
        
        <div class="text">
            <p>Britta software currently allows to install a set of 4 custom collages. You can download these four layouts and customize them to your liking.</p>
            <p>The technical specifications are the following:</p>
            <ul>
                <li><span class="title">Size:</span> (pixels): <strong>2044x1416</strong></li>
                <li><span class="title">Type:</span> <strong>RGB - 300dpi</strong></li>
                <li><span class="title">Format:</span> <strong>png</strong> (You must keep the area of the photo transparent)</li>
                <li><span class="title">Names:</span> <strong>lay1, lay2, lay3, lay4</strong></li>
            </ul>
        </div>
        <div class="tutorial">
            <div class="video" onclick="mostraTube('j81FHYJpXjA')"></div>
        </div>
    </div>
    <div class="collage">
        <div class="button_select" onclick='edit(73 , $USERID);'></div> 
            <div class="DCCollage" onclick='edit(73 , $USERID);'></div>
    </div>
    <div id='selectedCollage' class='selectedCollage hidden'></div>
HTML;
        
echo $html;