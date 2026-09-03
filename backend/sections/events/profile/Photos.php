<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "common/Classes/StatisticsController.php";

function preparePhotos($baseController, $photos, $limit) {
    global $CLD_CON;
    global $URL_LOGIN;
    global $photos_array;
    global $ID;
    $boothType = NULL;
    $html = "";
    $limit_2 = $limit + 24;


    $pbs_id = $baseController->photosModel->getPhotoPBIdByCode($photos[0][0]);

    if($pbs_id) {
        $photoBooth = $baseController->App_boothsModel->getBoothId($pbs_id[0]["pbs_id"]);
        if (is_array($photoBooth) && count($photoBooth) > 0) {
            if(array_key_exists("type", $photoBooth[0])) {
                $boothType = $photoBooth[0]['type'];
            }
        }
    }
    

    $event=array();
    $html .= "<div class='row'>";
    while ($limit < $limit_2 && $limit < sizeof($photos)) {
        $photo[0] = $photos[$limit][0];
        $photo[1] = $photos[$limit][1];
        //20220125 posem un control per fer la consulta una vegada, no a cada foto...
        if(!count($event)){
            $event = $baseController->eventsModel->getEvent($photo[1]);
//                if($event[0]["id"]==40404){
                            $countersOtp = getCountersOptimized($event[0]["id"], $baseController);

//                            utils::log("logPhotos:".$countersOtp, "logasd");
//                }
        }

        if ($event) {
            $creationDate = $event[0]["start_date"];
            $user_id = $event[0]["rental_id"];
            $folder = $creationDate . $photo[1];

            if (file_exists(G_PATH . "events/" . $folder . "/" . $photo[0] . ".jpg")) {

                $wImg = 0;
                $hImg = 0;

                if($boothType == 'V'){
                    $tipusPhoto = 4;
                } else {
                    list($wImg, $hImg) = getimagesize(G_PATH . "events/" . $folder . "/" . $photo[0] . ".jpg");
                    //segons les mides tindrem el tipus de foto 1:tira vertical   2: tira horitzontal   3:4x6 horitzontal   4:4x6 vertical
                    ////deixem un marge en les comparacions
                    if (abs($wImg - 708) < 50) {//
                        $tipusPhoto = 1;
                    } else if (abs($hImg - 708) < 50) {//
                        $tipusPhoto = 2;
                    } else if (abs($hImg - 1416) < 50) {//
                        $tipusPhoto = 3;
                    } else if (abs($wImg - 1416) < 50) {//
                        $tipusPhoto = 4;
                    }
                }

                if ($tipusPhoto == 1) {
                    $html .= "<div class='frames_1 frames_'>";
                    $html .= "<p style='text-align:center;'>$photo[0]</p>";
                    $html .= "<div class='div_tira_mostra_1' onclick='viewPhoto(\"$photo[0]\")'>";
                    $html .= "<img class='imgTira_1' src='events/" . $folder . "/" . $photo[0] . ".jpg'  title='Show Photo'>";
                    $html .= "</div>";
                    $html .= "<div style='width:100%;height:20%;'>";
                } elseif ($tipusPhoto == 2) {
                    $html .= "<div class='frames_2 frames_'>";
                    $html .= "<p style='text-align:center;'>$photo[0]</p>";
                    $html .= "<div class='div_tira_mostra_2' onclick='viewPhoto(\"$photo[0]\")'>";
                    $html .= "<img class='imgTira_2' src='events/" . $folder . "/" . $photo[0] . ".jpg'  title='Show Photo'>";
                    $html .= "</div>";
                    $html .= "<div style='width:100%;height:20%;'>";
                } elseif ($tipusPhoto == 3) {
                    $html .= "<div class='frames_3 frames_'>";
                    $html .= "<p style='text-align:center;'>$photo[0]</p>";
                    $html .= "<div class='div_tira_mostra_3' onclick='viewPhoto(\"$photo[0]\")'>";
                    $html .= "<img class='imgTira_3' src='events/" . $folder . "/" . $photo[0] . ".jpg'  title='Show Photo'>";
                    $html .= "</div>";
                    $html .= "<div style='width:100%;height:20%;'>";
                } elseif ($tipusPhoto == 4) {
                    $html .= "<div class='frames_4 frames_'>";
                    $html .= "<p style='text-align:center;'>$photo[0]</p>";
                    $html .= "<div class='div_tira_mostra_4' onclick='viewPhoto(\"$photo[0]\")'>";
                    $html .= "<img class='imgTira_4' src='events/" . $folder . "/" . $photo[0] . ".jpg'  title='Show Photo'>";
                    $html .= "</div>";
                    $html .= "<div style='width:100%;height:20%;'>";
                } else {
                    $html .= "<div class='col-md-2 col-xs-4'>";
                    $html .= "<p style='text-align:center;'>$photo[0]</p>";
                    $html .= "<div class='' onclick='viewPhoto(\"$photo[0]\")'>";
                    $html .= "<img class='img-thumbnail' src='events/" . $folder . "/" . $photo[0] . ".jpg'  title='Show Photo'>";
                    $html .= "</div>";
                    $html .= "<div style='width:100%;height:20%;'>";
                }

                $html .= "<p>";
                $photos_array[] = $photo[0];
                
//                if($event[0]["id"]==40404){
//                   $html .= $photo[0]." ".$event[0]["id"]." ".$countersOtp[$photo[0]][1]; 
                   $html .= getCountersOptHtml($photo[0],$countersOtp);
//                }else{
//                   $html .= getCounters($photo[0], $baseController); //20220126 obsoleta, triga una eternitat
//                }
               
                //$html .= getCounters($photo[0], $baseController);
                $html .= "</p>";

                $photo = $baseController->photosModel->getPhoto($photo[0]);
                if ($photo) {
                    $dd = $photo[0]["Appusr_datetime"];
                    $inappropiated = $photo[0]["flag"];
                    $photo_code = $photo[0]["code"];

                    $d = date("F d, Y - H:i:s", strtotime($dd));
                    $html .= "<p style='text-align:center;'>$d</p>";

                    if ($inappropiated == 1) {
                        $html .= "<p style='text-align:center;color:#610B0B;cursor:pointer;font-weight:bolder;' id='flag" . $photo_code . "1'              onClick='setFlag(\"" . $photo_code . "\" , 1);' title='Click here to set'>INAPPROPRIATE PHOTO</p>";
                        $html .= "<p style='text-align:center;color:#21610B;cursor:pointer;display:none;font-weight:bolder;' id='flag" . $photo_code . "0' onClick='setFlag(\"" . $photo_code . "\" , 0);' title='Click here to set'>APPROPRIATE PHOTO</p>";
                    } else {
                        $html .= "<p style='text-align:center;color:#610B0B;cursor:pointer;display:none;font-weight:bolder;' id='flag" . $photo_code . "1' onClick='setFlag(\"" . $photo_code . "\" , 1);' title='Click here to set'>INAPPROPRIATE PHOTO</p>";
                        $html .= "<p style='text-align:center;color:#21610B;cursor:pointer;font-weight:bolder;' id='flag" . $photo_code . "0'              onClick='setFlag(\"" . $photo_code . "\" , 0);' title='Click here to set'>APPROPRIATE PHOTO</p>";
                    }
                }
                $html .= "</div></div>";
            }
        }

        $limit++;
    }
    $html .= "</div>";
    $photo_url = glob(G_PATH . "events/{$folder}/*.jpg");
//    utils::log($photo_url, "logasd");
    foreach ($photo_url as $url) {
        $photo_url1[] = substr($url, -28);
    }
    $url = substr($photo_url[0], 0, -14);
    $photo_url1 = implode(",", $photo_url1);
//    $files = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Frames/*");
//    utils::log($user_id, "logasd");
//    if($user_id == 7 || $user_id == 305){
//        $html .= "<input class='boto_face facebookUploadSDk' title='SHARE ALL PHOTOS TO FACEBOOK' type_shared='4' code='$ID' fileType='photo' id='face_button_start' PhotoUrl='$photo_url1' url='$url' style='background-image: url(\"images/web/fcbk.png\");background-size: 32px 32px;float:left;width: 32px;height: 32px;margin-left: 594px;margin-right: 8px;cursor: pointer;top: -509px;position: relative;display: inline; border:0px;'>";
////        utils::log("ad", "logasd");
//    }
    return $html;
    //   print_r(error_get_last());
}

