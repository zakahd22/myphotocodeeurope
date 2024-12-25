<?php

require_once './common/global.php';
include './sessio.php';
require_once G_PATH . 'common/conexio.php';

$UserName = null;
$num_incid = null;
$counter_incidencies = null;
$counter_incidencies_manufacturer=null;
$uID = null;

function get_head($USERTYPE){
    $head = <<<HTML
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

            <title>MyPhotoCode</title>
            <meta name="description" content="MyPhotoCode">
            <meta name="keywords" content="MyPhotoCode,PhotoBooths,Photo,Booths,Strip">
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <meta name="author" content="Joan Corominas Lozano - IT Department -  Digital Centre">
            <link rel="author" href="https://es.linkedin.com/in/joancorominaslozano">
            <link rel="icon" href="images/web/favicon.ico">
            
            <link rel="stylesheet" href="assets/libraries/bootstrap/css/bootstrap.css">
            
            <link href='https://fonts.googleapis.com/css?family=PT+Sans:700|Francois+One' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.0.0/animate.min.css">
            <link rel="stylesheet" href="./includes/jquery-anyslider.css">
            
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/jquery-1.10.2.js"></script>
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
            
            <script src="https://api.html5media.info/1.1.8/html5media.min.js"></script>
            <script src="includes/jquery.form.js"></script>
            <script src="includes/jquery.anyslider.js"></script>
            <script> 
                var userType = {$USERTYPE};
            </script>
            <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBO7uxz5GfKtcAcMS87DjWht-YBsgjD1I&sensor=true"></script>
            <link rel="stylesheet" type="text/css" href="css.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="assets/css/base/base.css"/>
            <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
            <script src="assets/js/base.js"></script>
            <script src="js.js"></script> 
            
            <link href="assets/css/popupV2.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="assets/js/popupV2.js"></script>
            
            <script src="assets/libraries/sweetalert2/sweetalert2.min.js"></script>
            <link rel="stylesheet" href="assets/libraries/sweetalert2/sweetalert2.min.css">
                
            <script src="assets/libraries/bootstrap/js/bootstrap.js"></script>
            <link rel="stylesheet" href="assets/libraries/bootstrap/css/bootstrap-theme.css">
        </head>
HTML;
    return $head;
}

function get_notification($type, $message){
    return <<<HTML
        <div class="alert alert-{$type}" style="margin-right: 50px; margin-top: 2px">
            <!--<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>-->
            {$message}
        </div>
HTML;
}

function get_header($UserName){
    $notification = "";

//    $notification = get_notification(
//        "success",
//        "<strong>The maintenance has finished.</strong> Check if your PhotoBooth is connected into Internet and it is uploading photos again.<br />"
//        . "If there is any problem with one of your events of 2017, please send us an email at <a href='mailto::mon@dc-image.com'>mon@dc-image.com</a>."
//    );
    
//    $notification = get_notification(
//        "danger", 
//        "<strong>Our system is under maintenance.</strong> During the maintenance, you may be not able to see your pictures.<br/>"
//        . "We apologize for any inconvenience this may cause you. If you need further assistance, please send an email to <a href='mailto:mon@dc-image.com'>mon@dc-image.com</a>."
//    );
    
    $header = <<<HTML
        <a href="mailto:info@myphotocode.com?Subject=MyPhotoCode%20Contact" target="_top" style='cursor: pointer;cursor:pointer;position:absolute;top: -4px;right: 25px;z-index: 100;'><img src="images/web/contact_us.png" style='width:40px;cursor:pointer;position:absolute;top: -4px;right: 25px;z-index: 100;' title='Contact Us'></a>
        <div class='header'>
            <div class='logo'>
                <div class='titleTopLeft bar'></div>
                <a href="https://digital-centre.com" target="_blank"><img src='images/web/logo_in.png' class='imgLogo'></a>
            </div>
            <div class='topBar'>
                <div class='titleTop bar '>
                    <span id='a1'></span>
                    <span id='a2'></span>
                    <span id='a3'></span>
                    <span id='a4'></span>
                    <img src='images/web/back.png' onClick="back();" class='backButton'> 
                    <img src='images/web/logOut.png' class='logout' onClick='logout();'/>
                    <span class='welcome'>Welcome {$UserName}</span>

                </div>
                <div class="filtersContainer">
                    {$notification}
                    <div id='filters' class='menu2'></div>
                </div>
                <div class='menu3' id='menu3'></div>
            </div>
        </div>
HTML;
    return $header;
}

