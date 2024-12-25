<html>
    <head>
        <meta name="author" content="Digital Centre">
        <meta name="apple-itunes-app" content="app-id=736602319 , app-argument=https://itunes.apple.com/us/app/myphotocode/id736602319?mt=8&uo=4">
        <meta name="google-play-app" content="app-id=com.myphotocode">
        <meta http-equiv="content-type"  content="text/html;charset=utf-8" />
        <link rel="icon" href="images/web/favicon.ico">
        <link href='https://fonts.googleapis.com/css?family=Risque' rel='stylesheet' type='text/css'>
        <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
        <link rel='stylesheet' href="includes/logincss.css" type="text/css">
        <link rel='stylesheet' href="includes/jquery.smartbanner.css" type="text/css">
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <script src="includes/jquery.corner.js"></script>
          <script src="includes/jquery.smartbanner.js"></script>
        <!--[if lt IE 9]>
          <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
          <![endif]-->
        <script>
<?php include 'includes/loginJavaScript.js'; ?>
        </script>    
        <title>
            MyPhotoCode
        </title>
    </head> 
    <body>
        
        <img src='obres.png' style='width:40%; position:absolute;top:10%;left:30%;'/>
        
<!--
        <div id="popup" class="popup"></div>
        <div  class="content-popup"></div>
        <div  class="content-popup2"></div>
        <div class="errorsPestanya" id='pestError'>
            <p id='pError'></p>
            <div id='closeErrors' onclick='errorClose()'>&#9650;</div>
        </div>
        <img src="images/web/myphotocode.png" id ="logoMyPhotoCode">
        <div class="page2" id="pageLogin" >
            <span class="option"  onclick="toCodeLogin();" >MyPhotoCode</span> 
            <div class="login">
                <form onsubmit="return false;"  id="loginForm">
                    <center><label for="username">USERNAME</label></center><input type="text" name="username" id='user'><br>
                    <center><label for="pswd">PASSWORD</label></center><input type="password" name="pswd" id='pswd'>
                    <input type="button" onclick="login();" id="loginButton2" value="LOGIN">
                </form>
            </div>            
                     <div id="login-error" class='error'><?php
                if (isset($_GET['error'])) {
                    echo $_REQUEST['error'];
                }
                ?></div>
        </div>
        <div class="page" id="pageCode">
            <span class="option" onclick="toLoginCode();">LOGIN</span> 
            <div class="login">
                <form onsubmit="return false;"  id="photoCodeForm">
                    <center><label for="username">MyPhotoCode</label></center>
                    <?php
                    if (isset($_REQUEST['code'])) {
                        $code = $_REQUEST['code'];
                        echo "<input type='text' name='photocode' value='$code' id='photocode'>";
                    } else {
                        echo "<input type='text' name='photocode' id='photocode'>";
                    }
                    ?>
                    <center><img src="images/web/logo.png" class='logoMini'></center>
                    <input type="button" onclick="lookPhoto();" id="loginButton" value="GET PHOTO">


                </form>
            </div>
            
            
        </div>

        <div class="page2" id="pagePHOTO">
        </div>
        <script>

            $(document).ready(function() {
                $(".popup").slideUp(0);
                $(".content-popup").slideUp(0);
                $(".content-popup2").slideUp(0);
                $(".popup-close").hide();



                $(document).keypress(function(e) {
                    if (e.keyCode == 27 && popupStatus == 1) {
                        popupClose();
                    }
                });
                // $("#logoMyPhotoCode").fadeOut();
<?php if (!isset($_REQUEST['code'])) { ?>
                    setTimeout(function() {
                        $("#logoMyPhotoCode").fadeIn(1000);

                    }, 1500);
    <?php
} else {
    ?>
                    lookPhoto(1);
    <?php
}
?>
<?php if (isset($_REQUEST['login'])) { ?>
                  
     toLoginCode();
 <?php
}
?>
                $(document).keypress(function(k) {
                    if (k.keyCode == 13) {

                        if ($("#user").is(":focus") || $("#pswd").is(":focus")) {
                            login();
                        }
                        if ($("#photocode").is(":focus")) {
                            lookPhoto();
                        }
                    }
                });
 $.smartbanner();
            });

        </script>
      

                 
        -->
    </body> 

</html>