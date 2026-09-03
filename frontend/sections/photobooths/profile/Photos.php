<?php

// S'ha desactivat aquesta pagina el dia 19/10/2016 per mal funcionament.
// S'haurien d'invertir moltes hores per fer que aquesta pàgina funcioni,
// ja que s'ha implementat el trash events i el compres events per tan s'ha 
// de redefinir aquesta pàgina
// 
/*
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

//$PhotosController = new photosController();
//$photos = $PhotosController->getPhotosPBs($idPB);

$baseController = new baseController();
$baseController->createModel('App_boothDongle');
$baseController->createModel('events');
$baseController->createModel('photos');

$html = "";

if (isset($_POST['id'])){
    unset($_SESSION['ph']);
    $ID = $_POST['id'];
    $dongles_ids = [];
    $photos = [];
    $i = 0;

    $PAGE = 1;
    $limit = 0;
    
    $boothDongles = $baseController->App_boothDongleModel->boothDongles($ID);
    foreach ($boothDongles as $boothDongle){
        $array[0] = $boothDongle["idDongle"];
        $array[1] = $boothDongle["datetimeS"];
        $array[2] = $boothDongle["datetimeF"];
        $dongles_ids[$i] = $array;
        $i++;
    }

    foreach ($dongles_ids as $arrDongle) {
        if (empty($arrDongle[2])) {
            $dateF = "3000-01-01";
        } 
        else {
            $dateF = $arrDongle[2];
        }
        
        $user_id = "";
         $_SESSION['USERTYPE'] == 4? $user_id = TRUE : $user_id = FALSE;
        
        $request = $baseController->photosModel->getAllPhotosFromPbs($arrDongle[0], $arrDongle[1], $dateF, $user_id = false);
        $i=0;
        foreach ($request as $photo){
            $photocode = $photo["code"];
            $event = $photo["event_id"];
            $datePhoto = $photo["Appusr_datetime"];
            
            $photos[$datePhoto] = [$photocode, $event, $datePhoto];    
            
            $ph[$i]= $photos[$datePhoto];
            $i++;
        }
    }
    krsort($ph);
    $_SESSION['ph'] = $ph;
}
else {
    $PAGE = $_POST['page'];
    $limit = $_POST['limit'];
    $ph = $_SESSION['ph'];
}


$html .= getPhotos($ph, $limit, $event);

$LIMITERPAGES = 24;
$totalrows = sizeof($ph);

if (($pages = floor($totalrows / $LIMITERPAGES) + 1) > 1) {
    $html .= "<div class='page-selector'><center><ul class='listpageSelector' STYLE='bottom: 76.8%;'>";
    $html .=  "<li style='width:50px;'>$totalrows</li>";

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
    $html .= "<li class='pageSelectorFrist' onclick='setPagePhoto( 24 , 1)'> |&lsaquo;</li>";
    while ($x < $lastpage + 1) {
        if ($x == $PAGE) {
            $html .= "<li onclick='setPagePhoto($l , $x)'><b>$x</b></li>";
        } else {
            $html .= "<li onclick='setPagePhoto($l, $x)'>$x</li>";
        }
        $x++;
        $l = $l + $LIMITERPAGES;
    }

    $html .= "<li class='pageSelectorLast' onclick='setPagePhoto($fi , $pages)'>&rsaquo;| </li>";
    $html .= "</ul></center></div>";
} 
else {
    $html .=  <<<HTML
        <div class='page-selector' style='text-align:center;width:20%;'><ul class='listpageSelector'>
           <li style='width:50px;'>$totalrows</li>
        </ul></div>
HTML;
}

function getPhotos($photos, $limit) {
    global $CLD_CON;
    global $URL_LOGIN;
    global $baseController;
    
    $html .= "<link rel='stylesheet' href='sections/photobooths/resources/css/photos.css' type='text/css'>";
    
    $limit_2 = $limit + 24;
    $videos = "";
    
    $html .= <<<HTML
        <div class='inContent'>
        <div class='boxLeft'>
            <h1>Photos</h1>
        <div class='box' id='photoBox'>
HTML;
    
    while($limit < $limit_2 && $limit < sizeof($photos)) {
        
        $photo[0] = $photos[$limit][0];
        $photo[1] = $photos[$limit][1];
        
        $event = $baseController->eventsModel->getEvent($photo[1]);
        
        if($event){
            $creationDate = $event[0]["start_date"];
            $folder = $creationDate . $photo[1];
                        
            if(file_exists(G_PATH . "events/" . $folder . "/" . $photo[0] . ".jpg")){
                
                $wImg = 0;
                $hImg = 0;
                
                 list($wImg, $hImg) = getimagesize(G_PATH . "events/" . $folder . "/" . $photo[0] . ".jpg");
                 
                //segons les mides tindrem el tipus de foto 1:tira vertical   2: tira horitzontal   3:4x6 horitzontal   4:4x6 vertical
                    ////deixem un marge en les comparacions
                if(abs($wImg - 708) < 50){
                    $html .= <<<HTML
                    <div class='frames_1 frames_'>
                        <p style='text-align:center;'>$photo[0]</p>
                        <div class='div_tira_mostra_1' onclick='viewPhoto("$photo[0]")'>
                            <img class='imgTira_1' src='events/$folder/$photo[0].jpg'  title='Show Photo'>
                        </div>
                        <div style='width:100%;height:20%;'>
HTML;
                }
                else if(abs($hImg - 708) < 50){
                    $html .= <<<HTML
                    <div class='frames_2 frames_'>
                        <p style='text-align:center;'>$photo[0]</p>
                        <div class='div_tira_mostra_2' onclick='viewPhoto("$photo[0]")'>
                            <img class='imgTira_2' src='events/$folder/$photo[0].jpg'  title='Show Photo'>
                        </div>
                        <div style='width:100%;height:20%;'>
HTML;
                }
                else if(abs($hImg - 1416) < 50){
                    $html .= <<<HTML
                        <div class='frames_3 frames_'>
                            <p style='text-align:center;'>$photo[0]</p>
                            <div class='div_tira_mostra_3' onclick='viewPhoto("$photo[0]")'>
                                <img class='imgTira_3' src='events/$folder/$photo[0].jpg'  title='Show Photo'>
                            </div>
                            <div style='width:100%;height:20%;'>
HTML;
                }
                else if(abs($wImg - 1416) < 50){
                    $tipusPhoto = 4;
                }

                $html .= "<p>";    
                $html .= getCounters($photo[0]);
                $html .= "</p>";
                
                
                $photo_ = $baseController->photosModel->getPhoto($photo[0]);
                
                if($photo_){
                    $Appusr_datetime = $photo_[0]["Appusr_datetime"];
                    $d = date("F d, Y - H:i:s", strtotime($Appusr_datetime));
                    $html .= "<p style='text-align:center;font-size:9pt;'>$d</p>";
                }
//                $CLD_CON->OpenRs("
//                        SELECT Appusr_datetime 
//                        FROM photos 
//                        WHERE code='$photo[0]'");
                
//                if ($CLD_CON->FetchArray()) {
//                    $dd = $CLD_CON->GetArrayField("Appusr_datetime");
//                    $d = date("F d, Y - H:i:s", strtotime($dd));
//                    $html .= "<p style='text-align:center;font-size:9pt;'>$d</p>";
//                }
                $html .= "</div></div>";
            }
            $videos .=  getVideo($_SERVER['DOCUMENT_ROOT'] . "/events/" . $folder . "/" . $photo[0] . ".mp4" , $photo[0] , ".mp4" ); 
            
            
        }
        
        $limit++;
    }
    
    $html .= <<<HTML
        </div>
        </div>
        <div class='boxRight'>
        <h1>Videos</h1>
        <div class='box'>
        $videos
        </div>
        </div>
        </div>
HTML;
    
    return $html;
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
    
    
    $html .= <<<HTML
        <table width='100%;' style='text-align:center;'>
            <tr>
                <td style='width:18%;'>
                    <img src='images/web/qrIMG.png' style='width:100%'>
                </td>
                <td style='width:20%;'>
                    <img src='images/web/webIMG.png' style='width:100%'>
                </td>
                <td style='width:20%;'>
                    <img src='images/web/fcbk.png' style='width:100%'>
                </td>
                <td style='width:20%;'>
                    <img src='images/web/emailIMG.png' style='width:100%'>
                </td>
                <td style='width:20%;'>
                    <img src='images/web/twitter.png' style='width:100%'>
                </td>
            </tr>
            <tr>
                <td style='width:20%;'>
                    $type[1]
                </td>
                <td style='width:20%;'>
                    $type[2]
                </td>
                <td style='width:20%;'>
                   $type[3]
                </td>
                <td style='width:20%;'>
                    $type[4]
                </td>
                <td style='width:20%;'>
                    $type[5]
                </td>
            </tr>
        </table>
HTML;
    
    
    return $html;
}

function getVideo($u , $c , $ext){
    if(file_exists($u)){
        $r=  "<div class='frames_video' onclick='viewVideo(\"$c$ext\")'>";
        $r.=  "<p style='font-size:10pt;text-align:center;'>$c</p>";
        $r.= "<div class='div_video_conten'>";
        $r.= "<img src='images/web/video_img.jpg' style='width:100%;'>";
        $r.="</div>";
        $r.= "<div style='width:100%;height:20%;'>";
        $r.= "<p>";
        $r.= getCountersVideo($c);
        $r.= "</p></div>";
        $r.= "</div>";
        return $r;
    }
    else{
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

    $r=  "<table width='100%;' style='text-align:center;'>";
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

echo $html;
*/