function get_menu_super($counter_incidencies){  
    $menu_super = <<<HTML
        <a href='main.php' ><img src="images/icons/menu/home.png"  class='dMenuSelected' alt='Home' id='Home'></a>
        <img src="images/icons/menu/owners.png"  class='dMenu' onClick='setSection("owner", 1);' alt='Owners' id='Owner'>
        <img src="images/icons/menu/pb.png"  class='dMenu' onClick='setSection("photobooths", 1);' alt='PhotoBooths' id='PhotoBooths'>
        <img src="images/icons/menu/events.png"  class='dMenu' onClick='setSection("events", 1);' alt='Events' id='Events'>
        <img src="images/icons/menu/photos.png"  class='dMenu' onClick='setSection("photos", 1);' alt='Photos' id='Photos'>
        <img src="images/icons/menu/dongles.png"   class='dMenu' onClick='setSection("dongles", 1);' alt='Dongles' id='Dongles'>
        <img src="images/icons/menu/emails.png"   class='dMenu' onClick='setSection("emails", 1);' alt='Emails' id='Emails'>                                            
        <img src="images/icons/menu/components.png"   class='dMenu' onClick='setSection("components", 1);' alt='Components' id='Components'>
        {$counter_incidencies}
        <img src="images/icons/menu/incidents.png"   class='dMenu' onClick='setSection("incidents", 1);' alt='Incidents' id='Incidents'>
        <img src="images/icons/menu/devalerts.png"   class='dMenu' onClick='setSection("devalerts", 1);' alt='Device Alerts' id='Devalerts'>
        <!-- <a href='support/main.php' target='_blank'><img src="images/icons/menu/sat.png" class='dMenu'></a> -->
        <img src="images/icons/menu/manuals.png"  class='dMenu' onClick='setSection("manuals", 1, {$uID});' alt='Manuals' id='Manuals'>
        <img src="images/icons/menu/templates.png"  class='dMenu' onClick='setSection("templates", 1, {$uID});' alt='Templates' id='Templates'>
        <img src="images/icons/menu/audits.png" class='dMenu' onClick='setSection("audits", 1);' alt='Audits' id='Audits'>
        <img src="images/icons/menu/fCode.png"   class='dMenu' onClick='setSection("financingCode", 1);' alt='FinancingCode' id='FinancingCode'>
        <!--<img src="images/icons/menu/instagram.png"   class='dMenu' onClick='setSection("instagram", 1);' alt='instagram' id='instagram'>-->   
        <img src="images/icons/menu/upgrade.png"   class='dMenu' onClick='setSection("upgrade", 1);' alt='Upgrade' id='Upgrade'>
        
HTML;
    

    return $menu_super;
}

