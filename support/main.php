<?php 
include_once 'sessio.php';
require_once G_PATH . 'common/conexio.php';
//var_dump($_SESSION['USERTYPE']);
?>
<!DOCTYPE html>
<html>
<head>
   <link href='https://fonts.googleapis.com/css?family=Quantico:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
    <script src='js/javascriptFunction.js'></script>
    <link rel=stylesheet href="css/style.css" type="text/css">
    <link rel="shortcut icon" href="favico.ico"/>
    <!--[if lt IE 9]>
    <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
</head>
<body>
    <div class='header'>
        <div class='lgn'><span style='margin-left:25px;'>Welcome <?php echo $USERNAME; ?></span> <span class='logOut' onclick="javascript:location.href= 'ajax/logout.php'"/> </div>
        <img src='images/logo.png' class='logo'>
            <!--<img src="images/logo.png" class="logo">-->
        </div>

    <div id='inicio'>
        <?php
        if($userType==1){
        ?>
            <a onclick="setPage('./php/oldQuestionaris.php')"><div class="largeBox" style="background-image: url('images/lookOldQuestionaris_admin.jpg');" ><span class='title1'> LOOK OLD QUESTIONARIS </span></div></a>
            <a onclick="setPage('./php/newQuestionari.php')"><div class="largeBox" style="background-image: url('images/cuestionari_admin.jpg');"><span class='title1'> NEW QUESTIONARI </span> </div></a>
            <a onclick="setPage('./menuAdmin.php')"><div class="largeBox" style="background-image: url('images/admin.png');" ><span class='title1'> ADMINISTRATOR </span></div></a>
        <?php
        }
        else{
        ?>
            <meta http-equiv="refresh" content="0; url=./php/newQuestionari.php?4">
          <!--  <a href="./php/oldQuestionaris.php"><div class="box first" style="background-image: url('images/lookOldQuestionaris.jpg');background-repeat: no-repeat;"> LOOK OLD QUESTIONARIS </div></a>
            <a href="./php/newQuestionari.php"><div class="box" style="background-image: url('images/cuestionari.jpg');background-repeat: no-repeat; "> NEW QUESTIONARI </div></a>-->
        <?php
        }
        ?>
    </div>
    <div class='footer'></div>  
</body>
<script>
    function setPage(url){
        window.top.location.href = url;
    }
</script>
</html>
