<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT * FROM events WHERE id = $ID");

echo "<div class='inContent'>";
if ($CLD_CON->FetchArray()) {
    $idEvent = $CLD_CON->GetArrayField("id");
    $title = stripcslashes($CLD_CON->GetArrayField("title"));
    $date2 = $CLD_CON->GetArrayField("start_date");
    $date = date("F d, Y", strtotime($date2));
    $private = $CLD_CON->GetArrayField("private");
    $invitedName = $CLD_CON->GetArrayField("CLD_invitedName");
    $invitedEmail = $CLD_CON->GetArrayField("CLD_invitedEmail");
    $securityCode = $CLD_CON->GetArrayField("CLD_SecurityCode");
    $eventManagerID = $CLD_CON->GetArrayField("CLD_eventManegerId");
    $eventOwer = $CLD_CON->GetArrayField("rental_id");
    $hashtag = $CLD_CON->GetArrayField("hashtag");
    $CLD_CON2->OpenRs("SELECT id FROM photos WHERE event_id=$idEvent");
    $numPhotos = $CLD_CON2->GetRsRows();
    if ($private == 0) {
        $private2 = "NO";
    } else {
        $private2 = "YES";
    }

    $hoy = date("Y-m-d");
    $dat1 = date("Y-m-d", mktime(0, 0, 0, date("m") - 3, date("d"), date("Y")));
    $CLD_CON2->OpenRs("SELECT id FROM events WHERE (CLD_date_lastPhoto < '$dat1' OR CLD_date_lastPhoto IS NULL) AND id=$idEvent");
    if ($CLD_CON2->FetchArray()) {
        $color = "#A10326";
        $title_s = "Inactive from more 3 month ago";
    }
    $CLD_CON2->OpenRs("SELECT id FROM events WHERE id=$idEvent AND CLD_date_lastPhoto BETWEEN '$dat1' AND '$hoy'");
    if ($CLD_CON2->FetchArray()) {
        $color = "#FFCC33";
        $title_s = "The last photo is befor 3 months ago";
    }
    $dat2 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
    $CLD_CON2->OpenRs("SELECT id FROM events WHERE CLD_date_lastPhoto BETWEEN '$dat2' AND '$hoy' AND id=$idEvent");
    if ($CLD_CON2->FetchArray()) {
        $color = "#6BBA70";
        $title_s = "Active";
    }

    if ($CLD_CON2->FetchArray()) {
        $ownerName = $CLD_CON2->GetArrayField("name");
    }




    $CLD_CON2->OpenRs("SELECT r.email FROM registre_emails r RIGHT JOIN photos p ON r.code=p.code WHERE p.event_id=$ID AND r.email IS NOT NULL GROUP BY r.email");
    $e1 = $CLD_CON2->GetRsRows();
    $CLD_CON2->OpenRs("SELECT id FROM CLD_questions_emails WHERE event=$ID AND email IS NOT NULL GROUP BY email");
    $e2 = $CLD_CON2->GetRsRows();
    $numEmails = $e1 + $e2;
    echo "<div class='boxLeft'>";

    echo "<h1>$title &nbsp <span style='background-color:$color;padding:0px 16px;border-radius:100%;color:$color;' title='$title_s'>·</span></h1>";
    echo "<p>START DATE: $date</p>";
    echo "<p>PRIVATE: $private2</p>";

    /*    if($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 4){
      if($numPhotos==0){
      echo "<p> This event don't have any photo , if you want delete it click : <input type='button' class='miniTrash'></p>";
      }else{
      echo "<p>PHOTOS: $numPhotos </p>";
      }
      }else{ */
    echo "<p>PHOTOS: $numPhotos </p>";
    /* } */
    echo "<p>CAPTURED EMAILS: $numEmails</p>";

    if (empty($hashtag)) {
        echo "<p>HASHTAGS : No selected <input type='button' class='editButton' onClick='edit(66 , $ID);'></p>";
    } else {
        echo "<p>HASHTAGS : $hashtag <input type='button' class='editButton' onClick='edit(66 , $ID);'></p>";
    }


    if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
        $CLD_CON2->OpenRs("SELECT name FROM rentals WHERE id=$eventOwer");
        if ($CLD_CON2->FetchArray()) {
            $OwnerName = $CLD_CON2->GetArrayField("name");
            echo "<p>OWNER : <span class='link2' onclick='openLink(\"Owner\" ,$eventOwer);'>$OwnerName</span></p>";
        }
    }

    echo "<h1>PhotoBooths</h1>";
    $CLD_CON->OpenRs("SELECT booth_id , COUNT(*) as numPh FROM photos WHERE event_id=$ID GROUP BY booth_id");
    $date3 = date("Y-m-d", strtotime($date2));
    while ($CLD_CON->FetchArray()) {
        $idDongle = $CLD_CON->GetArrayField("booth_id");
        $numPh = $CLD_CON->GetArrayField("numPh");
        $CLD_CON2->OpenRs("SELECT idBooth FROM App_boothDongle WHERE idDongle=$idDongle AND datetimeS < '$date3' AND (datetimeF IS NULL OR datetimeF > '$date3') LIMIT 1");
        if ($CLD_CON2->FetchArray()) {
            $idBooth = $CLD_CON2->GetArrayField("idBooth");
            $CLD_CON2->OpenRs("SELECT serialnumber , CLD_idType FROM App_booths WHERE idBooth=$idBooth");
            $CLD_CON2->FetchArray();
            $p_SN = $CLD_CON2->GetArrayField("serialnumber");
            $p_idType = $CLD_CON2->GetArrayField("CLD_idType");
            $CLD_CON2->OpenRs("SELECT name FROM CLD_boothTypes WHERE id=$p_idType");
            $CLD_CON2->FetchArray();
            $p_nameType = $CLD_CON2->GetArrayField("name");
            $CLD_CON2->OpenRs("SELECT rand_string FROM booths WHERE id=$idDongle");
            $CLD_CON2->FetchArray();
            $randString = $CLD_CON2->GetArrayField("rand_string");


            echo "<p>  $p_nameType - $p_SN -  $randString - $numPh photos</p>";
        }
    }


    if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
        echo "<h1>Event Manager</h1>";
        echo "<p>An Event Manager is a person you invite to customize the event. (For example the bride or the groom is an event manager)</p>";
        if (empty($invitedEmail)) {
            echo "<p>No one invited yet. <input type='button' class='editButton' onClick='edit(13 , $ID);'></p>";
        } else {
            echo "<p>Invited Name : -$invitedName-</p>";
            echo "<p>Invited E-mail : -$invitedEmail-</p>";
            echo "<p>Security Code : -$securityCode-</p>";
            if (empty($eventManagerID)) {
                echo "<p> No registered yet <input type='button' class='editButton' onClick='edit(13 , $ID);'></p>";
            } else {
                $CLD_CON->OpenRs("SELECT * FROM CLD_EventsManegers WHERE id= $eventManagerID");
                if ($CLD_CON->FetchArray()) {
                    $manager = $CLD_CON->GetArrayField("name") . " " . $CLD_CON->GetArrayField("surname");
                    $emailManager = $CLD_CON->GetArrayField("email");
                }
                echo "<p> Registered Name: $manager</p>";
                echo "<p> Registered E-mail: $emailManager</p>";
            }
        }
    }


    echo "</div>";
}

