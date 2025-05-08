<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('event_backgrounds');
$baseController->createModel('CLD_questions');
$baseController->createModel('CLD_questions_emails');
$baseController->createModel('CLD_banners');

$ID = $_POST['id'];
$rnd = rand(200000, 9999999);
$events = $baseController->eventsModel->getEvent($ID);
//$CLD_CON2 = clone($CLD_CON);
//$CLD_CON->OpenRs("SELECT * FROM events WHERE id=$ID");
//if ($CLD_CON->FetchArray()){
if ($events){
    $eventDate = $events[0]['start_date'];    
    $date2 = substr($eventDate , 0, 4) . "-" . substr($eventDate , 4, 2) . "-" . substr($eventDate , 6, 2);
    $eventDateFormat = substr($eventDate, 4, 2) . "/" . substr($eventDate, 6, 2) . "/" . substr($eventDate, 0, 4);
    $eventDateFormat = date("F d, Y", strtotime($eventDateFormat));
    $eventTitle = $events[0]["title"];
    $owner = $events[0]["rental_id"];
    $isPrivate = $events[0]["private"];
    $background_id = $events[0]["background_id"];
}

if ($background_id != 99){
    if ($background_id == 0) {
        $i = "ok";
        $image = "assets/images/backgrounds/background-default.jpg?version=$rnd";
    } else {
        $eventBackground = $baseController->event_backgroundsModel->getBackground($background_id);
//        $CLD_CON->OpenRs("SELECT ev.image_url , ev.color , ev.repeat FROM event_backgrounds ev WHERE ev.id=$background_id");
//        if ($CLD_CON->FetchArray()) {
        if ($eventBackground) {
            $color = $eventBackground[0]['color'];
            $i = $eventBackground[0]['image_url'];
            $image = "assets/images/backgrounds/" . $eventBackground[0]['image_url'] . "?version=$rnd";
            $repeat = $eventBackground[0]['repeat'];
        }
    }
    $sty = "";
    $sty .= "<style>";
    $sty .= ".background-photo{";
    if (!empty($i)) {
        $sty .="background-image: url('$image');";
        $bg = "background-image: url('$image');";
    }
    $sty .= "background-color: $color;";
    $bgc = "background-color: $color;";
    $sty .= "width:100%;";
    $sty .="height:100%;";
    $sty .= "overflow:hidden;";
    $sty .= "overflow-y: auto;";
    $sty .= "}";
} else {
    $backgroundurl = "events/" . $eventDate . $ID . "/background.jpg?version=$rnd";
    $sty .= "<style>";
    $sty .= ".background-photo{";
    $sty .= "background-image: url('$backgroundurl');";
    $bg = "background-image: url('$backgroundurl');";
    $bgc = "";
    $sty .= "width:100%;";
    $sty .= "height:100%;";
    $sty .= "overflow:hidden;";
    $sty .= "overflow-y: auto;";
    $sty .= "}";
}
$sty .= ".allBg{";
$sty .= $bg;
$sty .= $bgc;
$sty .= "}";
$sty .= "</style>";


echo "<div class='background-photo'>";

echo "<img src='images/web/defaultTira.jpg' style='margin-left:7.5%;margin-top:20px;display:inline;float:left;height: 90%;'>";
echo "<div style='display:inline;float:left;margin-left:20px;_margin-top:20px;width:50%;'>";
echo "<h1 style='margin-left:20px;text-shadow:2px 2px 0px black;color:white;'>$eventTitle <img src='images/web/title_cloud.png' style='cursor:pointer;height:33px;' onClick='edit(14, $ID);'></h1>";
echo "<h2 style='margin-left:20px;text-shadow:2px 2px 0px black;color:white;'>Photo Date ($eventDateFormat)</h2>";

