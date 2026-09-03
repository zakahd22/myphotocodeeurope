<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$bgId = $_POST['bg'];
$CLD_CON->OpenRs("SELECT * FROM event_backgrounds WHERE id=$bgId");
    if($CLD_CON->FetchArray()){

    $color = $CLD_CON->GetArrayField('color');
    $imgBg = $CLD_CON->GetArrayField('image_url');
    $align_x = $CLD_CON->GetArrayField('align_x');
    $align_y = $CLD_CON->GetArrayField('align_y');
    $repeat = $CLD_CON->GetArrayField('repeat');
    echo "background:";
    if (!empty($color))
        echo "#" . $color;
    if (!empty($imgBg))
        echo " url('assets/images/backgrounds/" . $imgBg . "')";
    echo " " . $align_x;
    echo " " . $align_y;
    echo " " . $repeat;
    echo ";";
    echo "width: 243px;";
    echo "height: 200px;";
    //echo "width: 90%;";
    //echo "height: 80%;";
    //echo "margin: 5%;";

    }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
