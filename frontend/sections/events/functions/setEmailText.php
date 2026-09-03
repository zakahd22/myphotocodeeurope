<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$id = $_POST['id'];
$text = $_POST['texto'];
$type = $_POST['type'];
$CLD_CON2 = clone($CLD_CON);

$CLD_CON->OpenRs("SELECT id FROM CLD_emailsText WHERE event =$id AND type=$type");
if($CLD_CON->FetchArray()){
    $idText = $CLD_CON->GetArrayField("id");
    if($CLD_CON2->Execute("UPDATE CLD_emailsText as x SET x.text='$text' WHERE x.id=$idText")){
        echo "OK";
    }else{
        echo "Error , please try again";
    }
}else{
    if($CLD_CON2->Execute("INSERT INTO CLD_emailsText(event,type,text) VALUES($id , $type , '$text')")){
        echo "OK";
    }else{
        echo "Error , please try again";
    }
}



/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