function listvideo($path, $baseController) {
    $ar = Array();
    $z = 0;
    $dir = opendir($path);
    $html = "";
    // Leo todos los ficheros de la carpeta
    while ($elemento = readdir($dir)) {
        // Tratamos los elementos . y .. que tienen todas las carpetas
        if ($elemento != "." && $elemento != ".." && $elemento != "background.jpg" && $elemento != "banner.jpg" && $elemento != "banner.png" && $elemento != "banner.gif") {
            if (!is_dir($path . $elemento)) {
                $trozos = explode(".", $elemento);
                $extension = end($trozos);
                if ($extension == "wmv" || $extension == "mp4") {
                    $ar[] = $elemento;
                }
            }
        }
    }
    closedir($dir);
    krsort($ar);
    global $photos_array;
    foreach ($photos_array as $elemento) {
        $trozos = explode(".", $elemento);
        if (file_exists($path . "/" . $elemento . ".mp4") || file_exists($path . "/" . $elemento . ".wmv")) {
            $html .= "<div class='frames_video' onclick='viewVideo(\"$elemento\")'>";
            $html .= "<p style='text-align:center;'>$trozos[0]</p>";
            $html .= "<div class='div_video_conten'>";
            $html .= "<img src='images/web/video_img.jpg' style='width:100%;'>";
            $html .= "</div>";
            $html .= "<div style='width:100%;height:20%;'>";
            $html .= "<p>";
            $html .= getCountersVideo($trozos[0], $baseController);
            $html .= "</p></div>";
            $html .= "</div>";
        }
    }
    return $html;
}

