<?php
require_once '../../../common/global.php';
include "../../../sessio.php";
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "common/Classes/StatisticsController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('CLD_questions_emails');
$baseController->createModel('rentals');
$baseController->createModel('photos');
$baseController->createModel('App_boothDongle');
$baseController->createModel('App_booths');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('booths');
$baseController->createModel('usbs');
$baseController->createModel('CLD_EventsManegers');
$baseController->createModel('registre_emails');

$stdController = new StatisticsController();

function get_img_type($imageUrl){
    $wImg = 0;
    $hImg = 0;
    $tipusPhoto = 0;
    list($wImg, $hImg) = getimagesize($imageUrl);
    //segons les mides tindrem el tipus de foto 1:tira vertical   2: tira horitzontal   3:4x6 horitzontal   4:4x6 vertical
    ////deixem un marge en les comparacions
    if(abs($wImg - 708) < 50){//
        $tipusPhoto = 1;
    }
    else if(abs($hImg - 708) < 50){//
        $tipusPhoto = 2;
    }
    else if(abs($hImg - 1416) < 50){//
        $tipusPhoto = 3;
    }
    else if(abs($wImg - 1416) < 50){//
        $tipusPhoto = 3;
    }
    return $tipusPhoto;
}

$ID = $_POST['id'];
$trashed = $_POST['trashed'];
$CLD_CON2 = clone($CLD_CON);
//$CLD_CON->OpenRs("SELECT * FROM events WHERE id = $ID");
$events = $baseController->eventsModel->getEvent($ID);
$html = "";

