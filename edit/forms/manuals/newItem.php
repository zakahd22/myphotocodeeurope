<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . 'common/global.php';

$title = $_POST['name'];
$version = implode(", ", $_POST['version']);
$oldtype = $_POST['oldtype'];
$olddata = $_POST['olddata'];
$oldcadena = $_POST['oldcadena'];
$oldcadena = explode(',', $oldcadena[0]);
$newtype = $_POST['newtype'];
$newfile = $_FILES['newfile'];
$newYouTube = $_POST['newfile'];
$booths = $_POST['booths'];
$olddesc = $_POST['olddesc'];
$olddesc = explode(',', $olddesc[0]);
$newdesc = $_POST['ndesc'];



utils::log('===================================================================', 'logASD');

$namesForTheQuery = [];

// if there are new files:
if ($newfile) {
    $videoformats = ['webm', 'mkv', 'ogg', 'ogv', 'gifv', 'avi', 'mov', 'qt', 'wmv', 'mp4', 'mpg', 'mpeg', 'm4v', '3gp', '3g2'];
    $errors = "";
    $fileerrors = 0; // error counter
    $youTubeCounter = 0; // separate counters needed to keep indexes comparable later
    $fileCounter = 0; // separate counters needed to keep indexes comparable later

    foreach ($newtype as $type) {

        $a = substr("0" . strval($i), -2);
        $fileid = "newfile";


        switch ($type) {
            case ("pdf"):

                $name = $_FILES['newfile']['name'][$fileCounter];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $tmp = $_FILES['newfile']['tmp_name'][$fileCounter];
                $name = str_replace(' ', '_', $name);
                if ($ext != "pdf") {
                    $errors .= "File type should be .pdf";
                    $fileerrors++;
                } else {
                    $path = G_PATH . 'manuals/' . $name;
                    if (move_uploaded_file($tmp, $path)) {
                        $errors .= "{$name} uploaded";
                    } else {
                        $errors .= "Error while uploading {$name}: {$_FILES['newfile']['error'][$fileCounter]}";
                        $fileerrors++;
                    }
                }
                $fileCounter++;
                break;
            case ("video"):

                $name = $_FILES['newfile']['name'][$fileCounter];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $tmp = $_FILES['newfile']['tmp_name'][$fileCounter];
                $name = str_replace(' ', '-', $name);
                if (in_array($ext, $videoformats)) {
                    $path = G_PATH . 'manuals/videos/' . $name;
                    if (move_uploaded_file($tmp, $path)) {
                        $errors .= "{$name} uploaded";
                    } else {
                        $errors .= "Error while uploading {$name}: {$_FILES['newfile']['error'][$fileCounter]}";
                        $fileerrors++;
                    }
                } else {
                    $errors .= "File should be a video";
                    $fileerrors++;
                }
                $fileCounter++;
                break;
            case ("youtube"):
                $name = $newYouTube[$youTubeCounter];
                $youTubeCounter++;
                break;
        }
        array_push($namesForTheQuery, $name);
    }


    if ($fileerrors > 0) {
        $errors .= "Errors while uploading the files, aborting the process";
        utils::log($errors, 'logASD');
        utils::log($fileerrors, 'logASD');
        exit;
    }
}


// start inserting the data, first the manuals table:
$queryA = "INSERT INTO manuals (`name`, `version`) 
        VALUES ('{$title}', '{$version}');";
$lastInsertID = $CLD_CON->ExecuteInsert($queryA);



//query to insert the items of the new manual, manualsItems table
$queryB = "INSERT INTO manualsItems (`manual_id`, `type`, `data`, `desc`)
        VALUES ";
//first add the old/reused items to the list
$i = 0;
$add = [];
foreach ($oldtype as $type) {
    if ($type != "") {
        array_push($add, "({$lastInsertID}, '{$type}', '{$oldcadena[$i]}', '{$olddesc[$i]}')");
    }
    $i++;
}

//then add the new ones
$i = 0;
foreach ($newtype as $type) {
    if ($type != "") {
        array_push($add, '(' . $lastInsertID . ', "' . $type . '", "' . $namesForTheQuery[$i] . '", "' . $newdesc[$i] . '")');
    }
    $i++;
}
$queryB .= implode(", ", $add) . ";
        ";                              // concatenate all the values to add to the query
$CLD_CON->OpenRS($queryB);

utils::log($queryB, 'logASD');
//last, insert the values in the manualsBooths table
$queryC = "INSERT INTO manualsBooths(`booth_id`, `manual_id`)
        VALUES ";
// check if booth 0 is selected, "All", so you don't have to insert every single booth
if ($booths[0] == 0) {
    $queryC .= "(0, {$lastInsertID})";
} else {
    $add = [];
    foreach ($booths as $booth) {
        array_push($add, "({$booth}, {$lastInsertID})"); //insert every value pair into an array
    }
    $queryC .= implode(", ", $add).";"; //concatenate all the values with ", " and add them to the query
}
$CLD_CON->OpenRS($queryC);

utils::log("$queryA $queryB $queryC", "logASD");