function get_menu_manufacturer($counter_incidencies){ 
    $menu_manufacturer = <<<HTML
        <a href='main.php' ><img src="images/icons/menu/home.png"  class='dMenuSelected' alt='Home' id='Home'></a>
        <img src="images/icons/menu/pb.png"  class='dMenu' onClick='setSection("photobooths", 1);' alt='PhotoBooths' id='PhotoBooths'>
        <img src="images/icons/menu/dongles.png"   class='dMenu' onClick='setSection("dongles", 1);' alt='Dongles' id='Dongles'>                                  
        <img src="images/icons/menu/components.png"   class='dMenu' onClick='setSection("components", 1);' alt='Components' id='Components'>
        {$counter_incidencies}
        <img src="images/icons/menu/incidents.png"   class='dMenu' onClick='setSection("incidents", 1);' alt='Incidents' id='Incidents'>
        <img src="images/icons/menu/ProductionO.png"   class='dMenu' onClick='setSection("productions", 1);' alt='Productions' id='Productions'>
        <img src="images/icons/menu/finishPB.png"   class='dMenu' onClick='setSection("fiproducte", 2);' alt='FinishPB' id='fiproducte'>

        <img src="images/icons/menu/manuals.png"  class='dMenu' onClick='setSection("manuals", 1, {$uID});' alt='Manuals' id='Manuals'>
        <img src="images/icons/menu/templates.png"  class='dMenu' onClick='setSection("templates", 1, {$uID});' alt='Templates' id='Templates'>

        <!-- <img src="images/icons/menu/sat.png"   class='dMenu' onClick='goToSAT();'> -->
HTML;
    return $menu_manufacturer;
}

function get_menu_distributor($counter_incidencies_distributor, $uID){ 
    $menu_distributor = <<<HTML
        <a href='main.php' ><img src="images/icons/menu/home.png"  class='dMenuSelected' alt='Home' id='Home'></a>
        <img src="images/icons/menu/owners.png"  class='dMenu' onClick='setSection("owner", 1);' alt='Owners' id='Owner'>
        <img src="images/icons/menu/pb.png"  class='dMenu' onClick='setSection("photobooths", 1);' alt='PhotoBooths' id='PhotoBooths'>
        <!-- <img src="images/icons/menu/payxprint.png"  class='dMenu' onClick='setSection("payxprint" , 2, {$uID});' alt='PayxPrint' id='PayxPrint'> -->
        <img src="images/icons/menu/payxprint.png"  class='dMenu' onClick='setProfileAndSubmenu("payxprint", "dongles", {$uID});' alt='PayxPrint' id='PayxPrint'>
        {$counter_incidencies_distributor}
        <img src="images/icons/menu/incidents.png"   class='dMenu' onClick='setSection("incidents", 1);' alt='Incidents' id='Incidents'>
        <img src="images/icons/menu/manuals.png"  class='dMenu' onClick='setSection("manuals", 1, {$uID});' alt='Manuals' id='Manuals'>
        <img src="images/icons/menu/templates.png"  class='dMenu' onClick='setSection("templates", 1, {$uID});' alt='Templates' id='Templates'>
        <!-- <img src="images/icons/menu/sat.png"   class='dMenu' onClick='goToSAT();'> -->
HTML;
    //        <img src="images/icons/menu/audits.png" class='dMenu' onClick='setSection("audits", 1, {$uID});' alt='Audits' id='Audits'>

    return $menu_distributor;
}

function get_menu_owner($uID){ 
    $menu_owner = <<<HTML
        <a href='main.php' ><img src="images/icons/menu/home.png"  class='dMenuSelected' alt='Home' id='Home'></a>
        <img src="images/icons/menu/myprofile.png"  class='dMenu' onClick='setSection("owner", 2, {$uID});' alt='MyProfile' id='Owner'>	
        <img src="images/icons/menu/pb.png"  class='dMenu' onClick='setSection("photobooths", 1);' alt='PhotoBooths' id='PhotoBooths'>
        <img src="images/icons/menu/events.png"  class='dMenu' onClick='setSection("events", 1);' alt='Events' id='Events'>
        <img src="images/icons/menu/emails.png"   class='dMenu' onClick='setSection("emails", 1);' alt='Emails' id='Emails'>
        <img src="images/icons/menu/alerts.png" class='dMenu' onClick='setSection("alerts", 2, {$uID});' alt='Alerts' id='Alerts'>

        <img src="images/icons/menu/manuals.png"  class='dMenu' onClick='setSection("manuals", 1, {$uID});' alt='Manuals' id='Manuals'>
        <img src="images/icons/menu/templates.png"  class='dMenu' onClick='setSection("templates", 1, {$uID});' alt='Templates' id='Templates'>

        <!-- <a href='support/main.php' target='_blank'> <img src="images/icons/menu/sat.png" class='dMenu' ></a> -->
HTML;
            
//    if($uID == 7 || $uID == 1){
        $menu_owner .=  <<<HTML
                <img src="images/icons/menu/audits.png" class='dMenu' onClick='setSection("audits", 1, {$uID});' alt='Audits' id='Audits'>
HTML;
//    }
                
    return $menu_owner;
}

