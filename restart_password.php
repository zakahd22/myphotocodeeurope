<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

    <head>
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <meta name="author" content="Digital Centre">
        <meta name="apple-itunes-app" content="app-id=736602319 , app-argument=https://itunes.apple.com/us/app/myphotocode/id736602319?mt=8&uo=4">
        <meta name="google-play-app" content="app-id=com.myphotocode">
        <link rel="icon" href="https://www.myphotocode.com/images/web/favicon.ico">
        <link href='https://fonts.googleapis.com/css?family=Risque' rel='stylesheet' type='text/css'>
        <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
        <link rel='stylesheet' href="includes/logincss.css" type="text/css">
        <link rel='stylesheet' href="includes/jquery.smartbanner.css" type="text/css">
        <!--<link href='https://fonts.googleapis.com/css?family=Rokkitt' rel='stylesheet' type='text/css'>-->
        <link href='https://fonts.googleapis.com/css?family=Revalia' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Risque' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Paytone+One' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<!--        <script src="includes/jquery.corner.js"></script>
        <script src="includes/jquery.smartbanner.js"></script>-->
<!--        [if lt IE 9]>
          <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
          <![endif]-->
        <script>
<?php 
    include 'includes/loginJavaScript.js'; 
    require_once "./common/global.php";
    require_once G_PATH . 'common/conexio.php';
    require'./includes/classes/APP_common.php';
?>
        </script>    
        <title>
            MyPhotoCode
        </title>

    </head> 
    <body style="height: 100%;">
        <a class="option" href="https://www.myphotocode.com">LOGIN</a>
        <img src="images/web/myphotocode.png" id="logoMyPhotoCode" style="left: 25%; display: inline;">

    <!--<img src="images/web/myphotocode.png" id ="logoMyPhotoCode">-->
    <div class="page" id="pageCode">

        <div class="login">
            <?php
            if (isset($_REQUEST['c'])) {
                $code = $_REQUEST['c'];
                $CLD_CON->OpenRs("SELECT user , userType , datef FROM CLD_forgot_pws WHERE code='$code'");
                if ($CLD_CON->FetchArray()) {
                    $user = $CLD_CON->GetArrayField("user");
                    $userType = $CLD_CON->GetArrayField("userType");
                    $datef = strtotime($CLD_CON->GetArrayField("datef"));
                    $date = strtotime(date("Y-m-d H:i:s"));
                    if ($datef < $date) {
                        echo "This session has expired";
                    } else {
                        ?>

                        <form onsubmit="return false;"  id="photoCodeForm">
                            <center><label for="pws">New Password</label></center>
                            <input type='password'  id='pass'>
                            <center><label for="pws">Confirm Password</label></center>
                            <input type='password'  id='passR'>
                            <input type="hidden" id='code' value="<?php echo $code ?>">
                            <input style="margin-top: 30px;" type="button" onclick="restartPass();" id="loginButton" value="NEW PASSWORD">
                        </form>
                        <?php
                    }
                } else {
                    ?>

                    <meta http-equiv="refresh" content="0;URL='https://www.myphotocode.com'">
                    <?php
                }
            }
            ?>
        </div>
        <div id="i" class="info"></div>
        <!--<div id="funciona" class="hidden">-->

        <!--</div>-->
    </div>
    <?php
    function restartPass(){ 
        if(pass === ""){
        }
        elseif(passR === ""){
        }
        if(passR === pass){
            $CLD_CON->OpenRs("SELECT user , userType , datef FROM CLD_forgot_pws WHERE code='$code'");
            if ($CLD_CON->FetchArray()) {
                $user = $CLD_CON->GetArrayField("user");
                $userType = $CLD_CON->GetArrayField("userType");
                $CLD_CON2->Execute("UPDATE CLD_Login SET password='$pass' WHERE id_user=$user AND userType=$userType");
                if ($userType == 4) {
                    $CLD_CON2->Execute("UPDATE rentals SET password='$pass' WHERE id=$user");
                }
                echo "The password has been successfully changed, please go to the <a href='$URL_BASE'>home page</a> and login.";
            } else {
                echo "There is no request to change the password of this user.";
            }
        }
    }
    ?>

    <script>
            
function restartPass() {
    var pass = $("#pass").val();
    var passR = $("#passR").val();
    if (pass === "") {
        forgotError("Complete the new password field.");
        return;
    }
    if (passR === "") {
        forgotError("Complete the repeat password field.");
        return;
    }
    if (passR === pass) {
        var code = $("#code").val();
        var array = [code, pass];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'restartpass.php', //Server script to process data
            type: 'POST',
            dataType: 'html',
            data: {
                data : data
            },
            success: function (data) {
                forgotError("The password has been successfully changed.");
                $("#i").removeClass("info").addClass("info_restart");
                forgotError("The password has been successfully changed.");
                window.location.href = "index.php?forget=1";
            },
            error: function (data) {
                error(data);
            }
        });
    } else {
        forgotError("The passwords do not match.");
        return;
    }
//
}
    </script>




</body> 

</html>