function getCountersOptimized($eventId, $baseController) {
    $stdController = new StatisticsController();
   
    $counterArrayByCodeAndType = array();
    $countersArray = $stdController->getStatisticsAllByEvent($eventId); 
//    utils::log("logPhotos getCountersOtim cd".$countersArray, "logasd");
    
    
    return  $countersArray;
    
    
    
    
    
}

function getCountersOptHtml($photoCode,$countersOtp) {
    
    $html = "";   
    $html .= "<table width='100%;' style='text-align:center;'>";
    $html .= "<tr>";
    $html .= "<td style='width:18%;'>";
    $html .= "<img src='images/web/qrIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/webIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/fcbk.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/emailIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/twitter.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td style='width:20%;'>";
    if(!isset($countersOtp[$photoCode][1])){$countersOtp[$photoCode][1]=0;}
    $html .= $countersOtp[$photoCode][1];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    if(!isset($countersOtp[$photoCode][2])){$countersOtp[$photoCode][2]=0;}
    $html .= $countersOtp[$photoCode][2];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    if(!isset($countersOtp[$photoCode][3])){$countersOtp[$photoCode][3]=0;}
    $html .= $countersOtp[$photoCode][3];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    if(!isset($countersOtp[$photoCode][4])){$countersOtp[$photoCode][4]=0;}
    $html .= $countersOtp[$photoCode][4];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    if(!isset($countersOtp[$photoCode][5])){$countersOtp[$photoCode][5]=0;}
    $html .= $countersOtp[$photoCode][5];
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "</table>";
//    utils::log("logPhotos code:".$photoCode, "logasd");

    return $html;
}