echo "<div class='actions1'>";
echo "<div class='share' id='photoShare'  style='margin-left:20px;width: 400px;'>";
echo "<a href='#'><img  src='images/web/downloadLook.png' class='photoButton' style='margin-left: 4%;width:70px;'></a>";
echo "<a  href='#' ><img class='photoButton' id='facebook'  src='images/web/button-facebook.png' style='width:70px;'/></a>";
echo "<a  href='#'><img class='photoButton' id='email'  src='images/web/button-email.png' style='width:70px;' /></a>";
echo "<a  href='#'><img class='photoButton' id='twitter'  src='images/web/button-twitter.png' style='width:61px;'/></a></center>";
echo "</div>";

echo "<div class='share' id='videoShare' style='float:left;margin-left:20px;width: 470px;'>";
echo "<a href='#'><img  src='images/web/downloadLook.png' class='photoButton' style='margin-left:-3px;width:70px'></a>";
echo "<a  HREF='#'><img class='photoButton' id='video'  src='images/web/button-video.png'  style='margin-left: -3px;width:55px;'/></a>";
echo "<a  href='#'><img class='photoButton' id='facebook'  src='images/web/button-facebook.png' style='margin-left: -3px;width:70px;'/></a>";
echo "<a  href='#'><img class='photoButton' id='email'  src='images/web/button-email.png' style='margin-left: -3px;width:70px;'/></a>";
echo "<a href='#'><img class='photoButton' id='twitter'  src='images/web/button-twitter.png' style='margin-left: -3px;width:61px;'/></a>";
echo "</div>";

echo "</div>";

$banners = $baseController->eventsModel->getEvent($ID);
//$CLD_CON->OpenRs("SELECT CLD_banner , CLD_banner_URL FROM events WHERE id=$ID");
//$CLD_CON->FetchArray();
$banner = $banners[0]["CLD_banner"];
echo "<div style='display:block;float:left;width: 100%;'><center>";
if($banner == 1){
    $banner_url = $banners[0]["CLD_banner_URL"];
    $banner_displayed = false;
    $banner_path = G_PATH . "events/" . $eventDate . $ID . "/banner";
    
    // Open URL link if it exists
    if(!empty($banner_url)){
        echo "<a href='$banner_url' target='_blank'>";
    }
    
    // Array of possible extensions to check (lowercase only)
    $extensions = ['jpg', 'jpeg', 'gif'];
    $banner_found = false;
    
    // Loop through possible extensions
    foreach($extensions as $ext) {
        // Check lowercase version
        if(file_exists($banner_path . '.' . $ext)) {
            echo "<img style='margin-left:0px;margin-top:25px;margin-bottom:25px;' class='banner' 
                 src='events/" . $eventDate . $ID . "/banner.{$ext}?version=$rnd' 
                 onerror=\"this.onerror=null;this.src='images/web/banners/banner-default.gif?version=$rnd';\">";
            $banner_displayed = true;
            break;
        }
        
        // Check uppercase version
        $upper_ext = strtoupper($ext);
        if(file_exists($banner_path . '.' . $upper_ext)) {
            echo "<img style='margin-left:0px;margin-top:25px;margin-bottom:25px;' class='banner' 
                 src='events/" . $eventDate . $ID . "/banner.{$upper_ext}?version=$rnd' 
                 onerror=\"this.onerror=null;this.src='images/web/banners/banner-default.gif?version=$rnd';\">";
            $banner_displayed = true;
            break;
        }
    }
    
    // If no banner files found, display the default banner
    if(!$banner_displayed) {
        echo "<img style='margin-left:0px;margin-top:25px;margin-bottom:25px;' class='banner' 
             src='images/web/banners/banner-default.gif?version=$rnd'>";
    }

    // Close URL link if it was opened
    if(!empty($banner_url)){
        echo "</a>";
    }
}
elseif ($banner == 0){
    $banner = $baseController->CLD_bannersModel->getEventCloudBanner($owner, $date2);
//    $CLD_CON->OpenRs(
//            "SELECT b.banner , b.banner_url "
//            . "FROM CLD_banners b "
//            . "RIGHT JOIN CLD_timesBanners bt "
//            . "ON bt.id_banner= b.id "
//            . "WHERE b.rental_id=$owner "
//            . "AND (('$date2' BETWEEN start_date AND end_date AND end_date IS NOT NULL) "
//            . "OR ('$date2' BETWEEN start_date AND '3000-01-01' AND end_date IS NULL))"
//    );
    
    if (count($banner) == 0) {
        echo "<a href='http://www.digital-centre.com' target='_blank'>";
        echo "&nbsp;<img style='margin-top:30px;margin-left:20px;' class='banner'  src='images/web/banners/banner-default.gif?version=$rnd'>";
        echo "</a>";
    }
    else{
//        $CLD_CON->FetchArray();
        $url1 = $banner[0]["banner_url"];
        $banner = $banner[0]["banner"];
        if ($url1 != "") {
            echo "<a href='$url1' target='_blank'>";
        }
        echo "&nbsp;<img style='margin-top:30px;margin-left:20px;' class='banner'  src='images/web/banners/$banner'>";
        if ($url1 != "") {
            echo "</a>";
        }
    }
}