echo "<div class='boxRight'>";
if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
    echo "<h1> USB Stick customization download.</h1>";
    echo "<div class='box'>";
    $CLD_CON->OpenRs("SELECT id , creation_date , boothtype_char , CLD_idTypeBooth FROM usbs WHERE event_id=$ID");
    while ($CLD_CON->FetchArray()) {
        $usbId = $CLD_CON->GetArrayField("id");
        $fld = $CLD_CON->GetArrayField("creation_date") . $CLD_CON->GetArrayField("id");
        $boothChar = $CLD_CON->GetArrayField("boothtype_char");
        $idType = $CLD_CON->GetArrayField("CLD_idTypeBooth");
        echo "<div style='display:inline;float:left;width:28%;position:relative;padding:1%;border:1px solid black;margin:1%;cursor:pointer' onclick='downloadZIP($usbId , $fld , $ID);'>";

        if (empty($idType)) {
            echo "<img src='$URL/images/web/pb/$boothChar.png' style='width:95%;'>";
        } else {
            echo "<img src='$URL/images/web/pb/$idType.png' style='width:95%;'>";
        }
        echo "<input type='button' class='miniDownload'  style='position: absolute;top: 76%;right: 5%;'>";
        if ($boothChar == "C") {
            echo "<span style='background-color:black;color:white;padding: 12px 16px;border-radius:100px;position: absolute;top: 0px;left: 41%;'>IN</span>";
        }
        if ($boothChar == "D") {
            echo "<span style='background-color:black;color:white;padding:12px 10px;border-radius:100px;position: absolute;top: 0px;left: 41%;'>OUT</span>";
        }
        echo "</div>";
    }

    if ($CLD_CON->GetRsRows() == 0) {
        echo "<p>This event does not have any USB stick customization</p>";
        echo "<p>If you have uploaded the logo, frames or text (on section &quot;Print Photos&quot;), and there is no link to download the USB stick files, please choose a PhotoBooth model and click on the green button to create a download link.";
        $CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$eventOwer GROUP BY CLD_idType");


        if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
            $boothsTypes = "0";
            while ($CLD_CON->FetchArray()) {
                if (!empty($CLD_CON->GetArrayField("CLD_idType"))) {
                    $boothsTypes .= "," . $CLD_CON->GetArrayField("CLD_idType");
                }
            }
            $CLD_CON->OpenRs("SELECT b.name , b.id , b.char  FROM CLD_boothTypes b WHERE b.char != '-' AND b.id IN($boothsTypes)");
            echo "<p> Select USB Stick for PhotoBooth</p>";
            echo "<p> Choose the PhotoBooth model and click on the green button to start customizing it.</p>";
            echo "<p>Your PhotoBooth models : <select id='types' class='selectText' style='font-size:10pt;'><p>";
            echo "<option value=0>-------------</option>";
            while ($CLD_CON->FetchArray()) {
                $id_type = $CLD_CON->GetArrayField("char");
                $_type = $CLD_CON->GetArrayField("id");
                $nom = $CLD_CON->GetArrayField("name");
                echo "<option value='$id_type##$_type'>$nom</option>";
            }
            echo "</select><input type='button' class='miniAdd' onclick='newUSB($ID , $eventOwer)'></p>";
        }
    }
    echo "</div>";
}

