<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <?php
    require_once "../../common/global.php";
    require_once G_PATH . '../../common/conexio.php';
    require'./includes/classes/APP_common.php';
    include './includes/classes/APP_BdD_MySQL.php';
    //$USERNAME = "dbo399687929";
    //$PASSWORD ="digitalcentre";
    //$HOST = "db399687929.db.1and1.com";
    //$DBNAME="db399687929";
    //$CLD_CON = new BdD();
    //$CLD_CON->OpenBdD($HOST, $USERNAME, $PASSWORD, $DBNAME);
    ?>
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

        <div class="page" id="pageCode">

            <div class="forgot" style='text-align:center;'>
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
                                <p style='padding-top:50px;'>New password:</p>
                                <input type='password'  id='pass' style='width:80%;text-align:center;'>
                                <p style='padding-top:10px;'>Repeat:</p>
                                <input type='password'  id='passR' style='width:80%;text-align:center;'>
                                <input type="hidden" id='code' value="<?php echo $code ?>">
                                <p><input type="button" onclick="restartPass();" id="loginButton" value="NEW PASSWORD" style='padding-top:10px;'></p>
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
            <div style='display: block;width: 16%;position: relative;margin: auto;margin-top: -9%;' id='info'>

            </div>
        </div>       



  
    <script>
        function forgotError(text){
    $("#info").html(text);
    $("#info").fadeIn(500);
    
}
//function restartPass(){
//    var pass = $("#pass").val();
//    var passR = $("#passR").val();
//    if(pass === ""){
//        forgotError("Complete the new password field.");
//        return;
//    }else
//    if(passR === ""){
//        forgotError("Complete the repeat password field.");
//        return;
//    }
//    if(passR === pass){
//        var code = $("#code").val();
//        var url = "ajax/restartpass.php?code="+code+"&pass="+pass;
//         $.ajax({
//            url: url, //Server script to process data
//            type: 'POST',
//            success: function(data) {
//                if (data.indexOf("#Error:") >= 0) {
//                    var x2 = data.split("#Error:");
//                    forgotError(x2[1]);
//                } else {
//                    forgotError(data);
//                }
//            },
//            error: function(data) {
//                error(data);
//            }
//        });
//    }else{
//        forgotError("The passwords do not match.");
//        return;
//    }
//    
//}
        </script>
          </body> 
</html>
