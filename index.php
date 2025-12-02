<?php
require_once "common/global.php";
require_once 'common/conexio.php';
$document_root = "/homepages/46/d399659235/htdocs";

new index_view();

class index_view{
    public $head = null;
    public $body = null;
    public $do = null;
    public $input_value = null;
    public $error = null;
    
    public function __construct($lang='en-US') {
        
        $this->define_elements();
        $this->prepare_view();
        $this->show_view();
    }
    
    private function define_elements(){
        $_SESSION['photoCode'] = ""; //inicialitzem perque no pensin que tothom pot veure les seves fotos
        if (isset($_GET['error'])) {
            $this->error = $_REQUEST['error'];
        }

        if (isset($_REQUEST['code'])) {
            $this->input_value = $_REQUEST['code'];
            $_SESSION['photoCode'] = $_REQUEST['code'];
        } 
        else {
//            if(isset($_SESSION['photoCode'])){
//                $this->input_value = $_SESSION['photoCode'];
//            }
        }        
        if (isset($_REQUEST['token'])) {
            $this->getTokenFromPost($_REQUEST['token']);
            
        }else{
            if(isset($_SESSION['photoCode'])){
                $this->input_value = $_SESSION['photoCode'];
            }
        }           
        if(!isset($_REQUEST['code']) && !isset($_REQUEST['token']) ) {
            $this->do = "setTimeout(function() {
                    $('#logoMyPhotoCode').fadeIn(750);
                }, 250);";
        } 
        else{
            if (isset($_REQUEST['v'])) {
                $this->do = "lookPhoto({$_REQUEST['v']});";
            } 
            else{
                $this->do = "lookPhoto(1);";
            }
        }

        if (isset($_REQUEST['login'])) {               
            $this->do .= "toLoginCode();";
        }
        if (isset($_REQUEST['forget'])) {   
            $this->do .= "toLoginCode();";
            $this->error = "The password has been successfully changed";
            $error = "true";
        }
    }

    private function prepare_view(){
        $this->head = <<<HTML
            <head>
                <script>
                    (function(i, s, o, g, r, a, m) {
                        i['GoogleAnalyticsObject'] = r;
                        i[r] = i[r] || function() {
                            (i[r].q = i[r].q || []).push(arguments)
                        }, i[r].l = 1 * new Date();
                        a = s.createElement(o),
                                m = s.getElementsByTagName(o)[0];
                        a.async = 1;
                        a.src = g;
                        m.parentNode.insertBefore(a, m)
                    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                    ga('create', 'UA-54469059-1', 'auto');
                    ga('send', 'pageview');
                </script>

                <!-- Leadfeeder Website Tracker -->
                 <script> (function(ss,ex){ window.ldfdr=window.ldfdr||function(){(ldfdr._q=ldfdr._q||[]).push([].slice.call(arguments));}; (function(d,s){ fs=d.getElementsByTagName(s)[0]; function ce(src){ var cs=d.createElement(s); cs.src=src; cs.async=1; fs.parentNode.insertBefore(cs,fs); }; ce('https://sc.lfeeder.com/lftracker_v1_'+ss+(ex?'_'+ex:'')+'.js'); })(document,'script'); })('DzLR5a5rldx8BoQ2'); </script>
                
                <meta name="author" tyle='margin-top: 10px;' content="IT Department -  Digital Centre">
                <meta name="description" content="MyPhotoCode">
                <meta name="keywords" content="MyPhotoCode,PhotoBooths,Photo,Booths,Strip">
                <meta name="apple-itunes-app" content="app-id=736602319 , app-argument=https://itunes.apple.com/us/app/myphotocode/id736602319?mt=8&uo=4">
                <meta name="google-play-app" content="app-id=com.myphotocode">
                <meta http-equiv="content-type"  content="text/html;charset=utf-8" />
                <meta property="og:title" content="DC PhotoBooth" />
                <meta property="og:image" content="https://www.myphotocode.com/images/web/DC-share_small.jpg" />
                <meta property="og:description" content="Check this out! I took a photo at a DC Photobooth." />

                <link rel="author" href="https://es.linkedin.com/in/joancorominaslozano">
                <link rel="icon" href="images/web/favicon.ico">
                <link href='https://fonts.googleapis.com/css?family=Paytone+One' rel='stylesheet' type='text/css'>
                <!--
                <link href='https://fonts.googleapis.com/css?family=Revalia' rel='stylesheet' type='text/css'>
                <link href='https://fonts.googleapis.com/css?family=Risque' rel='stylesheet' type='text/css'>
                -->
                <link  type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
                <link rel="stylesheet" type="text/css" href="assets/css/base/base.css"/>
                <link rel='stylesheet' href="includes/logincss.css" type="text/css">
                <link rel='stylesheet' href="includes/jquery.smartbanner.css" type="text/css">
                

                <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
                <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
                <script src="includes/jquery.corner.js"></script>
                <script src="includes/jquery.smartbanner.js"></script>
                <!--[if lt IE 9]>
                <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
                <![endif]-->
                <script src="includes/loginJavaScript.js"></script>
                <title>
                    MyPhotoCode
                </title>
                
                <script src="assets/js/base.js"></script>
                <link href="assets/css/popupV2.css" rel="stylesheet" type="text/css">
                <script type="text/javascript" src="assets/js/popupV2.js"></script>
                
                <script src="assets/libraries/sweetalert2/sweetalert2.min.js"></script>
                <link rel="stylesheet" href="assets/libraries/sweetalert2/sweetalert2.min.css">
            </head> 
HTML;

        $this->body = <<<HTML
            <body>
                <div id="cover_popupV2-Off">
                    <div id="popup_divV2">
                        <div id="buttons_top"></div>
                        <div id="popup_general_divV2">
                            <hr class="popup-spacer">
                            <div id="title_popupV2"></div>
                            <div id="fill_popupV2">
                                <div id="content_popupV2">
                                </div>
                            </div>
                            <div class="swal2-validationerror"></div>
                            <hr class="popup-spacer">
                            <div class="popup-buttons" style="justify-content: center;"></div>
                        </div>
                    </div>
                </div>
                <div id="popup" class="popup"></div>
                <div  class="content-popup"></div>
                <div  class="content-popup2"></div>
                
HTML;

        if(utils::is_test()) $this->body .= "<div class='test_identifier'>SANDBOX</div>";
        
        $this->body .= <<<HTML
                <img src="images/web/myphotocode.png" id ="logoMyPhotoCode" style="left: 25%;">
                <div class="page2" id="pageLogin" >
                    <span class="option"  onclick="toCodeLogin();" >MyPhotoCode</span> 
                    <div class="login">
                        <form onsubmit="return false;"  id="loginForm">
                            <center><label for="username">USERNAME</label></center><input type="text" name="username" id='user'><br>
                            <center><label for="pswd">PASSWORD</label></center><input type="password" name="pswd" id='pswd'>
                            <input type="button" onclick="login();" id="loginButton2" value="LOGIN">
                            <center><a href="forgot.php">forgot password</a></center>
                        </form>
                    </div> 

HTML;
        if($this->error == "The password has been successfully changed"){          
        
        $this->body .= <<<HTML
                    <div class="errorsPestanya2" id='pestError' style='width: 255px, padding: 9px, height: 54px'>
                        <p id='pError'>{$this->error}</p>
                    </div>
HTML;
        }
        
        $this->body .= <<<HTML
                 <div id="login-error" class='error'>{$this->error}</div>
                </div>
                <div class="page" id="pageCode">
                    <span class="option" onclick="toLoginCode();">LOGIN</span> 
                    <div class="login1">
                        <form onsubmit="return false;"  id="photoCodeForm">
                            <center><label for="username">MyPhotoCode</label></center>
                            <input type='text' name='photocode' value='{$this->input_value}' id='photocode'>
                            <center><img src="images/web/DC-Logo-blue.png" class='logoMini'></center>
                            <input type="button" onclick="lookPhoto();" id="loginButton" value="GET PHOTO">
                        </form>
                    </div>
HTML;
        if($this->error == "Photo not found"){
            $this->body .= <<<HTML
                    <div class="errorsPestanya" id='pestError' style='width: 255px, padding: 9px, height: 54px'>
                        <p id='pError'>Photo not found</p>
                    </div>
HTML;
        }                    
        $this->body .= <<<HTML
                    <div id="errorOverlay" onclick="cancel()"></div>
                    <div class="errorsPestanya" id='pestError' style='display: none'>
                        <p id='pError'></p>
                    </div>

                </div>
                <div class="page2" id="pagePHOTO"></div>

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
                        {$this->do}
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
//                        $.smartbanner();
                    });

                </script>

            </body> 
HTML;
    }

    private function show_view(){
        echo "<html lang='en'>";
        echo $this->head;
        echo $this->body;
        echo "</html>";
    }
    private function getTokenFromPost($token){
        $CLD_CON = getNewBdD();
        $sql = "SELECT code FROM `gestor` WHERE token = '$token'"; 
        $CLD_CON->OpenRs($sql);
        while ($CLD_CON->FetchArray()) {
            $code = $CLD_CON->GetArrayField("code");
            $this->input_value = $code;

        }
        if($this->input_value == NULL){
            $this->error = "Photo not found";
            $error = "true";
        }
    }
}
