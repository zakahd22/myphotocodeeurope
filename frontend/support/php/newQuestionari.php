<?php
include '../sessio.php';
include G_PATH . "common/conexio.php";

$USERNAME =  $_SESSION['USERNAME'];
$userType = $_SESSION['USERTYPE'];
$userID =  $_SESSION['USERID'];

utils::log("usertype={$userType}, userID= {$userID}, USERNAME = {$USERNAME}","logSupport","support/php/newQuestionari.php");
?>

<html>
<head>
    <link href='https://fonts.googleapis.com/css?family=Quintessential|Kelly+Slab|Oleo+Script' rel='stylesheet' type='text/css'>
    <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
    <script src='../js/javascriptFunction.js'></script>
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

<!--   <img src ="../images/dcRodo.png" id="logo1" class="logo">
<img src ="../images/dcRodo.png" id="logo2" class="logo">
<img src ="../images/dcRodo.png" id="logo3" class="logo">
<img src ="../images/dcRodo.png" id="logo4" class="logo">
<div id="bordersDIV">&nbsp;</div> -->

    <div id='inicio'>
    <p>
        <?php
        if ($userType == 1) {
        ?> 
        <span class='left first'>
        <input type='button' class='back' value='BACK' onclick='to("../main.php");'>
        </span>

        <?php
        }
        ?>            
        </p>

        <?php
        if ($userType == 1 || $userType == 2 || $userType == 3) {
        ?> 
            <p class="text"> 
                <select id="Owner" onchange='getOwnerBooths();'>
                    <option value=0>----------</option>
                    <?php
                    if ($userType == 3) {
                        $CLD_CON->OpenRs("SELECT  id , code , name  FROM rentals WHERE CLD_DistributorId=$userID ORDER BY name");
                    } 
                    else {
                        $CLD_CON->OpenRs("SELECT  id , code , name  FROM rentals ORDER BY name");
                    }
                    while ($CLD_CON->FetchArray()){
                        $code = $CLD_CON->GetArrayField("code");
                        $name = $CLD_CON->GetArrayField("name");
                        $id = $CLD_CON->GetArrayField("id");
                        echo "<option value='$id'>$code - $name</option>";
                    }
                    ?>
                </select>
            </p>    
        <?php
        } 
        else {
            if ($userType == 4) {
                $CLD_CON->OpenRs("SELECT name FROM rentals WHERE id=$userID");
                if ($CLD_CON->FetchArray()) {
                    $nomOwner = $CLD_CON->GetArrayField("name");
                }
            } 
            else {
                $nomOwner = $_SESSION['USERNAME'];
            }
            $text = "<p class='text'><br><br>Dear $nomOwner,<br><br></p><p class='text'>Welcome to our support system where we help you find a solution to the issue you are having.</p>
                    <p class='text'>We will ask you a series of questions and also ask you to follow some steps in order to find a solution to your issue. Here you will find an array of videos and photos to help you understand the steps to follow and to answer any questions you may have.</p>
<p class='text'>To begin, please select the model of the PhotoBooth and in the drop-down menu select the PhotoBooth that you want to work on.</p>
<p class='text'>Once the PhotoBooth is selected, click on Start Questions to begin.<br></p>
<p class='text'>Thank you for your continued support.<br><br></p>
<p class='text'>Digital Centre<br><br><br><br></p>";
        ?>
        <div class="info">
            <p class='title1'>PhotoBooths</p>
            <p class='title2'>QUESTIONS</p>
            <p class="text"><?php echo $text ?>
                <input type='hidden' id='Owner' value="<?php echo $_SESSION['USERID']; ?>">
            </p>
        </div>
        <?php
        }
        ?>   
        <div class='photoBothBox'>
            <?php
            if ($userType == 1 || $userType == 2 || $userType == 3) {
            ?> 
            <p class="text"> 
                <select id="boothType">
                    <option value='0'>SELECT OWNER PLEASE</option>
                </select>
            </p>
            <div class='start'>
                <input type='button' class='startQuestions' onclick='newProblem();'>
            </div>
            <?php
            } 
            else{
                if ($userType == 4){
            ?>
            <p class='title2'> This/These is/are your PhotoBooth model/s:</p>
            <div class='boothTypes'>                          
                <?php
                // $CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$userID GROUP BY CLD_idType");
                //Comentari : Acaba
                //   while($CLD_CON)
                $CLD_CON2 = clone($CLD_CON);
                $CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$userID");
                $i = "";
                while($CLD_CON->FetchArray()){
                    $id = $CLD_CON->GetArrayField("CLD_idType");
                    if (empty($i)) {
                        $i = "$id";
                    }
                    else{
                        $i .= " , $id";
                    }
                }
                $in = "IN($i)";
                $notIn = "NOT IN($i)";
                $CLD_CON->OpenRs("SELECT id FROM CLD_boothTypes WHERE id $in");
                while ($CLD_CON->FetchArray()) {
                    $type = $CLD_CON->GetArrayField("id");
                    //echo $type;
                    //$CLD_CON2->OpenRs("SELECT boothType FROM SAT_firstquestion WHERE boothType='$type'");
                    //if ($CLD_CON2->FetchArray()) {
                    echo "<div class='Type' value='$type' id='modelss'><img src='../images/pb/$type.png'></div>";
                    //}
                }
                ?>
            </div>
            <div class='start'>
                <select id="boothType" style='font-size:13pt;max-width:50%;'>
                    <option value='0'>SELECT PHOTOBOOTH MODEL</option> 
                </select>
                <input type='button' class='startQuestions' onclick='newProblem();'>
            </div>
            <?php
            }
            if ($userType == 5) {
            ?>   
            <div class='boothTypes'>
                <?php
                $CLD_CON2 = clone($CLD_CON);
                $CLD_CON->OpenRs("SELECT id FROM CLD_boothTypes");
                while ($CLD_CON->FetchArray()) {
                    $type = $CLD_CON->GetArrayField("id");
                    $CLD_CON2->OpenRs("SELECT boothType FROM SAT_firstquestion WHERE boothType='$type'");
                    if ($CLD_CON2->FetchArray()) {
                        echo "<div class='Type' value='$type'><img src='../images/pb/$type.png'></div>";
                    }
                }
                ?>
                <input type='hidden' id="boothType" value='0-1'>
            </div>
            <div class='start'>
                <input type='button' class='startQuestions' onclick='newProblem();'>
            </div>
            <?php
            }
        }
        ?>
        </div>
        <p id="errors"> </p>
    </div>

    <div class='footer'></div>
        <script>
            $(document).ready(function() {
                $(".Type").hover(
                        function() {
                            $(this).addClass("Type2");
                        },
                        function() {
                            $(this).removeClass("Type2");
                        });
                $(".Type").click(function(){
                    $(".Type3").addClass("Type").removeClass("Type3");
                    $(this).addClass("Type3");
                    $(this).removeClass("Type");
                    $(this).removeClass("Type2");
                    <?php
                    if ($userType == 5) {
                    ?>
                    $("#boothType").val($(this).attr("value"));
                    <?php
                    }
                    if ($userType == 4) {
                    ?>
                    getOwnerBoothsByType($(this).attr("value"));
                    <?php
                    }
                    ?>
                });
            });
        </script>      
    </body>
</html>