//20220125 obsoleta, tarda molt. La substituïm per getCountersOptimized()
function getCounters($foto, $baseController) {
    $stdController = new StatisticsController();
    $html = "";
    for ($i = 1; $i <= 5; $i++) {
//        $count_ = $baseController->CLD_estadistiques_photosModel->getEstadistiquesPhotoForEvents($foto, $i);
        $count_ = $stdController->getStatisticsPhotoType($foto, $i); //20220125 
        //$count_ = $stdController->getStatisticsPhotoTypeFromTable($foto, $i); //directament de la taula es molt pitjor ;)
        if (!$count_[0]["numShow"]) {
            $count_[0]["numShow"] = 0;
        }
        $type[$i] = $count_[0]["numShow"];
    }

    $html .= "<table width='100%;' style='text-align:center;'>";
    $html .= "<tr>";
    $html .= "<td style='width:18%;'>";
    $html .= "<img src='images/web/qrIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/webIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/fcbk.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/emailIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= "<img src='images/web/twitter.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td style='width:20%;'>";
    $html .= $type[1];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= $type[2];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= $type[3];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= $type[4];
    $html .= "</td>";
    $html .= "<td style='width:20%;'>";
    $html .= $type[5];
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "</table>";

    return $html;
}

function getCountersVideo($video, $baseController) {
    $stdController = new StatisticsController();
    global $CLD_CON;
    global $URL;
    $html = "";

    for ($i = 6; $i <= 8; $i++) {
//        $count_ = $baseController->CLD_estadistiques_photosModel->getEstadistiquesPhotoForEvents($video, $i);
        $count_ = $stdController->getStatisticsPhotoType($video, $i);
        if (!$count_[0]["numShow"]) {
            $count_[0]["numShow"] = 0;
        }
        $type[$i] = $count_[0]["numShow"];
    }

    $html .= "<table width='100%;' style='text-align:center;'>";
    $html .= "<tr>";
    $html .= "<td style='width:33%;'>";
    $html .= "<img src='images/web/fcbk.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:33%;'>";
    $html .= "<img src='images/web/emailIMG.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "<td style='width:33%;'>";
    $html .= "<img src='images/web/twitter.png' style='width:100%'>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td style='width:33%;'>";
    $html .= $type[6];
    $html .= "</td>";
    $html .= "<td style='width:33%;'>";
    $html .= $type[7];
    $html .= "</td>";
    $html .= "<td style='width:33%;'>";
    $html .= $type[8];
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "</table>";

    return $html;
}

function getHeaders() {
    $html = "";
    $html .= "<link rel='stylesheet' href='sections/events/resources/css/photosvideos.css' type='text/css'>";
    $html .= "<script src='sections/events/resources/js/photos&videos.js'></script>";
    $html .= "<script src='assets/js/facebook.js'></script>";
    return $html;
}

function getPaginator($totalrows, $LIMITERPAGES, $current_page) {
    $pages = ((int) ($totalrows / $LIMITERPAGES)) + (($totalrows % $LIMITERPAGES) == 0 ? 0 : 1);
    $html = "<div><center><ul class='listpageSelector'>";
    $html .= "<li style='width:50px;'><p>{$totalrows}</p></li>";

    if ($pages > 1) {
        $total_showed_pages = 9;
        $page_offset = 4;

        //Define paginator last page
        if ($pages > $total_showed_pages) {
            if (($current_page + $page_offset) > $pages) {
                $last_page = $pages;
            } elseif (($current_page + $page_offset) < $total_showed_pages) {
                $last_page = $total_showed_pages;
            } else {
                $last_page = $current_page + $page_offset;
            }
        } else {
            $last_page = $pages;
        }

        //Define paginator start page
        if ($pages > $total_showed_pages) {
            if ($current_page <= $page_offset) {
                $start_page = 1;
            } elseif (($current_page + $page_offset) > $pages) {
                $start_page = ($pages - $total_showed_pages) + 1;
            } else {
                $start_page = $current_page - $page_offset;
            }
        } else {
            $start_page = 1;
        }

        $html .= "<li class='pageSelectorFrist' onclick='setPagePhoto2($LIMITERPAGES, 1)'> <p>|&lsaquo;</p></li>";

        $start_from_limit = ($start_page * $LIMITERPAGES) - $LIMITERPAGES;
        for ($page = $start_page; $page <= $last_page; $page++) {
            $active = "";
            if ($page == $current_page) {
                $html .= "<li><p class='paginator_selected'>{$page}</p></li>";
            } else {
                $html .= "<li class='paginator_hover' onclick='setPagePhoto2($start_from_limit ,$page)'><p>{$page}</p></li>";
            }
            $start_from_limit = $start_from_limit + $LIMITERPAGES;
        }

        $html .= "<li class='pageSelectorLast' onclick='setPagePhoto2($start_from_limit, $pages)'><p>&rsaquo;|</p> </li>";
        $html .= "</ul></center></div>";
    }

    return $html;
}

