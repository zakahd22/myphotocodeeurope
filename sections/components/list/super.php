<?php
$CLD_CON->OpenRs("SELECT id , Name FROM CLD_Distributors");
$dist = array();
while($CLD_CON->FetchArray()){
    $i = $CLD_CON->GetArrayField("id");
    $dn = $CLD_CON->GetArrayField("Name");
    
    $dist[$i] = $dn;
}

if(isset($_POST['fil'])){
    $sn = $_POST['sn'];
    $owner = $_POST['owner'];
    $dis = $_POST['dis'];
    $tipo = $_POST['type'];
    $x = false;
    $filters = "";
    
    if(!empty($sn)){
        $x=true;
        $filters .= " c.serialnumber LIKE '%$sn%' ";
    }
    
    if($owner!=0){
        if($x){
            $filters .= " AND ";
        }
        $filters .= " c.owner=$owner ";
        $x= true;
    }
    
      if($dis!=0){
        if($x){
            $filters .= " AND ";
        }
        $filters .= " c.distributor=$dis ";
        $x= true;
    }    
    if($tipo!=0){
            if($x){
            $filters .= " AND ";
        }
        $filters .= " c.type=$tipo ";
    }
   
    $CLD_CON->OpenRs("SELECT tc.marca , tc.model, c.distributor , tc.controlable , tc.descripcio ,tc.type ,c.serialnumber ,r.name , r.id FROM CLD_typeComponents tc RIGHT JOIN CLD_components c LEFT JOIN rentals r ON c.owner=r.id ON c.type = tc.id WHERE $filters ORDER BY c.type , c.serialnumber");
    $select_nolimit = "SELECT tc.marca , tc.model, c.distributor , tc.controlable , tc.descripcio ,tc.type ,c.serialnumber ,r.name , r.id FROM CLD_typeComponents tc RIGHT JOIN CLD_components c LEFT JOIN rentals r ON c.owner=r.id ON c.type = tc.id WHERE $filters ORDER BY c.type , c.serialnumber";
}
else{
    $CLD_CON->OpenRs("SELECT tc.marca , tc.model, c.distributor , tc.controlable , tc.descripcio ,tc.type ,c.serialnumber ,r.name , r.id FROM CLD_typeComponents tc RIGHT JOIN CLD_components c LEFT JOIN rentals r ON c.owner=r.id ON c.type = tc.id ORDER BY c.type , c.serialnumber LIMIT $LIMIT");
    $select_nolimit = "SELECT c.serialnumber FROM CLD_typeComponents tc RIGHT JOIN CLD_components c LEFT JOIN rentals r ON c.owner=r.id ON c.type = tc.id ORDER BY c.type , c.serialnumber";
}

echo "<div class='inContent'>";


while ($CLD_CON->FetchArray()) {
    $componentSN = $CLD_CON->GetArrayField('serialnumber');
    $componentDesc = stripslashes($CLD_CON->GetArrayField('descripcio'));
    $isControlable = $CLD_CON->GetArrayField('controlable');
    $ownerName = stripslashes($CLD_CON->GetArrayField('name'));
    $ownerID = $CLD_CON->GetArrayField('id');
    $Distributorid = $CLD_CON->GetArrayField("distributor");
    if(empty($Distributorid)){
         $DName = "-";
    }else{
         $DName = $dist[$Distributorid];
    }
    
    echo "<ul class='regCompUL' onclick='setSection(\"components\" ,  2 ,  \"$componentSN\")'>";
    echo "<li  style='width:25%;' title='Serial Number' >$componentSN </li>";
    echo "<li  style='width:25%;' title='Component Description' >$componentDesc </li>";
     if (empty($ownerName)) {
        echo "<li title='No Owner' style='width:25%;' >-</li>";
    } else {
       echo "<li title='Owner Name'  style='width:25%;' class='link' onclick='openLink(\"Owner\" ,$ownerID);'>$ownerName</li>";
    }
    
    
     echo "<li title='Distributor' style='width:25%;' >$DName</li>";
  /*  if ($isControlable) {
        echo "<li title='Controlable/NoControlable' style='width:25%;'>Controlable</li>";
    } else {
        echo "<li title='Controlable/NoControlable' style='width:25%;'>No Controlable</li>";
    }*/
    echo "</ul>";
    /* $CLD_CON3->OpenRs("SELECT b.name , bc.quantitat FROM CLD_boothsComponents bc LEFT JOIN booth_types b ON bc.id_typeBooth = b.id WHERE id_component=$componentID");
      echo "<div style='border:solid;overflow:auto;height:30px;'>";
      while($CLD_CON3->FetchArray()){
      $boothType = $CLD_CON3->GetArrayField('name');
      $qty = $CLD_CON3->GetArrayField('quantitat');
      echo"<ul class='list'><li class='list-column'>$boothType</li><li class='list-column'>$qty</li></ul>";
      }
      echo "</div>"; */
}


echo "</div>";

$s = "components";
$color="#8989BA";
include '../../pagescount.php';

?>

