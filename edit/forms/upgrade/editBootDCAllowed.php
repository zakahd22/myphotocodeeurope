<?php
//require_once dirname(__FILE__) . '/../../../common/global.php';
//require_once G_PATH . 'common/conexio.php';
$baseController->createModel('App_bootDCAllowed');

function utf8_converter($array){
   array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
   });
    return $array;
}

$CLD_CON->OpenRs("SELECT *
                    FROM App_bootDCAllowed        
                    WHERE id = $ID");
$id = 0;
if($CLD_CON->FetchArray()){
    
    $id = $CLD_CON->GetArrayField("id");
    $UPGRADEid = $CLD_CON->GetArrayField("UPGRADEid");
    $idBootDC = $CLD_CON->GetArrayField("idBootDC");
    $allowedIds = $CLD_CON->GetArrayField("allowedIds");
    $response = $CLD_CON->GetArrayField("response");
}    

$CLD_CON->OpenRs("SELECT * FROM `App_bootDC` ");
$optionsIdBootDC = "";
while ($CLD_CON->FetchArray()) {
    $selected = "";
    $idBootDCSelect = stripslashes($CLD_CON->GetArrayField('idBootDC'));
    $textLine = stripslashes($CLD_CON->GetArrayField('textLine'));
    if($idBootDCSelect == $idBootDC){
         $selected  = " selected ";
    }
    
    
    $optionsIdBootDC .= "<option class='' value='$idBootDCSelect' $selected >$idBootDCSelect - $textLine</option>";
   
}


$html = "<script src='sections/upgrade/resources/js/upgradeEdit.js'></script>";
//$html .= "<link rel='stylesheet' href='sections/upgrade/resources/css/upgradeEdit.css'>";
$html .= "<link rel='stylesheet' href='sections/upgrade/resources/css/upgradeAddNew.css'>";

//$CLD_CON->OpenRs("SELECT *
//                    FROM App_bootDCAllowed        
//                    WHERE id = $ID");

if($id){
    
//    $id = $CLD_CON->GetArrayField("id");
//    $UPGRADEid = $CLD_CON->GetArrayField("UPGRADEid");
//    $idBootDC = $CLD_CON->GetArrayField("idBootDC");
//    $allowedIds = $CLD_CON->GetArrayField("allowedIds");
//    $response = $CLD_CON->GetArrayField("response");
    
    $title = "Upgrade Edit";
    
   
    
    
    if(is_null($allowedIds) || $allowedIds==NULL){
        $allowedIds = '';
        $allowChecked=' checked="checked" ';
        $allowReadonly = ' readonly ';
    }
    
    
    $html .= <<<HTML
              <div id='popup_conten'>
             <form id='upgrade_info' method='post'>
                <input type='hidden'  id='id' name='id' value='$id'>
                <div class"posDiv" style='width:300px;'>
                    <b>Type</b>
        <br>
                </div>  
                <select id='idBootDC' class='popupInputLarge' name='idBootDC' value="" >
                    $optionsIdBootDC
                </select>
                <br>
                <div class"posDiv" >
        <br>
                    <b>UPGRADEid</b> <input type='text' class='popup-input-large' style='width:100%;' id='UPGRADEid' name='UPGRADEid' value='$UPGRADEid'>
        <br>
        <br>
           
                    <b>allowedIds</b> <input type='text' class='popup-input-large' style='width:100%;' id='allowedIds' name='allowedIds' placeholder='Insert the ids separated by commas'  value='$allowedIds' $allowReadonly> <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="upgradeAll" $allowChecked>
    <label class="form-check-label" for="exampleCheck1">Upgrade All <br></label>
        <br>
                    <b>Response</b> <input type='text' class='popup-input-large' style='width:100%;' id='response' name='response' value='$response'>
        <br>
        <br>
                    <b>Admin Security Key:</b>&nbsp <input type='text' class='popup-input-large' style='width:100%;' id='secKey' name='secKey' value=''>
  </div>
        <br>
                </div>               
              
            </form>
        </div>
        <script>
    $(document).ready(function() {
        

        $("#upgradeAll").on("change", function() {
            $("#allowedIds").val('');
            if($("#allowedIds").prop("readonly") == true){
                $("#allowedIds").prop('readonly', false);                
                $("#allowedIds").attr('placeholder', 'Insert the ids separated by commas');
            }else{
                $("#allowedIds").prop('readonly', true);
                $("#allowedIds").attr("placeholder", "All - This will update all PBs in this version and model");
            }
        
           
        
        });
        
        
        
    });


    
</script>
                
HTML;
                
    
    
  
           
   
}


$content = $html;

$buttons .= "<button type='button' class='popup-confirm' onclick='saveBootDCAllowed({$ID}); hidePopupv2(); '>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$json = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

$json = utf8_converter($json);

echo json_encode($json);



