<html>
    <head>
        <meta name="author" content="Digital Centre">
        <meta name="apple-itunes-app" content="app-id=736602319 , app-argument=https://itunes.apple.com/us/app/myphotocode/id736602319?mt=8&uo=4">
        <meta name="google-play-app" content="app-id=com.myphotocode">
        <meta http-equiv="content-type"  content="text/html;charset=utf-8" />
        <link rel="icon" href="https://www.myphotocode.com/images/web/favicon.ico">
        <link href='https://fonts.googleapis.com/css?family=Risque' rel='stylesheet' type='text/css'>
        <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
        <link rel='stylesheet' href="includes/logincss.css" type="text/css">
        <link rel='stylesheet' href="includes/jquery.smartbanner.css" type="text/css">
        <link href='https://fonts.googleapis.com/css?family=Rokkitt' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Paytone+One' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <script src="includes/jquery.corner.js"></script>
        <script src="includes/jquery.smartbanner.js"></script>
        <link rel="stylesheet" type="text/css" href="assets/css/base/base.css"/>
        <!--[if lt IE 9]>
          <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
          <![endif]-->
        <script>
<?php include 'includes/loginJavaScript.js';?>
        </script>    
        <title>
            MyPhotoCode
        </title>

    </head> 
    <body>
        <a class="option" href="https://www.myphotocode.com">LOGIN</a>
        <img src="images/web/myphotocode.png" id="logoMyPhotoCode" style="left: 25%; display: inline;">
        <div class="page" id="pageCode">
            <div class="login">
                <form onsubmit="return false;"  id="photoCodeForm">
                    <center><div><label for="forgot">Please enter your username.</label></div></center>
                    <center><div style="margin-top: 14px;"><input type='text' name='forgot' id='forgot'></div></center>
                    <center><img src="images/web/logo.png" class='logoMini'></center>
                    <center><div><input style="margin-top: 0px;" type="button" onclick="forgotPass();" id="loginButton" value="NEW PASSWORD"></div></center>
                </form>
            </div>
            <div class="info">

            </div>
        </div>


        <script>

            $(document).ready(function() {
                setTimeout(function() {
                    $("#logoMyPhotoCode").fadeIn(1000);

                }, 1500);
                $(document).keypress(function(k) {
                    if (k.keyCode == 13) {
                        if ($("#forgot").is(":focus")) {
                            lookPhoto();
                        }
                    }
                });
                $.smartbanner();
            });

        </script>




    </body> 

</html>