function get_menu_customer(){ 
    $menu_customer = <<<HTML
        <a href='main.php' ><img src="images/icons/menu/home.png"  class='dMenuSelected' alt='Home' id='Home'></a>
        <img src="images/icons/menu/events.png"  class='dMenu' onClick='setSection("events", 1);' alt='Events' id='Events'>
HTML;
    return $menu_customer;
}


function get_menu_consultant($counter_incidencies){  
    $menu_super = <<<HTML
        <a href='main.php' ><img src="images/icons/menu/home.png"  class='dMenuSelected' alt='Home' id='Home'></a>
        <img src="images/icons/menu/owners.png"  class='dMenu' onClick='setSection("owner", 1);' alt='Owners' id='Owner'>
        <img src="images/icons/menu/pb.png"  class='dMenu' onClick='setSection("photobooths", 1);' alt='PhotoBooths' id='PhotoBooths'>
        <img src="images/icons/menu/events.png"  class='dMenu' onClick='setSection("events", 1);' alt='Events' id='Events'>
        <!--<img src="images/icons/menu/photos.png"  class='dMenu' onClick='setSection("photos", 1);' alt='Photos' id='Photos'>
        <img src="images/icons/menu/dongles.png"   class='dMenu' onClick='setSection("dongles", 1);' alt='Dongles' id='Dongles'>
        <img src="images/icons/menu/emails.png"   class='dMenu' onClick='setSection("emails", 1);' alt='Emails' id='Emails'>  -->                                          
        <img src="images/icons/menu/components.png"   class='dMenu' onClick='setSection("components", 1, 999);' alt='Components' id='Components'>
        {$counter_incidencies}
        <img src="images/icons/menu/incidents.png"   class='dMenu' onClick='setSection("incidents", 1);' alt='Incidents' id='Incidents'>
        <!-- <a href='support/main.php' target='_blank'><img src="images/icons/menu/sat.png" class='dMenu'></a> -->
        <img src="images/icons/menu/manuals.png"  class='dMenu' onClick='setSection("manuals", 1, {$uID});' alt='Manuals' id='Manuals'>
        <img src="images/icons/menu/templates.png"  class='dMenu' onClick='setSection("templates", 1, {$uID});' alt='Templates' id='Templates'>
        <img src="images/icons/menu/audits.png" class='dMenu' onClick='setSection("audits", 1);' alt='Audits' id='Audits'>
        <!--<img src="images/icons/menu/fCode.png"   class='dMenu' onClick='setSection("financingCode", 1);' alt='FinancingCode' id='FinancingCode'>
        <img src="images/icons/menu/instagram.png"   class='dMenu' onClick='setSection("instagram", 1);' alt='instagram' id='instagram'>-->      
        
HTML;
    

    return $menu_super;
}

