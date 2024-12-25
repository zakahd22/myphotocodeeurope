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
$activeID=$_POST['activeID'];
$todelete=$_POST['todelete'];
$manualID = $_POST['manualID'];


utils::log("================= EDIT ITEM ================", 'logASD');

//This code deletes the item-manual relationship with the selected choices to delete.
foreach ($todelete as $id) {
    utils::log("Deleting row $id", 'logASD');
    $CLD_CON->Execute("DELETE FROM `manualsItems` WHERE `id` = {$id}");
}


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
                $name = str_replace(' ', '-', $name);
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
                $name = str_replace(' ', '_', $name);
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


//START UPDATING
//Updating Title and Version
$CLD_CON->Execute("UPDATE manuals SET `name` = '{$title}', `version`= '{$version}' WHERE manuals.id = {$manualID}");

//query to insert the items of the new manual, manualsItems table
$queryB = "INSERT INTO manualsItems (`manual_id`, `type`, `data`, `desc`)
        VALUES ";
//first add the old/reused items to the list
$i = 0;
$add = [];
foreach ($oldtype as $type) {
    if ($type != "") {
        array_push($add, "({$manualID}, '{$type}', '{$oldcadena[$i]}', '{$olddesc[$i]}')");
    }
    $i++;
}

//then add the new ones
$i = 0;
foreach ($newtype as $type) {
    if ($type != "") {
        array_push($add, '(' . $manualID . ', "' . $type . '", "' . $namesForTheQuery[$i] . '", "' . $newdesc[$i] . '")');
    }
    $i++;
}
$queryB .= implode(", ", $add) . ";
        ";                              // concatenate all the values to add to the query
$CLD_CON->OpenRS($queryB);


//Then update the booth information




$substract = [];
$add = [];
$boothsInDB = [];

if ($booths[0] == 0) {
    //esborra tot
    // escriu booth 0 manual $manualID
} else {

    $CLD_CON->OpenRs("SELECT `booth_id` FROM manualsBooths WHERE `manual_id` = {$manualID}");
    while ($CLD_CON->FetchArray()) {
        $id = $CLD_CON->GetArrayField('booth_id');
        array_push($boothsInDB, $id);
        if (!in_array($id, $booths)) {
            array_push($substract, "(`booth_id` = {$id} AND `manual_id` = {$manualID})");
        }
    }

    foreach ($booths as $booth) {
        if (!in_array($booth, $boothsInDB)) {
            array_push($add, "({$booth}, {$manualID})");
        }
    }

    if ($substract) {
        $deleteFromBooths = "DELETE FROM `manualsBooths` WHERE " . implode(" OR ", $substract);
        $CLD_CON->Execute($deleteFromBooths);
    }
    if ($add) {
        $addBooths = "INSERT INTO manualsBooths (`booth_id`, `manual_id`) VALUES " . implode(", ", $add);
        $CLD_CON->Execute($addBooths);
    }
}
