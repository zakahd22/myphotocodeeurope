<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

$ID = $_POST['id'];
$oldComponent = $_POST['oldC'];
$newComponent = $_POST['newC'];
$typeC = $_POST['type'];
$CLD_CON2 = clone($CLD_CON);
$date = date("Y-m-d H:i:s");
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
}

$CLD_CON->OpenRs("SELECT type , booth FROM CLD_components WHERE serialnumber='$newComponent'");
if($CLD_CON->FetchArray()){
    $type=$CLD_CON->GetArrayField("type");
    $actualBooth=$CLD_CON->GetArrayField("booth");
    $CLD_CON2->OpenRs("SELECT descripcio FROM CLD_typeComponents id=$type");
    if($CLD_CON2->FetchArray()){
        $typeName = $CLD_CON2->GetArrayField("descripcio");
    }
    if($type!=$typeC){
    echo "Error , the introduced SN is not of this type of component.";    
    }else{
        if(!empty($actualBooth)){
            if($actualBooth == $ID){
                  echo "Error , Este componente ya esta en este PhotoBooth.";  
            }else{
                  echo "Error , the introduced SN is on other PhotoBooth.";  
            }
        }else{
            $y=false;
            if(!empty($oldComponent)){
               if($CLD_CON->Execute("UPDATE CLD_components SET booth = NULL WHERE serialnumber='$oldComponent'")){
                   $y= true;
                   $coment = addslashes("The $typeName $oldComponent has been removed from photobooth.");
                   $CLD_CON2->Execute("INSERT INTO CLD_historyBooth (comment, data , idBooth , sn) VALUES('$coment' , '$date' , $ID , '$sn')");
                   $coment = addslashes("Removed from photobooth $sn");
                    $CLD_CON2->Execute("INSERT INTO CLD_historyComponents (comment, data , component_sn) VALUES('$coment' , '$date' , '$oldComponent')");
               }
            }else{
                $y=true;
            }
            $x= false;
            if($CLD_CON->Execute("UPDATE CLD_components SET booth = $ID WHERE serialnumber='$newComponent'")){
                $coment = addslashes("The $typeName $newComponent has been added in the photobooth");
                $CLD_CON2->Execute("INSERT INTO CLD_historyBooth (comment, data , idBooth , sn) VALUES('$coment' , '$date' , $ID , '$sn')");
                $coment = addslashes("Added in the photobooth $sn");
                $CLD_CON2->Execute("INSERT INTO CLD_historyComponents (comment, data , component_sn) VALUES('$coment' , '$date' , '$newComponent')");
                $x= true;
            }
            
            if($x && $y){
                echo "OK";
            }else{
                echo "ERROR";
            }
            
            
        }  
    }   
    
    
    
}else{
    echo "Error , The introduced SN no exist.";
}


?>
