<html>
    <head>
        <meta name="author" content="Digital Centre">
        <meta name="apple-itunes-app" content="app-id=736602319 , app-argument=https://itunes.apple.com/us/app/myphotocode/id736602319?mt=8&uo=4">
        <meta name="google-play-app" content="app-id=com.myphotocode">
        <meta http-equiv="content-type"  content="text/html;charset=utf-8" />
        <link href='https://fonts.googleapis.com/css?family=Quantico:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <script src='js/javascriptFunction.js'></script>
        <link rel=stylesheet href="css/style.css" type="text/css">
        <!--[if lt IE 9]>
          <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
          <![endif]-->

        <title>
            MyPhotoCode SAT
        </title>

    </head> 
    <body>
        <div class="page" id="pageCode">
            <div class="forgot" style='text-align: center;position:relative;'>
                <form onsubmit="return false;"  id="photoCodeForm">
                    <h1 style='padding-top:11%;'>Forgot Password</h1>
                    <label for="forgot">Please enter your username.</label>
                    <input type='text' name='forgot' id='forgot' style='text-align:center;'>
                    <input type="button" onclick="forgotPass();" id="loginButton" value="NEW PASSWORD"  style='margin-top:11%;'>
                </form>
                <p style='position:absolute;top: 81%;right:25px;'> <a href='index.php'>Back to HomePage</a></p>
            </div>
            <div style='display: block;width: 16%;position: relative;margin: auto;margin-top: -9%;' id='info'>

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
                                    forgotPass();
                                }
                            }
                        });
                    });


//                    function   forgotPass() {
//                        $(".info").hide();
//                        var username = $("#forgot").val();
//                        if (username === "") {
//                            error("Complete the new username field.");
//                        } else {
//                            $.ajax({
//                                url: 'ajax/forgot_password.php?username=' + username, //Server script to process data
//                                type: 'POST',
//                                success: function(data) {
//
//                                    if (data.indexOf("#Error:") >= 0) {
//                                        var x2 = data.split("#Error:");
//                                        forgotError(x2[1]);
//                                    } else {
//                                        forgotError(data);
//                                    }
//                                },
//                                error: function(data) {
//                                    error(data);
//                                }
//                            });
//                        }
//                    }
                    function forgotError(text) {
                        $("#info").html(text);
                        $("#info").fadeIn(500);

                    }
        </script>
    </body>
</html>