$html .= "<div class='inContent'>";
if ($events) {
    $idEvent = $events[0]["id"];
    $title = stripcslashes($events[0]["title"]);
    $date2 = $events[0]["start_date"];
    if($trashed){
        $html .= '
                    <div class="alert alert-danger fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Warning!</strong> This event has expired because have more than 1 year old.
                    </div>
                ';
    }
    //<strong>Info!</strong> This event has expired because have more than 1 year old. See <a>Terms and conditions</a> for more information.
    $date = date("F d, Y", strtotime($date2));
    $private = $events[0]["private"];
    $invitedName = $events[0]["CLD_invitedName"];
    $invitedEmail = $events[0]["CLD_invitedEmail"];
    $securityCode = $events[0]["CLD_SecurityCode"];
    $eventManagerID = $events[0]["CLD_eventManegerId"];
    $eventOwer = $events[0]["rental_id"];
    $hashtag = $events[0]["hashtag"];
//    $CLD_CON2->OpenRs("SELECT id FROM photos WHERE event_id=$idEvent");
    $photos = $baseController->photosModel->getPhotos($idEvent);
    $numPhotos = count($photos);
//    $numPhotos = $CLD_CON2->GetRsRows();
    if ($private == 0) {
        $private2 = "NO";
    } else {
        $private2 = "YES";
    }
    
    $hoy = date("Y-m-d");
    $dat1 = date("Y-m-d", mktime(0, 0, 0, date("m") - 3, date("d"), date("Y")));
    $infoEvent = $baseController->eventsModel->getEventCLD_dateLP($dat1, $idEvent);
    //$CLD_CON2->OpenRs("SELECT id FROM events WHERE (CLD_date_lastPhoto < '$dat1' OR CLD_date_lastPhoto IS NULL) AND id=$idEvent");
    if ($infoEvent) {
        $color = "#A10326";
        $title_s = "Inactive from more 3 month ago";
        $ownerName = $infoEvent[0]["name"];
    }
    $infoEvent = $baseController->eventsModel->getEventCLD_dateLP($dat1, $idEvent, $hoy);
    //$CLD_CON2->OpenRs("SELECT id FROM events WHERE id=$idEvent AND CLD_date_lastPhoto BETWEEN '$dat1' AND '$hoy'");
    if ($infoEvent) {
        $color = "#FFCC33";
        $title_s = "The last photo is befor 3 months ago";
        $ownerName = $infoEvent[0]["name"];
    }
    $dat2 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
    $infoEvent = $baseController->eventsModel->getEventCLD_dateLP($dat2, $idEvent, $hoy);
    //$CLD_CON2->OpenRs("SELECT id FROM events WHERE CLD_date_lastPhoto BETWEEN '$dat2' AND '$hoy' AND id=$idEvent");
    if ($infoEvent) {
        $color = "#6BBA70";
        $title_s = "Active";
        $ownerName = $infoEvent[0]["name"];
    }
    
    if(!$color){
        $color = "#A10326";
        $title_s = "Inactive from more 3 month ago";
    }

    $e1 = $baseController->registre_emailsModel->getRegistreEmailEvent($ID);
    $e1 = count($e1);
    
    $e2 = $baseController->CLD_questions_emailsModel->getQuestionsEmail($ID);
    $e2 = count($e2);
    
    $numEmails = $e1 + $e2;
    $html .= "<div class='boxLeft'>";

    $html .= "<h1>$title &nbsp <span style='background-color:$color;padding:0px 16px;border-radius:100%;color:$color;' title='$title_s'>·</span></h1>";
    $html .= "<p> ID : $ID </p>";
    $html .= "<p>START DATE: $date</p>";
    $html .= "<p>PRIVATE: $private2</p>";

    /*    if($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 4){
      if($numPhotos==0){
      $html .= "<p> This event don't have any photo , if you want delete it click : <input type='button' class='miniTrash'></p>";
      }else{
      $html .= "<p>PHOTOS: $numPhotos </p>";
      }
      }else{ */
    $html .= "<p>PHOTOS: $numPhotos </p>";
    /* } */
    $html .= "<p>CAPTURED EMAILS: $numEmails</p>";
    $editaHashtag = "<input type='button' class='editButton' onClick='edit(66 , $ID);'>";
    if ($_SESSION['USERTYPE']==6) {
        $editaHashtag = "";
    }
    if (empty($hashtag)) {
        $html .= "<p>HASHTAGS : No selected $editaHashtag</p>";
    } 
    else {
        $html .= "<p>HASHTAGS : $hashtag $editaHashtag</p>";
    }


    if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
        $rentals = $baseController->rentalsModel->getRentalsNames($eventOwer);
        //$CLD_CON2->OpenRs("SELECT name FROM rentals WHERE id=$eventOwer");
        if ($rentals) {
            $OwnerName = $rentals[0]["name"];
            $html .= "<p>OWNER : <span class='link2' onclick='openLink(\"Owner\" ,$eventOwer);'>$OwnerName</span></p>";
        }
    }

    $html .= "<h1>PhotoBooths</h1>";
    $booths = $baseController->photosModel->getPhotosCountBooth($ID);
    foreach ($booths as $booth) {
        $numPh = $booth["counter"];
        $boothID = $booth["pbs_id"];
        $dongleID = $booth["booth_id"];
        if ($boothID) {
            $descBooth = $baseController->App_boothsModel->getBoothID($boothID);
            $p_SN = $descBooth[0]["serialnumber"];
            $p_idType = $descBooth[0]["CLD_idType"];
            $p_nameType = $baseController->CLD_boothTypesModel->getBoothTypeName($p_idType);
            $p_nameType = $p_nameType[0]["name"];
            $randString = $baseController->boothsModel->getBoothsByDongle($dongleID);
            $randString = $randString[0]["rand_string"];
            utils::log($randString, "logasd");
            $html .= "<p>  $p_nameType - $p_SN -  $randString - $numPh photos</p>";
        }
        
    }

    if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
        $editaInvite = "<input type='button' class='editButton' onClick='edit(13 , $ID);'>";
        if ($_SESSION['USERTYPE']==6) {
            $editaInvite = "";
        }
        $html .= "<h1>Event Manager</h1>";
        $html .= "<p>An Event Manager is a person you invite to customize the event. (For example the bride or the groom is an event manager)</p>";
        if (empty($invitedEmail)) {
            $html .= "<p>No one invited yet. $editaInvite</p>";
        } 
        else {
            $html .= "<p>Invited Name : -$invitedName-</p>";
            $html .= "<p>Invited E-mail : -$invitedEmail-</p>";
            $html .= "<p>Security Code : -$securityCode-</p>";
            if (empty($eventManagerID)) {
                $html .= "<p> No registered yet $editaInvite</p>";
            } 
            else{
                //$CLD_CON->OpenRs("SELECT * FROM CLD_EventsManegers WHERE id= $eventManagerID");
                $emanager = $baseController->CLD_EventsManegersModel->getCLD_EventsManegers($eventManagerID);
                if ($emanager) {
                    $manager = $emanager[0]["name"] . " " . $emanager[0]["surname"];
                    $emailManager = $emanager[0]["email"];
                }
                $html .= "<p> Registered Name: $manager</p>";
                $html .= "<p> Registered E-mail: $emailManager</p>";
            }
        }
    }

    $html .= "</div>";
}