function getDivTopBar($compressed, $totalrows, $LIMITERPAGES, $descompressed, $PAGE) {
    global $ID;
    global $start_date;

    $html = "";
    $html .= "<div id='topBar'>";

        $html .= "<div id='top_0'>";
        $html .= "</div>";
        $html .= "<div id='top_1'>";
        $photo_url = "https://127.0.0.1/myphotocode/events/2015100514155/PAROYUBSPU.jpg";
        if(empty($compressed) || $descompressed) {
            $html .= "<div style='width:138px;height:40px;padding:2px;border:1px solid black;border: 3px solid gray;background-color: white;border-top: none;'>";
            $html .= "<input type='button' class='miniDownload' id='downloadAll' title='DOWNLOAD ALL PHOTOS & VIDEOS'  style='margin-left:8px;top: 0px;float:left;' idEvent='{$ID}'>";
            if ($_SESSION['USERTYPE'] < 5  || $_SESSION['USERTYPE']==6) {
                $html .= "<input type='button' class='miniImport' title='IMPORT PHOTOS FROM OTHER EVENT' style='margin-left:8px;top: 0px;float:left;' onclick='edit(30 , {$ID});'>";
            }

//            utils::log($user_id, "logasd");
//            if($user_id == 7){
//                $html .= "<input class='boto_face facebookUploadSDk' style='background-image: url(\"images/web/fcbk.png\")'title='SHARE ALL PHOTOS TO FACEBOOK' type_shared='3' code='$ID' fileType='photo' id='face_button_start' PhotoUrl='$photo_url'>";
//
//            }
        $html .= "</div>";
    }

    $html .= "</div>";
    $html .= "<div id='top_2'>";
    $html .= getPaginator($totalrows, $LIMITERPAGES, $PAGE);
    $html .= "</div>";
    $html .= "</div>";
    return $html;
}

/*
  Fer new photosVideos passant-l'hi la seccio des don s'ha cridat per coneixe el context
  i que el contruct agafi el post
 */
$html = "";
$photos_compress = 1;
$descompressed = false;
$baseController = new baseController();
$baseController->createModel('App_boothDongle');
$baseController->createModel('events');
$baseController->createModel('photos');
//$baseController->createModel('CLD_estadistiques_photos');
$baseController->createModel('App_booths');

if (isset($_POST['id'])) {
    unset($_SESSION['ph']);
    $ID = $_POST['id'];
    $PAGE = 1;
    $limit = 0;
    $photos = [];
    $i = 0;

    $photos = $baseController->photosModel->getPhotos($ID);

    foreach ($photos as $photo) {
        $photocode = $photo["code"];
        $event = $photo["event_id"];
        $datePhoto = $photo["Appusr_datetime"];
        $photos[$i] = [$photocode, $event, $datePhoto];
        $i++;
    }
//    $events  = $baseController->eventsModel->getEventFromEventsPhotos($ID);
    $_SESSION['ph'] = $photos;
    $_SESSION['id_event'] = $ID;
} else {
    $PAGE = $_POST['page'];
    $limit = $_POST['limit'];
    $photos = $_SESSION['ph'];
    $ID = $_SESSION['id_event'];
}

$events = $baseController->eventsModel->getEvent($ID);
$start_date = $events[0]["start_date"];
$compressed = $events[0]["compressed"];
$trashed = $events[0]["trashed"];

