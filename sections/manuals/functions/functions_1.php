<?php

function showManuals($USERID, $pbmodel) {

    $baseController = new baseController();
    $baseController->createModel('App_booths');

    //This extracts the list of Booths related to the specified model
    $boothList = $baseController->App_boothsModel->getBoothsTypeSegregated($USERID, $pbmodel);

    $boothDescription = $boothList['App_booths'];
    $boothTypes = $boothList['CLD_boothTypes'];
    //we check if there is any booth in the list, showing a simple message if the list is empty
    $bttotal = count($boothTypes);
    if ($bttotal < 1) {
        $html = "";
        $html .= "<h2>You don't have any $pbmodel PhotoBooth</h2>";
    } else {
        //print the title section
        $html = "";
        $html .= "<div class='gran'>";
        $html .= "<h2>Photobooth Manuals - $pbmodel</h2>";
        //prints a list of every applicable booth the user have, besides a link to the user manual  
        $i = 0;
        $alreadyPrinted = [''];
        foreach ($boothTypes as $boothType) {
            $version = str_replace(' ', '_', $boothDescription[$i]['version']);
            //check if we already printed a machine like this
            $flag = false;
            foreach ($alreadyPrinted as $name) {
                if ($boothType['name'] == $name) {$flag = true;break;};
            }

            if ($flag === false) {
                array_push($alreadyPrinted, $boothType['name']);

                //print the name of the booth and the links
                $html .= "<div class='element'>
                <div class='nom'>" . $boothType['name'] . " User Manual</div>
                <div class='container'>";
                $html .= printMe("pdf", $pbmodel, $boothType['name']);
                $html .= printMe("video", $pbmodel, $boothType['name']);
                $html .= "</div>
            </div>";
                $i++;
            }
        };
        unset($alreadyPrinted);
        //add standard manuals
        $generics = ['Customization', 'MyPhotoCode','Wifi','SmartPrint'];
        $html .= showGenerics($generics);
            $html .= "</div>";
    }
    return $html;
}

function printMe($que, $pbmodel , $nomarxiu) {
    switch ($que){
    case "video":
        // search if a video tutorial about this exists, and prints it
        $archiu = strtolower(str_replace(' ', '_', $nomarxiu));
        $archiu .= ".mp4";

        $ruta = dirname(__FILE__) . "/../../../manuals/videos/" . $archiu;
        if (file_exists($ruta)) {
            $codi = "<div class='link'><img src='images/icons/submenu/watch-video.png' height='32' onclick='showVideo(" . '"' . $archiu . '"' . ")'></div>";
            return $codi;
        }else{
            $codi = "<div class='linkoff'><img src='images/icons/submenu/watch-video.png' height='32'></div>";
            return $codi;           
        }
        break;
    case "pdf":
        // search if a pdf about this exists, and prints it
        $pbmodel = strtolower($pbmodel);
        $archiu = strtolower(str_replace(' ', '_', $nomarxiu));
        $archiu .= ".pdf";

        $ruta = dirname(__FILE__) . "/../../../manuals/$pbmodel/" . $archiu;
        if (file_exists($ruta)) {
            $codi = "<div class='link'><a href='manuals/$pbmodel/$archiu' download><img src='images/icons/submenu/download-button.png' height='32'></a></div>";
            return $codi;
        }else{
            $codi = "<div class='linkoff'><img src='images/icons/submenu/download-button.png' height='32'></div>";
            return $codi;
        }
        break;
    }  
}
function showGenerics($generics) {
    $coda = "";
    foreach ($generics as $gen) {
        $coda .= "<div class='element'>
                <div class='nom'>$gen Manual</div>
                <div class='container'>";
        $coda .= printMe("pdf", "generic", $gen);
        $coda .= printMe("video", "", $gen);
        $coda .= "</div>
            </div>";
    }
    return $coda;
}