echo "</center>";
if($_SESSION['USERTYPE']<5 || $_SESSION['USERTYPE']==6){
    echo "<span style='float: right; top:80% ;right: 28%; position:absolute;'><img src='images/web/banner_cloud.png' style='cursor:pointer;height:33px;' onClick='edit(17 , $ID);'></span>";
}

echo "</div>";
if($isPrivate == 1) {
    echo "<span class='bSp3' style='background-color:transparent;'><img src='images/web/is_private_cloud.png' style='cursor:pointer;height:33px;' onClick='edit(16 , $ID);'> </span>";
} 
else{
    echo "<span class='bSp2' style='background-color:transparent;'><img src='images/web/isnotprivate_cloud.png' style='cursor:pointer;height:33px;' onClick='edit(16 , $ID);'></span>";
   // echo "<span class='bSp' ><img  src='images/web/flecha_arriba.jpg' style='width:15px;'> See All photos </span>";
}

echo "<span class='bSp4' style='background-color:transparent;'><img src='images/web/background_cloud.png' style='cursor:pointer;height:33px;' onClick='edit(15 , $ID);'></span>";
echo "<div class='questions' id='question' onClick='openQuestions();' title='Click to open Questions'>";
echo "<span id='qText'><img src='images/web/questions_cloud.png' style='cursor:pointer;height:33px;'></span></div>";
echo "</div>";

echo "<div class='qst' style='display:none;overflow:auto;'>";
echo '<img id="closeQ" style="cursor: pointer; width:40px;float: right; display: block;" onclick="closeQuestions();" src="images/web/close.png">';
/* Question 1 */

$Q1 = $baseController->CLD_questionsModel->getEventsByQuestionNumber($ID, 1);
//$CLD_CON->OpenRs("SELECT id FROM CLD_questions WHERE question_number=1 AND event=$ID");

if ($Q1) {
    echo "<h1>ASK EMAIL IS <span style='color:green;'>ON</span> <input type='button' class='editButton' onClick='edit(18 , $ID);'></h1>";
    echo "<p>If this option is ON, the user will be asked to enter their email before they can see their photo.</p>";
//    $CLD_CON2->OpenRs("SELECT id FROM CLD_questions_emails WHERE event=$ID GROUP BY email");
    $num_email = $baseController->CLD_questions_emailsModel->getQuestionsEmail($ID);
    $num_emails = count($num_email);
    echo "<p>This option is OF. ($num_emails Recapted Emails )</p>";
} 
else{
//    $CLD_CON2->OpenRs("SELECT id FROM CLD_questions_emails WHERE event=$ID GROUP BY email");
    $num_email = $baseController->CLD_questions_emailsModel->getQuestionsEmail($ID);
    $num_emails = count($num_email);
    echo "<h1>ASK EMAIL IS <span style='color:red;'>OFF</span> <input type='button' class='editButton' onClick='edit(18 , $ID);'></h1>";
    echo "<p>If this option is ON, the user will be asked to enter their email before they can see their photo.</p>";
    echo "<p>This option is OFF. ($num_emails Recapted Emails )</p>";
}