$html .= "<div class='boxRight'>";
if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
    $html .= "<h1> USB Stick customization download.</h1>";
    $html .= "<div class='box'>";
    //$CLD_CON->OpenRs("SELECT id , creation_date , boothtype_char , CLD_idTypeBooth FROM usbs WHERE event_id=$ID");
    $usbs = $baseController->usbsModel->get_usbsEventId($ID);
    foreach ($usbs as $usb){
        $usbId = $usb["id"];
        $fld = $usb["creation_date"] . $usb["id"];
        $boothChar = $usb["boothtype_char"];
        $idType = $usb["CLD_idTypeBooth"];
        $html .= "<div style='display:inline;float:left;width:28%;position:relative;padding:1%;border:1px solid black;margin:1%;cursor:pointer' onclick='downloadZIP($usbId , $fld , $ID);'>";

        if (empty($idType)) {
            $html .= "<img src='images/web/pb/$boothChar.png' style='width:95%;'>";
        } 
        else {
            $html .= "<img src='images/web/pb/$idType.png' style='width:95%;'>";
        }
        $html .= "<input type='button' class='miniDownload'  style='position: absolute;top: 76%;right: 5%;'>";
        if ($boothChar == "C") {
            $html .= "<span style='background-color:black;color:white;padding: 12px 16px;border-radius:100px;position: absolute;top: 0px;left: 41%;'>IN</span>";
        }
        if ($boothChar == "D") {
            $html .= "<span style='background-color:black;color:white;padding:12px 10px;border-radius:100px;position: absolute;top: 0px;left: 41%;'>OUT</span>";
        }
        $html .= "</div>";
    }

    if (count($usbs) == 0) {
        $html .= "<p>This event does not have any USB stick customization</p>";
        $html .= "<p>If you have uploaded the logo, frames or text (on section &quot;Print Photos&quot;), and there is no link to download the USB stick files, please choose a PhotoBooth model and click on the green button to create a download link.";
        //$CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$eventOwer GROUP BY CLD_idType");
        $booths1 = $baseController->App_boothsModel->getBooths($eventOwer, 'CLD_idType');

        if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
            $boothsTypes = array();
            foreach($booths1 as $booth1){
                if (!empty($booth1["CLD_idType"])) {
                    array_push($boothsTypes, $booth1["CLD_idType"]);
                    //versio per saber la resolucio de l'imatge
                    $version = $booth1["version"];   
                    $version = explode(" ",$version);
                    if ($version[0] == "Expression"){
                        $resolucio = 1;
                    }elseif($version[0] == "Britta"){
                        $resolucio = 2;
                    }else{//null
                        $resolucio = 3;
                    } 
                }
            }
           
            //$CLD_CON->OpenRs("SELECT b.name , b.id , b.char  FROM CLD_boothTypes b WHERE b.char != '-' AND b.id IN($boothsTypes)");            
            $booths2 = $baseController->CLD_boothTypesModel->getBoothsIdIn($boothsTypes);
            $html .= "<p>Your PhotoBooth models : <select id='types' class='selectText' style='font-size:10pt;'><p>";
            $html .= "<option value=0>-------------</option>";
//            utils::log($booths2, 'eventInfo');
            foreach ($booths2 as $booth2){
                $id_type = $booth2["char"];
                $_type = $booth2["id"];
                $nom = $booth2["name"];
                $html .= "<option value='$id_type##$_type'>$nom</option>";
                
            }
            utils::log($version, "logasd");
            $html .= "</select><input type='button' class='miniAdd' onclick='newUSB($ID , $eventOwer, $version)'></p>";
        }
    }
    $html .= "</div>";
}

//$html .= "<h1>Counters</h1>";


$tipus = array("Show From QR", "Show From Web", "Shared on Facebook", "Sended By email", "Shared on Twitter", "Video Shared on Facebook", "Video Sended By e-mail", "Video Shared on Twitter", "Banners Clicks");
$q = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$most = array("-", "-", "-", "-", "-", "-", "-", "-");

$statics = $stdController->getStatisticsEvent($ID);
foreach($statics as $static){
    $type = $static["type_info"] - 1;
    $q[$type] = $static["summation"];
    $most[$type] = $static["code_photo"];
}