$html .= getHeaders();
$countPhotos = count($photos);
if (count($photos) == 0) {
    $html .= "<div class='empty_conten'>";
    if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
        $html .= "<div style='clear: both'><input type='button' class='miniImport' title='IMPORT PHOTOS FROM OTHER EVENT' style='margin-left:100px; top: 8px;float:left;' onclick='edit(30 , {$ID});'></div><br>";
    }
    $html .= "<div class='empty_conten' style='margin-top: 20px;' >This event does not contain any photo</div>";
    $photos_compress = 0;
} else {
    if ($compressed > 0) {
        $photos_pDelete = $baseController->photosModel->getCLD_pDeleteEvents_comprimides($ID);
        //nou correcció if ($photos_pDelete[0]['counter'] == 0) { //20220124 n'hi ha zero comprimides, no cal descomprimir
        //antic corregit eloi if ($photos_pDelete[0]['counter'] > 0) {
            //$descompressed = false;
            //nou correcció $descompressed = true;
            
        //}
        if (!file_exists("../../../events/compressed_events/".$ID."_compressed.zip")){
            $descompressed = true;
            $is_empty = (bool) (count(scandir("../../../events/" . $start_date . $ID)) == 2);
            if(!file_exists("../../../events/" . $start_date . $ID) || $is_empty){ //20220124 hi ha la tira de directoris buits, volem que avisi igual que ho fa si no existeix el directori
                $html .= "<div class='inContent'><div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Warning!</strong> This event is OLD. If you want to download all photos, please contact <a href='mailto::main@dc-image.com?subject=Old event Id: ".$id."&body=Hi, I tried to see/open an old event (Event id: ".$id.")! But could not open it. Can you help / check it please?'>main@dc-image.com</a></div></div>";
                echo $html;die();
            }
        }
        
    } else {
        $descompressed = true;
    }
    $photos_array = array();
    $totalrows = sizeof($photos);
    $LIMITERPAGES = 24;

    $html .= getDivTopBar($compressed, $totalrows, $LIMITERPAGES, $descompressed, $PAGE);
    $html .= "<div class='inContent'>";

    $html .= "<div id='photoBoxA'>";
    if (!empty($trashed)) {
        $html .= "<h3>This event you are looking for has expired. Sorry for the inconvenience.</h3>";
    } else {
        if (!empty($compressed)) {
            if (!$descompressed) {
                $html .= "<img id='roda' style='text-align:center; margin-right:0px; margin-left:0px;' src='images/web/loading.gif' class='loading'>";
                $html .= "<h3 id='text_recovering' style='width:100%;text-align:center;'> Recovering photos...</h3>";
            }
        }
    }
    $html .= "</div>";

    if ($descompressed) {
        $html .= "<div class='boxLeftA'>";
        $html .= "<h1 id='title_box_1'>Photos</h1>";
        $html .= preparePhotos($baseController, $photos, $limit);
        $html .= "</div>";
        $html .= "<div class='boxRightA'>";
        $html .= "<h1>Videos</h1>";
        $html .= "<div id='group_videos'>";
        if ($events) {
            $folder = "../../../events/" . $start_date . $ID . "/";
        }
        $html .= listvideo($folder, $baseController);
        $html .= "</div>";
        $html .= "</div>";
    }
    $html .= "</div>";
}
//exit();
$html .= <<<HTML
    <script>
    function setFlag(photo, flag){
        var f;
        if (flag === 0) {
            f = 1;
        }
        if (flag === 1) {
            f = 0;
        }
        var ajaxData = {nflag: f, photo: photo};

        $.ajax({
            url: 'edit/functions/photos/setFlag.php',
            type: 'POST',
            //Ajax events
            success: function() {
                $("#flag" + photo + flag).hide();
                $("#flag" + photo + f).show();
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });

    }
HTML;

if ($photos_compress != 0) {
//    $photos_pDelete = $baseController->photosModel->getCLD_pDeleteEvents_comprimides($ID);
//    if ($photos_pDelete[0]['counter'] == 0) { //n'hi ha zero comprimides, no cal descomprimir
//   
//        $descompressed = true;
//
//    }
    if (!$descompressed) {
        $html .= "descompressEvent({$ID}, {$start_date});";
    }
    
}
$html .= "//pc. ".$photos_compress."  d:".$descompressed."  cp.".$countPhotos;
$html .= "</script>";

echo $html;
 
?>