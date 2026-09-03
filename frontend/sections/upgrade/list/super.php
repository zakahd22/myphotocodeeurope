<?php
if(isset($_POST['UPGRADEid'])){
    $UPGRADEid = $_POST['UPGRADEid'];
    $idBootDC = $_POST['idBootDC'];
    $allowedIds = $_POST['allowedIds'];
    $response = $_POST['responseVar'];
    
    $x = false;
    $filters = "";
    
    if(!empty($UPGRADEid)){
        $x=true;
        $filters .= " UPGRADEid LIKE '%$UPGRADEid%' ";
    }
    
    if($idBootDC!=0){
        if($x){
            $filters .= " AND ";
        }
        $filters .= " App_bootDCAllowed.idBootDC=$idBootDC ";
        $x= true;
    }
    if(!empty($allowedIds)){
        if($x){
            $filters .= " AND ";
        }
        $x=true;
        $filters .= " allowedIds LIKE '%$allowedIds%' ";
    }
    if(!empty($response)){
        if($x){
            $filters .= " AND ";
        }
        $x=true;
        $filters .= " response LIKE '%$response%' ";
    }
    
   
   
    $CLD_CON->OpenRs("SELECT App_bootDCAllowed.`id`, App_bootDCAllowed.`UPGRADEid`, App_bootDCAllowed.`idBootDC`, App_bootDCAllowed.`allowedIds`, App_bootDCAllowed.`response`, App_bootDC.textLine FROM `App_bootDCAllowed` "
            . " LEFT JOIN App_bootDC on App_bootDC.idBootDC=App_bootDCAllowed.idBootDC "
            . " WHERE $filters ORDER BY UPGRADEid ");
    
    
}
else{
    $CLD_CON->OpenRs("SELECT App_bootDCAllowed.`id`, App_bootDCAllowed.`UPGRADEid`, App_bootDCAllowed.`idBootDC`, App_bootDCAllowed.`allowedIds`, App_bootDCAllowed.`response`, App_bootDC.textLine FROM `App_bootDCAllowed` "
            . " LEFT JOIN App_bootDC on App_bootDC.idBootDC=App_bootDCAllowed.idBootDC "
            . "ORDER BY UPGRADEid");
   
    
}

echo "<div class='inContent'>";
    echo "<ul class='UpgHead' >";
    echo "<li  style='width:10%;' title='UPGRADEid' >UPGRADEid </li>";
    echo "<li  style='width:35%;' title='idBootDC' >idBootDC </li>";     
    echo "<li title='allowedIds' style='width:20%;' >allowedIds</li>";
    echo "<li title='Response' style='width:25%;' >response</li>";
    echo "<li title='Delete' style='width:5%;' >&nbsp;</li>";
    echo "<li title='Delete' style='width:5%;' >ACTIONS</li>";
    
 
    echo "</ul>";

while ($CLD_CON->FetchArray()) {
    $id = $CLD_CON->GetArrayField('id');
    $UPGRADEid = $CLD_CON->GetArrayField('UPGRADEid');
    $idBootDC = stripslashes($CLD_CON->GetArrayField('idBootDC'));
    $textLine = stripslashes($CLD_CON->GetArrayField('textLine'));
    $allowedIds = $CLD_CON->GetArrayField('allowedIds');
    $response = stripslashes($CLD_CON->GetArrayField('response'));    
    if($response==NULL || $response=='NULL')$response="ALL PBs";
    
    echo "<ul class='regUpg' >";
    echo "<li  style='width:10%;' title='UPGRADEid'onclick='edit(83 , $id)' >$UPGRADEid &nbsp;</li>";
    echo "<li  style='width:35%;' title='idBootDC' onclick='edit(83 , $id)' >$idBootDC - $textLine&nbsp;</li>";     
    echo "<li title='allowedIds' style='width:20%;' onclick='edit(83 , $id)' >$allowedIds&nbsp;</li>";
    echo "<li title='Response' style='width:25%;' onclick='edit(83 , $id)' >$response&nbsp;</li>";
    echo "<li title='Delete' style='width:5%;' onclick='edit(83 , $id)' ><img src='images/web/edit.png' class='' alt='Delete Allowed Upgrade' ></li>";
    echo "<li title='Delete' style='width:5%;' onclick='deletebootDCAllowed($id)' ><img src='images/web/trash.png' class='' alt='Delete Allowed Upgrade' ></li>";
    
 
    echo "</ul>";
   
}


echo "</div>";

$s = "upgrades";
$color="#8989BA";
include '../../pagescount.php';

?>