echo "<h1>Counters</h1>";

$tipus = array("Show From QR", "Show From Web", "Shared on Facebook", "Sended By email", "Shared on Twitter", "Video Shared on Facebook", "Video Sended By e-mail", "Video Shared on Twitter", "Banners Clicks");
$q = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
$CLD_CON->OpenRs("SELECT es.type_info, COUNT( es.id ) as 'c' FROM CLD_estadistiques_photos es LEFT JOIN photos p ON p.code = es.photo WHERE p.event_id=$ID  GROUP BY type_info");
while ($CLD_CON->FetchArray()) {
    $type = $CLD_CON->GetArrayField("type_info") - 1;
    $qty = $CLD_CON->GetArrayField("c");
    $q[$type] = $qty;
}
$i = 1;
$t = 0;
$most = array("-", "-", "-", "-", "-", "-", "-", "-");
while ($i <= 8) {
    $CLD_CON->OpenRs("SELECT es.photo , COUNT( es.id ) as 'c' FROM CLD_estadistiques_photos es LEFT JOIN photos p ON p.code = es.photo WHERE p.event_id=$ID AND es.type_info =$i GROUP BY es.photo ORDER BY c DESC LIMIT 1");
    if ($CLD_CON->FetchArray()) {
        $most[$t] = $CLD_CON->GetArrayField("photo");
    }
    $i++;
    $t++;
}



