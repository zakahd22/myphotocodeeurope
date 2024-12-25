<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
$CLD_CON2 = clone($CLD_CON);
if (isset($_POST['id'])) {
    unset($_SESSION['ph']);
    $ID = $_POST['id'];
    $dongles_ids = [];
    $photos = [];
    $i = 0;

    $PAGE = 1;
    $limit = 0;
    
    $CLD_CON->OpenRs("SELECT * FROM App_boothDongle WHERE idBooth = $ID");
    while ($CLD_CON->FetchArray()) {
        $array[0] = $CLD_CON->GetArrayField("idDongle");
        $array[1] = $CLD_CON->GetArrayField("datetimeS");
        $array[2] = $CLD_CON->GetArrayField("datetimeF");
        $dongles_ids[$i] = $array;
        $i++;
    }

    foreach ($dongles_ids as $arrDongle) {

        if (empty($arrDongle[2])) {
            $dateF = "3000-01-01";
        } else {
            $dateF = $arrDongle[2];
        }
        /*
          if($_SESSION['USERTYPE']==4){
          $usID = $_SESSION['USERID'];
          $CLD_CON->OpenRs("SELECT p.code , p.event_id , p.Appusr_datetime , p.booth_id FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.booth_id = $arrDongle[0] AND p.Appusr_datetime BETWEEN '$arrDongle[1]' AND '$dateF' AND e.rental_id=$usID");

          }else{
          $CLD_CON->OpenRs("SELECT code , event_id , Appusr_datetime , booth_id FROM photos WHERE booth_id = $arrDongle[0] AND Appusr_datetime BETWEEN '$arrDongle[1]' AND '$dateF'");
          }
          while ($CLD_CON->FetchArray()) {
          echo $photocode . " , " . $CLD_CON->GetArrayField("booth_id") . "</br>";
          $photocode = $CLD_CON->GetArrayField("code");
          $event = $CLD_CON->GetArrayField("event_id");
          $datePhoto = $CLD_CON->GetArrayField("Appusr_datetime");
          $photos[$datePhoto] = [$photocode, $event, $datePhoto];
          echo $photocode . " , " . $CLD_CON->GetArrayField("booth_id") . "</br>";
          }
          }
         */
        $x = 0;
        if ($_SESSION['USERTYPE'] == 4) {
            $user = $_SESSION['USERID'];
            $CLD_CON->OpenRs("SELECT  p.event_id   FROM photos p LEFT JOIN events e ON e.id=p.event_id WHERE p.booth_id = $arrDongle[0]  AND  p.Appusr_datetime BETWEEN '$arrDongle[1]' AND '$dateF' AND e.rental_id=$user GROUP BY  p.event_id  ORDER BY p.event_id DESC");
        } else {
            $CLD_CON->OpenRs("SELECT  event_id   FROM photos WHERE booth_id = $arrDongle[0]  AND  Appusr_datetime BETWEEN '$arrDongle[1]' AND '$dateF' GROUP BY  event_id  ORDER BY event_id DESC");
        }

        while ($CLD_CON->FetchArray()) {
            $event = $CLD_CON->GetArrayField("event_id");
            $CLD_CON2->OpenRs("SELECT code , event_id , Appusr_datetime FROM photos WHERE booth_id = $arrDongle[0] AND  event_id=$event");
            while ($CLD_CON2->FetchArray()) {
                $photocode = $CLD_CON2->GetArrayField("code");
                $event = $CLD_CON2->GetArrayField("event_id");
                $datePhoto = $CLD_CON2->GetArrayField("Appusr_datetime");
                $photos[$datePhoto] = [$photocode, $event, $datePhoto];
            }
        }

        krsort($photos);

        foreach ($photos as $photo) {
            $ph[$i] = $photo;
            $i++;
        }
        $_SESSION['ph'] = $ph;
    }
    } else {
        $PAGE = $_POST['page'];
        $limit = $_POST['limit'];
        $ph = $_SESSION['ph'];
    }




    getPhotos($ph, $limit);
    $LIMITERPAGES = 24;
    $totalrows = sizeof($ph);
    if (($pages = floor($totalrows / $LIMITERPAGES) + 1) > 1) {
        echo "<div class='page-selector'><center><ul class='listpageSelector' STYLE='bottom: 76.8%;'>";
        echo "<li style='width:50px;'>$totalrows</li>";

        $fi = $totalrows % 24;
        $fi = $totalrows - $fi;
        if ($PAGE > $pages) {
            $PAGE = $pages;
        }
        if ($PAGE > 5) {
            $x = $PAGE - 5;
        } else {
            $x = 1;
        }

        if ($PAGE > $pages - 5) {
            if ($pages > 9) {
                $x = $pages - 9;
            }
            $lastpage = $pages;
        } else {
            if ($PAGE < 6) {
                $lastpage = 10;
            } else {
                $lastpage = $PAGE + 4;
            }
        }

        $l = $x * $LIMITERPAGES - $LIMITERPAGES;
        echo "<li class='pageSelectorFrist' onclick='setPagePhoto( 24 , 1)'> |&lsaquo;</li>";
        while ($x < $lastpage + 1) {
            if ($x == $PAGE) {
                echo "<li onclick='setPagePhoto($l , $x)'><b>$x</b></li>";
            } else {
                echo "<li onclick='setPagePhoto($l, $x)'>$x</li>";
            }
            $x++;
            $l = $l + $LIMITERPAGES;
        }

        echo "<li class='pageSelectorLast' onclick='setPagePhoto($fi , $pages)'>&rsaquo;| </li>";
        echo "</ul></center></div>";
    } else {
        echo "<div class='page-selector' style='text-align:center;width:20%;'><ul class='listpageSelector'>";
        echo "<li style='width:50px;'>$totalrows</li>";
        echo "</ul></div>";
    }

    function getPhotos($photos, $limit) {
        global $CLD_CON;
        global $URL_LOGIN;
        $limit_2 = $limit + 24;
        $videos = "";
        echo "<div class='inContent'>";
        echo "<div class='boxLeft'>";
        echo "<h1>Photos</h1>";
        echo "<div class='box' id='photoBox'>";
        while ($limit < $limit_2 && $limit < sizeof($photos)) {
            $photo[0] = $photos[$limit][0];
            $photo[1] = $photos[$limit][1];
            $CLD_CON->OpenRs("SELECT start_date FROM events WHERE id=$photo[1]");
            if ($CLD_CON->FetchArray()) {
                $creationDate = $CLD_CON->GetArrayField("start_date");
                $folder = $creationDate . $photo[1];

                echo "<div class='frames' style='height:55%;border:1px solid gray;margin:1%;width: 20%;' onclick='viewPhoto(\"$photo[0]\")'>";
                echo "<p style='font-size:10pt;text-align:center;'>$photo[0]</p>";
                echo "<div style='overflow:hidden;height:57%;width:100%;'>";
                echo "<img src='events/" . $folder . "/" . $photo[0] . ".jpg'  style='width:100%;height:200%;'>";
                echo"</div>";
                echo "<div style='width:100%;height:20%;'>";
                echo "<p>";
                getCounters($photo[0]);
                echo "</p>";
                $CLD_CON->OpenRs("SELECT Appusr_datetime FROM photos WHERE code='$photo[0]'");
                if ($CLD_CON->FetchArray()) {
                    $dd = $CLD_CON->GetArrayField("Appusr_datetime");
                    $d = date("F d, Y - H:i:s", strtotime($dd));
                    echo "<p style='text-align:center;font-size:9pt;'>$d</p>";
                }
                echo "</div></div>";
                $videos .= getVideo($_SERVER['DOCUMENT_ROOT'] . "/events/" . $folder . "/" . $photo[0] . ".mp4", $photo[0], ".mp4");
            }

            $limit++;
        }
        echo "</div>";
        echo "</div>";
        echo "<div class='boxRight'>";
        echo "<h1>Videos</h1>";
        echo "<div class='box'>";
        echo $videos;
        echo "</div>";
        echo "</div>";
        echo "</div>";



        //   print_r(error_get_last());
    }

    function getCounters($foto) {
        global $CLD_CON;
        global $URL;
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$foto' AND type_info=1");
        $type[1] = $CLD_CON->GetRsRows(); //QR
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$foto' AND type_info=2");
        $type[2] = $CLD_CON->GetRsRows(); //WEB
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$foto' AND type_info=3");
        $type[3] = $CLD_CON->GetRsRows(); //FACEBOOK
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$foto' AND type_info=4");
        $type[4] = $CLD_CON->GetRsRows(); //EMAIL
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$foto' AND type_info=5");
        $type[5] = $CLD_CON->GetRsRows(); //TWITTER;
        echo "<table width='100%;' style='text-align:center;'>";
        echo "<tr>";
        echo "<td style='width:18%;'>";
        echo "<img src='images/web/qrIMG.png' style='width:100%'>";
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo "<img src='images/web/webIMG.png' style='width:100%'>";
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo "<img src='images/web/fcbk.png' style='width:100%'>";
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo "<img src='images/web/emailIMG.png' style='width:100%'>";
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo "<img src='images/web/twitter.png' style='width:100%'>";
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td style='width:20%;'>";
        echo $type[1];
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo $type[2];
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo $type[3];
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo $type[4];
        echo "</td>";
        echo "<td style='width:20%;'>";
        echo $type[5];
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }

    function getVideo($u, $c, $ext) {
        if (file_exists($u)) {
            $r = "<div class='frames' style='height:50%;border:1px solid gray;margin:1%;width: 20%;' onclick='viewVideo(\"$c$ext\")'>";
            $r.= "<p style='font-size:10pt;text-align:center;'>$c</p>";
            $r.= "<div style='overflow:hidden;height:60%;width:100%;' >";
            $r.= "<img src='images/web/video_img.jpg' style='width:100%;'>";
            $r.="</div>";
            $r.= "<div style='width:100%;height:20%;'>";
            $r.= "<p>";
            $r.= getCountersVideo($c);
            $r.= "</p></div>";
            $r.= "</div>";
            return $r;
        } else {
            return "";
        }
    }

    function getCountersVideo($video) {
        global $CLD_CON;
        global $URL;
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$video' AND type_info=6");
        $type[6] = $CLD_CON->GetRsRows(); //FACEBOOK
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$video' AND type_info=7");
        $type[7] = $CLD_CON->GetRsRows(); //EMAIL
        $CLD_CON->OpenRs("SELECT id FROM CLD_estadistiques_photos  WHERE photo='$video' AND type_info=8");
        $type[8] = $CLD_CON->GetRsRows(); //twitter

        $r = "<table width='100%;' style='text-align:center;'>";
        $r.= "<tr>";
        $r.= "<td style='width:33%;'>";
        $r.= "<img src='images/web/fcbk.png' style='width:100%'>";
        $r.= "</td>";
        $r.= "<td style='width:33%;'>";
        $r.= "<img src='images/web/emailIMG.png' style='width:100%'>";
        $r.= "</td>";
        $r.= "<td style='width:33%;'>";
        $r.= "<img src='images/web/twitter.png' style='width:100%'>";
        $r.= "</td>";
        $r.= "</tr>";

        $r.= "<tr>";
        $r.= "<td style='width:33%;'>";
        $r.= $type[6];
        $r.= "</td>";
        $r.= "<td style='width:33%;'>";
        $r.= $type[7];
        $r.= "</td>";
        $r.= "<td style='width:33%;'>";
        $r.= $type[8];
        $r.= "</td>";
        $r.= "</tr>";
        $r.= "</table>";

        return $r;
    }

?>
