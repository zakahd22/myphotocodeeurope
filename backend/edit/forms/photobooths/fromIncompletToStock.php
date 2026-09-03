<?php

$e = "";
$compo= "";
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT CLD_idType , type , serialnumber FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $idType = $CLD_CON->GetArrayField("CLD_idType");
    $type = $CLD_CON->GetArrayField("type");
    $serialnumber = $CLD_CON->GetArrayField("serialnumber");
}
$CLD_CON->OpenRs("SELECT id_component , quantitat FROM CLD_boothsComponents WHERE id_typeBooth=$idType AND optional=0;");
while($CLD_CON->FetchArray()) {
    $cmp = $CLD_CON->GetArrayField("id_component");
    $qty = $CLD_CON->GetArrayField("quantitat");
    $CLD_CON2->OpenRs("SELECT * FROM CLD_components WHERE type=$cmp AND booth=$ID");
    $q = $CLD_CON2->GetRsRows();
    $CLD_CON3->OpenRs("SELECT  descripcio FROM CLD_typeComponents WHERE id=$cmp");
    if ($CLD_CON3->FetchArray()) {
        $cmpDes = $CLD_CON3->GetArrayField("descripcio");
    }
    if ($q < $qty) {
        $e .="<p> Falten " . ($qty - $q) . " $cmpDes</p>";
    } else {
       while($CLD_CON2->FetchArray()){
           $serialnumber_cmp = $CLD_CON2->GetArrayField("serialnumber");
           $compo .="<p> $cmpDes -> $serialnumber_cmp </p>" ;
       } 
    }
}

$title = "$serialnumber to Finish Factory Product";
$content = "";
$buttons = "";

if (!empty($e)) {
    $content .= "<div class='popup-text popup-margin-bottom'>";
        $content .= "You must enter serial numbers of the all components.";
    $content .= "</div>";
    
    $content .= "<div style='height:60%;width:90%;margin-left:5%;border:1px solid gray;overflow:auto;'>" . $e . "</div>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";
} else {
    $content .= "<div class='popup-text popup-margin-bottom'>";
        $content .= "The serial numbers entered are, found to be well and select the person who made the Quality control.";
    $content .= "</div>";
    
    $content .= "<div style='height:40%;width:90%;margin-left:5%;border:1px solid gray;overflow:auto;'>" . $compo . "</div>";
    $content .= "<p style='text-align:center;'> Control Quality Person : <select id='cQ' class='selectText'>";
    $content .= "<option value='0'>------</option>";
    $content .= "<option value='JS'>JS</option>";
    $content .= "<option value='JT'>JT</option>";
    $content .= "</select></p>";
    
    $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='toStockDis($ID , 7); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";
}  
    $content .= <<<HTML
<script>
    function toStockDis(id , from){
        var cQ = $("#cQ").val();
        if(cQ === "0"){
            alert("Please select the Control Quality Person");
        }else{
            var ajaxData = {id:id , cQ:cQ , from:from};
             $.ajax({
                    url: 'edit/functions/photobooths/toStockDis.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data) {
                        if (data === "OK") {
                            closePopup();
                            profile("photobooths", "info", id);
                        } else {
                            alert(data);                           
  
                        }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);