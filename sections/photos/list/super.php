<?php
ini_set('memory_limit', '256M');
require_once G_PATH . "common/Classes/StatisticsController.php";

$baseController = new baseController();
$baseController->createModel('photos');
$baseController->createModel('events');

$stdController = new StatisticsController();

$photoCode = false;

if (isset($_POST['filPage'])) {
    $photoCode = $_SESSION['photocode'];
} else {
    if (isset($_POST['fil'])) {
        $photoCode = $_POST['code'];
        $_SESSION['photocode'] = $photoCode;
    }
}

$baseController->photosModel->setLimit($LIMIT);    
//$photos = $baseController->photosModel->getPhotos($photoCode);
$result = $baseController->photosModel->getAllFromPhotos($photoCode);

$events = $result['events'];
$photos = $result['photos'];


$select_nolimit = "SELECT * FROM photos $where ORDER BY Appusr_datetime DESC";
//$CLD_CON2 = clone($CLD_CON);
echo "<div class='inContent'>";

if(!empty($photos)){
    for($i=0; $i < count($photos); $i++) {
        $photoCode = $photos[$i]["code"];
        $eID = $photos[$i]["event_id"];
        $booth = $photos[$i]["booth_id"];
        $photoDate = $photos[$i]["Appusr_datetime"];
        $photoDate = date("F d, Y  H:i:s", strtotime($photoDate));
        
        $eTitle = $events[$i]["title"];
        $eDate = $events[$i]["start_date"];
        $rID = $events[$i]["rental_id"];
        $trashed = $events[$i]["trashed"];
        
        utils::log($trashed, "logTrashed");

//        Check if the video exists
        $video = false;
        if (file_exists(G_PATH . "events/$eDate$eID/$photoCode.mp4")) {
            $video = true;
            $videoLink = "$photoCode.mp4";
        }
        if (file_exists(G_PATH . "events/$eDate$eID/$photoCode.wmv")) {
            $video = true;
            $videoLink = "$photoCode.wmv";
        }
        
        echo "<ul class='regPhotoUL'>";
        echo "<li style='width:10%; line-height:48px; cursor:pointer;'  class='link' title='Code Photo' onclick='viewPhoto(\"$photoCode.jpg\")'>";
        echo $photoCode;
        echo "</li>";
        echo "<li style='width:20%; line-height:48px' title='Date'>";
        echo $photoDate;
        echo "</li>";

        $type = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $count = $stdController->getStatisticsPhoto($photoCode);

        foreach ($count as $estadistica) {
            $type[$estadistica["type_info"]] = $estadistica["numShow"];
        }

        $facebook = $type[3] + $type[6];
        $mail = $type[4] + $type[7];
        $twitter = $type[5] + $type[8];

        echo "<li><img src='images/web/qrIMG.png' class='photosListSocialIcons' title='{$type[1]}'></li>";
        echo "<li><img src='images/web/webIMG.png' class='photosListSocialIcons' title='{$type[2]}'></li> ";
        echo "<li><img src='images/web/fcbk.png' class='photosListSocialIcons' title='{$facebook}'></li>";
        echo "<li><img src='images/web/emailIMG.png' class='photosListSocialIcons' title='{$mail}'></li>";
        echo "<li><img src='images/web/twitter.png' class='photosListSocialIcons' title='{$twitter}'></li>";

        if ($video) {
            $html = <<<HTML
            <li style='width: 10%; text-align:center;margin-right: 7%; margin-left: 2%;'>
                <img src='images/web/button-video.png' class='photosListSocialIcons videoIco' onclick='viewVideo("$videoLink")' title='Show Video'> 
            </li>
HTML;
            echo $html;
        } else {
            echo "<li style='width: 10%; text-align:center;margin-right: 7%;margin-left: 2%; line-height:35px '> No have Video</li>";
        }

        echo "<li style='line-height:48px; width: 15%;' title='Event Name'>";
        echo "<span class='link' onclick='openLink(\"Events\" , $eID);'>" . $eTitle . "</span>";
        echo "</li>";
        if($trashed){
             echo "<li style='width: 10%; text-align:center;margin-right: 7%; line-height:35px '> *Expired* </li>";
        }
        echo "</ul>";
    }
} else{
    echo "No matches found";
}
echo "</div>";

$s = "photos";
$color = "#FF7400";
include '../../pagescount.php';
?>