echo "<table class='tableCount'>";
echo "<tr>";
echo "<td colspan=2>Views</td>";
echo "<td colspan=3>Shared Photos</td>";
echo "<td colspan=3>Shared Videos</td>";
echo "</tr>";
echo "<tr>";
/* Views */
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/qrIMG.png' style='width:48px;height:48px;'>";
echo "</td>";
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/webIMG.png' style='width:48px;height:48px;'>";
echo "</td>";

/* Shared Photos */
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/fcbk.png' style='width:48px;height:48px;'>";
echo "</td>";
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/emailIMG.png' style='width:48px;height:48px;'>";
echo "</td>";
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/twitter.png' style='width:48px;height:48px;'>";
echo "</td>";

/* Shared Videos */
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/fcbk.png' style='width:48px;height:48px;'>";
echo "</td>";
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/emailIMG.png' style='width:48px;height:48px;'>";
echo "</td>";
echo "<td style='border: 2px solid black;padding:2px;'> ";
echo "<img src='$URL/images/web/twitter.png' style='width:48px;height:48px;'>";
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>$q[0]</td>";
echo "<td>$q[1]</td>";

echo "<td>$q[2]</td>";
echo "<td>$q[3]</td>";
echo "<td>$q[4]</td>";

echo "<td>$q[5]</td>";
echo "<td>$q[6]</td>";
echo "<td>$q[7]</td>";
echo"</tr>";
echo "<tr><td colspan=8>Top 1</td></tr>";
echo "<tr>";
echo "<td>";
if ($most[0] != "-") {
    echo "<img src='$URL_LOGIN/events/$date2$idEvent/$most[0].jpg' class='viewIMG'  onclick='viewPhoto(\"$most[0]\");'>";
} else {
    echo "-";
}
echo "</td>";
echo "<td>";
if ($most[1] != "-") {
    echo "<img src='$URL_LOGIN/events/$date2$idEvent/$most[1].jpg' class='viewIMG'  onclick='viewPhoto(\"$most[1]\");'>";
} else {
    echo "-";
}
echo "</td>";
echo "<td>";
if ($most[2] != "-") {

    echo "<img src='$URL_LOGIN/events/$date2$idEvent/$most[2].jpg' class='viewIMG'  onclick='viewPhoto(\"$most[2]\");'>";
} else {
    echo "-";
}
echo "</td>";
echo "<td>";
if ($most[3] != "-") {

    echo "<img src='$URL_LOGIN/events/$date2$idEvent/$most[3].jpg' class='viewIMG'  onclick='viewPhoto(\"$most[3]\");'>";
} else {
    echo "-";
}
echo "</td>";
echo "<td>";
if ($most[4] != "-") {

    echo "<img src='$URL_LOGIN/events/$date2$idEvent/$most[4].jpg' class='viewIMG'  onclick='viewPhoto(\"$most[4]\");'>";
} else {
    echo "-";
}
echo "</td>";
echo "<td>";
if ($most[5] != "-") {

    echo "<img src='$URL/images/web/button_video.png'  onclick='viewVideo(\"$most[5]\");' style='width:100%;cursor:pointer;'>";
} else {
    echo "-";
}
echo "</td>";

echo "<td>";
if ($most[6] != "-") {

    echo "<img src='$URL/images/web/button_video.png'  onclick='viewVideo(\"$most[6]\");' style='width:100%;cursor:pointer;'>";
} else {
    echo "-";
}
echo "</td>";

echo "<td>";
if ($most[7] != "-") {

    echo "<img src='$URL/images/web/button_video.png'  onclick='viewVideo(\"$most[7]\");' style='width:100%;cursor:pointer;'>";
} else {
    echo "-";
}
echo "</td>";
echo "</tr>";
echo "</table>";

if ($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 4 || $_SESSION['USERTYPE']==6) {
    $CLD_CON->OpenRs("SELECT * FROM photos WHERE event_id=$ID");
    if ($CLD_CON->GetRsRows() == 0) {
        ?>
        <img src='images/icons/submenu/delete.png' <?php echo "onclick='deleteEvent($ID)'"; ?> style='width:15%;position:absolute;top:-20px;right:0px;' >
        <?php
    }
}