//consulta sql
//mail
$CLD_CON->OpenRs("SELECT count( `method` ) AS mail FROM `gestor` WHERE `idb` =$ID AND `method` =0  and `state` = 6");
while ($CLD_CON->FetchArray()) {
    $mail_sent = $CLD_CON->GetArrayField("mail");
}

//sms
$CLD_CON->OpenRs("SELECT count( `method` ) AS sms FROM `gestor` WHERE `idb` =$ID AND `method` =1  and `state` = 6");
while ($CLD_CON->FetchArray()) {
    $sms_sent = $CLD_CON->GetArrayField("sms");
}

//whatsapp
$CLD_CON->OpenRs("SELECT count( `method` ) AS whatsapp FROM `gestor` WHERE `idb` =$ID AND `method` =3  and `state` = 6");
while ($CLD_CON->FetchArray()) {
    $whatsapp_sent = $CLD_CON->GetArrayField("whatsapp");
}

//telegram
$CLD_CON->OpenRs("SELECT count( `method` ) AS teleg FROM `gestor` WHERE `idb` =$ID AND `method` =2  and `state` = 6");
while ($CLD_CON->FetchArray()) {
    $teleg_sent = $CLD_CON->GetArrayField("teleg");
}

$html .= "<table class='tableCount' style='width: 434px'>";
$html .= "<tr>";
$html .= "<td></td>";
$html .= "<td>Views</td>";
$html .= "<td>Photo</td>";
$html .= "<td>Gifs</td>";
$html .= "<td>Video</td>";
$html .= "<td>Total Share</td>";
$html .= "</tr>";

$html .= "<tr>";

$html .= "<td><img src='images/web/webIMG.png' style='width:48px;height:48px;'><br>WEB</td>";
$html .= "<td>$q[1]</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";

$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/qrIMG.png' style='width:48px;height:48px;'><br>QR</td>";
$html .= "<td>$q[0]</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[18]</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-facebook.png' style='width:48px;height:48px;'><br>Facebook</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[2]</td>";
$html .= "<td>$q[5]</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-mail.png' style='width:48px;height:48px;'><br>Mail</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[3]</td>";
$html .= "<td>$q[6]</td>";
$html .= "<td>$q[12]</td>";
$html .= "<td>$q[16]</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-twitter.png' style='width:48px;height:48px;'><br>Twitter</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[4]</td>";
$html .= "<td>$q[7]</td>";
$html .= "<td>$q[13]</td>";
$html .= "<td>-</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-instagram.png' style='width:48px;height:48px;'><br>Instagram</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[19]</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-download.png' style='width:48px;height:48px;'><br>Download</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[9]</td>";
$html .= "<td>$q[10]</td>";
$html .= "<td>$q[11]</td>";
$html .= "<td>-</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-sms-text.png' style='width:48px;height:48px;'><br>SMS</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[14]</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-telegram.png' style='width:48px;height:48px;'><br>Telegram</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[15]</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-nfc.png' style='width:48px;height:48px;'><br>NFC</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[17]</td>";
$html .= "</tr>";

$html .= "<tr>";
$html .= "<td><img src='images/web/icon-whatsapp-text.png' style='width:48px;height:48px;'><br>Whatsapp</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";
$html .= "<td>$q[20]</td>";
$html .= "</tr>";

//$html .= "<tr>";
//$html .= "<td><img src='images/web/qrIMG.png' style='width:48px;height:48px;'></td>";
//$html .= "<td>-</td>";
//$html .= "<td>-</td>";
//$html .= "<td>-</td>";
//$html .= "<td>-</td>";
//$html .= "<td></td>";
//$html .= "</tr>";











if(!$trashed){
    if ($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 4 ) {
        //$CLD_CON->OpenRs("SELECT * FROM photos WHERE event_id=$ID");
        if (count($baseController->photosModel->getPhotos($ID)) == 0) {
            $html .= "<img src='images/icons/submenu/delete.png' onclick='deleteEvent({$ID})' style='width:15%;position:absolute;top:-20px;right:0px;'>";
        }
    }
}

$html .= "</div>";
$html .= "</div>";
$html .= <<<HTML
    <script> 
        function newUSB(id, owner) {
            var type = $("#types").val();
            if (type != 0) {
                var ajaxData = {id: id, type: type, owner: owner};
                $.ajax({
                    url: 'sections/events/functions/newUSB.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data) {
                        //alert(data);
                        if (data === "OK") {
                            profile("events", "info", id);
                        }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });


            }
        }
    </script>
HTML;

echo $html;
exit;
