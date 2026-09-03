<?php
include '../sessio.php';
include G_PATH. 'common/conexio.php';

$USERNAME =  $_SESSION['USERNAME'];
$userType = $_SESSION['USERTYPE'];
$userID =  $_SESSION['USERID'];
?>
<!DOCTYPE html>
<html>
    <head>
        <link href='https://fonts.googleapis.com/css?family=Quintessential|Kelly+Slab|Oleo+Script' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/jquery.timers-1.2.js"></script>
        <script type="text/javascript" src="../js/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="../js/jquery.galleryview-3.0-dev.js"></script>
        <script src='../js/javascriptFunction.js'></script>
        <link type="text/css" rel="stylesheet" href="../css/jquery.galleryview-3.0-dev.css" />
        <link rel=stylesheet href="../css/style.css" type="text/css">
                <link rel="shortcut icon" href="../favico.ico"/>
        <!--[if lt IE 9]>
        <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class='header'>
            <div class='lgn'><span style='margin-left:25px;'>Welcome <?php echo $USERNAME; ?></span> <span class='logOut' onclick="javascript:location.href= '../ajax/logout.php'"/> </div>
            <img src='../images/logo.png' class='logo'>
        </div>  

        <?php            
            $boothId = $_SESSION['boothID'];

            $CLD_CON->OpenRs("Select serialnumber, CLD_idType FROM App_booths WHERE idBooth = $boothId");
            if($CLD_CON->FetchArray()){
                $serialNumber = $CLD_CON->GetArrayField("serialnumber");
                $boothType = $CLD_CON->GetArrayField("CLD_idType");
            }

            $CLD_CON->OpenRs("Select name FROM CLD_boothTypes WHERE id = $boothType");
            if($CLD_CON->FetchArray()){
                $tipoSerial = $CLD_CON->GetArrayField("name");
            }


            echo "<p style='position: absolute;bottom: -23px;left: 10%;font-size: 16pt;background: white;padding: 10px;z-index: 10;'>{$tipoSerial} - {$serialNumber}</p>";

        ?>
            
        </div>   
        <div class='tot'>
            <div id='inicio'  style='height: 680px;'>
                <div id='question'>

                </div>
                <div id="help"></div>
            </div>
            <p id="errors">

            </p>
            <!--<div id="helpVideos"><center>HELP VIDEOS</center></div>
            <div id="helpIMG"><center>HELP IMAGES</center></div>   -->
            <input type='hidden' id='actualQuestion' value='0'>
            <input type='hidden' id='actualSolution' value='0'>       
        </div>
        <div class='popUp'>          
            <p style='color:red;font-size:18pt;'><span onclick='disablePopup();' style='cursor:pointer;float: right;margin-right: 25%;'>Close X</span></p>
        </div>  
        <video id='MP4' controls>
            <source src='' type='video/mp4' id='MP4V'>
            Your browser does not support the video tag.
        </video>
        <div  id='IMGPop'></div>
        <div class='footer'>
           
            
        </div>  

        <script>
                 $(document).ready(function() {
                     nextQuestion();
                     $(document).keyup(function(e) {
                         if (e.keyCode == 27 && popupStatus == 1) {
                             disablePopup();
                         }   // esc
                     });

                 });
        </script>      
    </body>

</html>


