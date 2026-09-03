<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="util/mov.css">
        <title>PhotoBooth Pay Pal payment</title>
    </head>
    <body>
        <h1>PhotoBooth Pay Pal payment</h1>
        
        <?php
        //control de browser
        $myUserAgent = strtolower ($_SERVER['HTTP_USER_AGENT']);
        //$ismobile = preg_match('/iphone|ipad|ipod|android|blackberry|mini|mobile|windows\sce|palm/i', $myUserAgent);
        $isApple = preg_match('/iphone|ipad|ipod/i', $myUserAgent); //nota: la 'i' és redundant
        
        
        $myinfo =  "https://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        
        $fromApp = false;
        if($isApple){
            if(isset($_REQUEST['MyPhotoCode'])){
                if($_REQUEST['MyPhotoCode'] == "true") $fromApp = true;
            }
        }
        
        //proves
        if($fromApp){
            $myinfo.= "<br/>from App";
        }
        else{
            $myinfo.= "<br/>not from App";
            if($isApple) $myinfo.= "<br/> from iphone or ipad.";
            
        }
        echo "<!-- $myinfo -->";
        include("PBPalBody.php");
        if($isApple && !$fromApp){
//20131115            echo "Please, install the App <a href='https://www.dc-image.com'>MyPhotoCode in Apple Store.</a>";
            
            echo "Would you like to download the MyPhotoCodeApp <br/><a href='https://itunes.apple.com/app/id736602319'><img src='APP-STORE-IPAD.png' border='none'></a>";
        }
        
        
        ?>
    </body>
</html>