/*
  echo "<h3>Views</h3>";
  echo "<table style='border: 2px solid black;font-weight:bold;text-align:center;margin-left: 10%;'>";
  echo "<tr>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  //echo $tipus[0];
  echo "<img src='$URL/images/web/qrIMG.png' style='width:48px;height:48px;'>";
  echo "</td>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/webIMG.png' style='width:48px;height:48px;'>";
  //  echo $tipus[1];
  echo "</td></tr>";
  echo "<tr><td>";
  echo $q[0];
  echo "</td>";
  echo "<td>";
  echo $q[1];
  echo "</td></tr>";
  echo "<tr><td>";
  if ($most[0] != "-") {
  echo "<span class='link2' onclick='viewPhoto(\"$most[0]\");' >";
  echo $most[0];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td>";
  echo "<td>";
  if ($most[1] != "-") {
  echo "<span class='link2' onclick='viewPhoto(\"$most[1]\");'>";
  echo $most[1];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td></tr>";
  echo "</table>";



  echo "<h3>Shared Photos</h3>";
  echo "<table style='border: 2px solid black;font-weight:bold;text-align:center;margin-left: 10%;'>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/fcbk.png' style='width:48px;height:48px;'>";
  //echo $tipus[2];
  echo "</td>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/emailIMG.png' style='width:48px;height:48px;'>";
  //echo $tipus[3];
  echo "</td>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/twitter.png' style='width:48px;height:48px;'>";
  // echo $tipus[4];
  echo "</td></tr>";
  echo "<tr><td>";
  echo $q[2];
  echo "</td>";

  echo "<td>";
  echo $q[3];
  echo "</td>";

  echo "<td>";
  echo $q[4];
  echo "</td></tr>";
  echo "<tr><td>";
  if ($most[2] != "-") {
  echo "<span class='link2' onclick='viewPhoto(\"$most[2]\");'>";
  echo $most[2];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td>";

  echo "<td>";
  if ($most[3] != "-") {
  echo "<span class='link2' onclick='viewPhoto(\"$most[3]\");'>";
  echo $most[3];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td>";

  echo "<td>";
  if ($most[4] != "-") {
  echo "<span class='link2' onclick='viewPhoto(\"$most[4]\");'>";
  echo $most[4];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td></tr>";
  echo "</table>";

  echo "<h3>Shared Videos</h3>";
  echo "<table style='border: 2px solid black;font-weight:bold;text-align:center;margin-left: 10%;'>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/fcbk.png' style='width:48px;height:48px;'>";
  echo "</td>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/emailIMG.png' style='width:48px;height:48px;'>";
  echo "</td>";
  echo "<td style='border: 2px solid black;padding:2px;'> ";
  echo "<img src='$URL/images/web/twitter.png' style='width:48px;height:48px;'>";
  echo "</td></tr>";

  echo "<tr><td>";
  echo $q[5];
  echo "</td>";

  echo "<td>";
  echo $q[6];
  echo "</td>";

  echo "<td>";
  echo $q[7];
  echo "</td></tr>";
  echo "<tr><td>";
  if ($most[5] != "-") {
  echo "<span class='link2' onclick='viewVideo(\"$most[5]\");'>";
  echo $most[5];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td>";

  echo "<td>";
  if ($most[6] != "-") {
  echo "<span class='link2' onclick='viewVideo(\"$most[6]\");'>";
  echo $most[6];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td>";

  echo "<td>";
  if ($most[7] != "-") {
  echo "<span class='link2' onclick='viewVideo(\"$most[7]\");'>";
  echo $most[7];
  echo "</span>";
  } else {
  echo "-";
  }
  echo "</td></tr>";

  echo "</table>";
  //echo "<h3> Banner Clicks : $q[8]</h3>";
 * 
 */
echo "</div>";
echo "</div>";
?>