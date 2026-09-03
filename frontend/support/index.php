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
    <img src="images/logo.png" id ="logoMyPhotoCode">
    <div class="page2" id="pageLogin" >
        <div class="login">
            <form onsubmit="return false;"  id="loginForm">
                <input type="text" name="username" id='user' class='loginInput' style='top: 98px;border: 1px Solid gray;'><br>
                <input type="password" name="pswd" id='pswd' class='loginInput' style='top: 199px;'><br>
                <input type="button" onclick="login();" id="loginButton2">
                <p style='position: absolute;top: 81%;left:50px;'> <a href='forgot.php'>Forgot Password</a></p>
                <p style='position: absolute;top: 81%;right:50px;'> <a href='register.php'>Register</a></p>
            </form>
        </div>
        <p style='width: 100%;text-align: center;' id ='error'> 
            <?php
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 1) {
                    echo "UserName or Password is not correct.";
                }
            }
            ?>
        </p>
    </div>

    <script>

                $(document).ready(function() {
                    $(document).keypress(function(k) {
                        if (k.keyCode == 13) {

                            if ($("#user").is(":focus") || $("#pswd").is(":focus")) {
                                login();
                            }
                        }
                    });

                    $("#loginButton2").hover(function(){
                        $(this).css("background-image","url('images/login.png')");                   
                    },function(){
                         $(this).css("background-image","none");      

                    });


                });
    </script>
</body> 
</html>