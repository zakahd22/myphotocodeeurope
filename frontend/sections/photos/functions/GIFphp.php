<?php

$code2 = $code . "-S";
$gif= "";
$gif2 = ""; 
$st2="";
$urlTwitterGIF = "https://twitter.com/intent/tweet?url=".G_PAGE."/events/" . $eventDate . $event . "/".$code."GIF.gif&text=DC-GIF";

if(!file_exists($folder.$code."GIF.gif")) {
    $z=0;
    $foto = imagecreatefromjpeg($img);
    //list($w,$h) = getimagesize($img);
    $h=$height;
    $w=$width;
    if($w<$h) {
        $st2="id='gif3D';";
        $st3= "background-image:url(\"images/web/shareGIF.png\");background-size:100%;background-repeat:no-repeat;position: absolute;top: -3%;right: -31%;width: 32%;height: 106%;text-align: center;"; 
        $h_m = ($h/4) - 57;
        $h2 = Array(57,577,1095,1613); 
        $w -= 110;
        while($z<4) {
            $destino = imagecreatetruecolor($w, $h_m);
            $destino_name = $folder.$code2.$z.".gif";
            imagecopyresampled($destino, $foto , 0, 0, 55, $h2[$z] , $w, $h_m , $w, $h_m);
            imagegif($destino,$destino_name);
            imagedestroy($destino);
            $z++;
        }
        imagedestroy($foto);
        $logoEND = "../../../images/web/LogoDCEND.gif";
    } 
    else {
        if ($h < 1000) {
            $st2="id='gif2D';";    
            $st3="background-image:url('images/web/shareGIF.png');background-size:100%;background-repeat:no-repeat;position: absolute;top: -3%;left: -31%;width: 32%;height: 106%;text-align: center;"; 
            $w_m = ($w/4) - 65;
            $w2 = Array(75,587,1095,1600);
            $h -= 110;
            while($z<4) {
                $destino = imagecreatetruecolor($w_m, $h);
                $destino_name = $folder.$code2.$z.".gif";
                imagecopyresampled($destino, $foto , 0, 0, $w2[$z] , 55,  $w_m, $h , $w_m, $h);
                imagegif($destino,$destino_name);
                imagedestroy($destino);
                $z++;
            }
            $logoEND = "../../../images/web/logoEND2.gif";
        }
    }

    $sd = scandir ( $folder );
    $tempus = Array(75,50,50,50); 
    $i=0;
    foreach ($sd as $s) {
        if ( $s != "." && $s != ".." ) {
            if(strpos($s,$code2) !== false) {
                $frames2[] = $folder.$s;
                $time2[] = $tempus[$i];
                $i++;
            }
        }
    }
    //$frames2[] = $logoEND;
    //$time2[] = 75;
    $gif = new GIFEncoder(      $frames2,
                                $time2,
                                0,
                                2,
                                0, 0, 0,
                                "url"
            );

    FWrite ( FOpen ( $folder.$code."GIF.gif", "wb" ), $gif->GetAnimation ( ) );
    echo "<link rel='stylesheet' href='includes/logincss.css' type='text/css'>";
    $x=0;
    while($x<$i) {
         unlink($folder.$code2.$x.".gif");
         $x++;
    }

    $gif2 = <<<HTML
        <div {$st2}>
            <img src='events/{$eventDate}{$event}/{$code}GIF.gif' style='width:100%;'>
            <div style="{$st3}">
                <a href='events/{$eventDate}{$event}/{$code}GIF.gif' download><img src='images/web/downloadLook.png' style='width: 70%;display: block;margin: 50% auto -10px auto;'></a>
                <a  href=javascript:startPopup('emailGIF','{$code}');><img src='images/web/emailIMG.png' style='width: 60%;display: block;margin: auto auto -10px auto;'></a>
                <a  href='{$urlTwitterGIF}' target='_blank'><img src='images/web/button-twitter.png' style='width: 64%;display: block;margin: auto auto -10px auto;'/></a></center>
            </div>
        </div>
HTML;
} 
else {
    if($height>$width){

        $st2="id='gif2D';";      
        $st3= "background-image:url('images/web/shareGIF.png');background-size:100%;background-repeat:no-repeat;position: absolute;top: -3%;right: -31%;width: 32%;height: 106%;text-align: center;"; 
    } 
    else {
        $st2="id='gif2D';"; 
        $st3="background-image:url('images/web/shareGIF.png');background-size:100%;background-repeat:no-repeat;position: absolute;top: -3%;left: -31%;width: 32%;height: 106%;text-align: center;"; 
    }
    $gif2 = <<<HTML
        <div {$st2}>
            <img src='events/{$eventDate}{$event}/{$code}GIF.gif' style='width:100%;'>
            <div style="{$st3}">
                <a href='events/{$eventDate}{$event}/{$code}GIF.gif' download><img src='images/web/downloadLook.png' style='width: 70%;display: block;margin: 50% auto -10px auto;'></a>
                <a  href=javascript:startPopup('emailGIF','{$code}');><img src='images/web/emailIMG.png' style='width: 60%;display: block;margin: auto auto -10px auto;'></a>
                <a  href='{$urlTwitterGIF}' target='_blank'><img src='images/web/button-twitter.png' style='width: 64%;display: block;margin: auto auto -10px auto;'/></a></center>
            </div>
        </div>
HTML;
}



?>