/* Question 2 */
$Q2 = $baseController->CLD_questionsModel->getEventsByQuestionNumber($ID, 2);
//$CLD_CON->OpenRs("SELECT id , question , reply1 , reply2 , r1 , r2 FROM CLD_questions WHERE question_number=2 AND event=$ID");

if($Q2){
    $question = $Q2[0]['question'];
    $reply1 = $Q2[0]['reply1'];
    $reply2 = $Q2[0]['reply2'];
    $r1 = $Q2[0]['r1'];
    $r2 = $Q2[0]['r2'];
    $XX = $r1 + $r2;
    
    if($r1==0){
        $p1 = $r1."%";
    }
    else{
        $p1 = (($r1 * 100) / $XX);
        $p1 = $p1 . "%";
    }
    
    if($r2==0){
        $p2 = $r2."%";
    }
    else{
        $p2 = (($r2 * 100) / $XX);
        $p2 = $p2 . "%";
    }
    
    echo "<h1>QUESTION 1 IS <span style='color:green;'>ON</span> <input type='button' class='editButton' onClick='edit(19 , $ID);'></h1>";
    echo "<p>Question Text: $question </p>";
    echo "<p>Answer 1 ($reply1): <b> $r1 has been clicked($p1)</b></p><p><progress value='$r1' max='$XX' style='margin-left:2%;margin-right:2%;width:95%;height:20px;'></progress></p>";
    echo "<p>Answer 2 ($reply2): <b>$r2 has been clicked($p2)</b></p><p><progress value='$r2' max='$XX' style='margin-left:2%;margin-right:2%;width:95%;height:20px;'></progress></p>";
}
else{
    echo "<h1>QUESTION 1 IS <span style='color:red;'>OFF</span> <input type='button' class='editButton' onClick='edit(19 , $ID);'></h1>";
    echo "<p>If this option is ON, the user will be asked a question before they can see their photo.</p>";
    echo "<p>This option is OFF.</p>";
}

/* Question 3 */
$Q3 = $baseController->CLD_questionsModel->getEventsByQuestionNumber($ID, 3);
//$CLD_CON->OpenRs("SELECT id , question , reply1 , reply2 , r1 , r2 FROM CLD_questions WHERE question_number=3 AND event=$ID");


if ($Q3) {
    $question = $Q3[0]['question'];
    $reply1 = $Q3[0]['reply1'];
    $reply2 = $Q3[0]['reply2'];
    $r1 = $Q3[0]['r1'];
    $r2 = $Q3[0]['r2'];
    $XX = $r1 + $r2;
    if($r1==0){
         $p1 = $r1."%";
    }else{
        $p1 = (($r1 * 100) / $XX);
        $p1 = $p1 . "%";
    }
    if($r2==0){
        $p2 = $r2."%";
    }else{
        $p2 = (($r2 * 100) / $XX);
        $p2 = $p2 . "%";
    }
    echo "<h1>QUESTION 2 IS <span style='color:green;'>ON</span> <input type='button' class='editButton' onClick='edit(20 , $ID);'></h1>";
    echo "<p>Question Text: $question </p>";
    echo "<p>Answer 1 ($reply1): <b> $r1 has been clicked($p1)</b></p><p><progress value='$r1' max='$XX' style='margin-left:2%;margin-right:2%;width:95%;height:20px;'></progress></p>";
    echo "<p>Answer 2 ($reply2): <b>$r2 has been clicked($p2)</b></p><p><progress value='$r2' max='$XX' style='margin-left:2%;margin-right:2%;width:95%;height:20px;'></progress></p>";
} else {
    echo "<h1> QUESTION 2 IS <span style='color:red;'>OFF</span> <input type='button' class='editButton' onClick='edit(20 , $ID);'></h1>";
    echo "<p>If this option is ON, the user will be asked a question before they can see their photo.</p>";
    echo "<p>This option is OFF. </p>";
}
echo "</div>";
echo $sty;
?>