function get_popup(){ 
    $popup = <<<HTML
        <div id="popup"></div>
        <div id="buttons_top"></div>
        <div id="content-popup2">            
            <img src='images/web/loading.gif' class='loading'>
        </div>
        <div id='content-popup'>
            <img id='close_popup_id' src='images/web/close.png' style='position:absolute;float:right;cursor:pointer;width: 40px;' onclick='closePopup();'>
            <div class='cPopup'>
                <img src='images/web/loading.gif' class='loading'>
            </div>
        </div>
        <span id="photoPop"  onclick='closePhoto();'></span>
        <img src='images/web/close.png' style='position: absolute;cursor:pointer;width: 40px;right:200px;top:15px;display:none;z-index:16;' id='close2Pop' onclick='closePhoto();'>


        <!-- 20160506 - Aleix - <script src="js.js"></script> -->
HTML;
    return $popup;
}

function show_menu($USERTYPE, $UserName, $uID){    
    $html = "<html lang='en'>";
        $html .= get_head($USERTYPE);        
        $html .= "<body>";
        
        if(utils::is_test()) $html .= "<div class='test_identifier'>SANDBOX</div>";

            $html .= '
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
                            <div class="popup-buttons" style="justify-content: center;">
                            </div>
                        </div>
                    </div>
                </div>';

            $html .= get_header($UserName);
            $html .= "<div class='content'>";
                $html .= "<div class='menu'>";
                switch($USERTYPE){
                    case 1:
                        $html .= get_menu_super($counter_incidencies);
                        break;

                    case 2:
                        $html .= get_menu_manufacturer($counter_incidencies);
                        break;

                    case 3:
                        $html .= get_menu_distributor($counter_incidencies_distributor, $uID);
                        break;

                    case 4:
                        $html .= get_menu_owner($uID);
                        break;

                    case 5:
                        $html .= get_menu_customer();
                        break;
                     case 6:
                        $html .= get_menu_consultant($counter_incidencies);
                        break;
                    
                    default:
                        break;
                }
                $html .= "</div>";
                $html .= "<div class='contingut'>";
                $html .= file_get_contents(G_PAGE . 'NEWS.php');
                $html .= "</div>";
            $html .= "</div>";
            $html .= get_popup();
        $html .= "</body>";
    $html .= "</html>";
    
    return $html;
}

$uID = $_SESSION['USERID'];
if ($USERTYPE == 4) {
    $CLD_CON->OpenRs("SELECT name FROM rentals WHERE id={$uID}");
    if ($CLD_CON->FetchArray()) {
        $UserName = $CLD_CON->GetArrayField("name");
    }
}
else{
    $sql = "SELECT username FROM CLD_Login WHERE id_user=$uID AND userType={$USERTYPE}";
    
    $esOK = $CLD_CON->OpenRs($sql);
    if(!$esOK){
        utils::log("TRACE main - Error {$CLD_CON->errno}: {$CLD_CON->error} - OpenRs: {$sql}", "log");
    }
    if ($CLD_CON->FetchArray()) {
        $UserName = $CLD_CON->GetArrayField("username");
    }
}

$CLD_CON->OpenRs("SELECT * FROM CLD_Incidents WHERE status<2");
if ($CLD_CON->GetRsRows() > 0) {
    $num_incid = $CLD_CON->GetRsRows();
    $counter_incidencies = "<span style='font-size: 8pt;color: white;padding: 3 9;background: red;border: 2px solid white;display: inline;border-radius: 213px;float: left;position: relative;right: -86%;bottom: -34;z-index: 10;margin-top: -34px;'> {$num_incid}</span>";
}

$CLD_CON->OpenRs("SELECT i.* FROM CLD_Incidents i  LEFT JOIN App_booths b ON i.idBooth=b.idBooth  WHERE i.status<2 AND b.CLD_Distributor={$uID}");
if ($CLD_CON->GetRsRows() > 0) {
    $num_incid = $CLD_CON->GetRsRows();
    $counter_incidencies_manufacturer = "<span style='font-size: 8pt;color: white;padding: 3 9;background: red;border: 2px solid white;display: inline;border-radius: 213px;float: left;position: relative;right: -86%;bottom: -34;z-index: 10;margin-top: -34px;'> {$num_incid}</span>";
}

echo show_menu($USERTYPE, $UserName, $uID);