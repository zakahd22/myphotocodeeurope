<?php
//header('Content-Type: application/json');
require_once dirname(__FILE__) . '/../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

if(isset($_SESSION['USERID'])){
    $USERID = $_SESSION['USERID'];
}

$title = "Allow BootDC to be Upgraded";
$content = "";
$buttons = "";


$content .= "<script src='sections/upgrade/resources/js/upgradeAddNew.js'></script>";
$content .= "<link rel='stylesheet' href='sections/upgrade/resources/css/upgradeAddNew.css'>";


$CLD_CON->OpenRs("SELECT * FROM `App_bootDC` ");
$optionsIdBootDC = "";
while ($CLD_CON->FetchArray()) {
    
    $idBootDC = stripslashes($CLD_CON->GetArrayField('idBootDC'));
    $textLine = stripslashes($CLD_CON->GetArrayField('textLine'));
    
    
    
    $optionsIdBootDC .= "<option class='' value='$idBootDC'>$idBootDC - $textLine</option>";
   
}

$CLD_CON->OpenRs("SELECT UPGRADEid FROM `App_boothBootDC`  GROUP BY UPGRADEid");
$optionsUPGRADEid = "";
$i=0;
while ($CLD_CON->FetchArray()) {
    $i++;
    $UPGRADEid = stripslashes($CLD_CON->GetArrayField('UPGRADEid'));
    
    
    
    
    $optionsUPGRADEid .= '<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="UPGRADEid'.$i.'" name="UPGRADEidArr" value="'.$UPGRADEid.'">
            <label class="form-check-label" for="inlineCheckbox1">'.$UPGRADEid.'</label>
          </div>';
   
}


$content .= <<<HTML
        <div id='popup_conten'>
             <form id='addUpgrades' method='post'>
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
                    <b>UPGRADEid</b> <!--<input type='text' class='popup-input-large' style='width:100%;' id='UPGRADEid' name='UPGRADEid'>-->
        <br>
        <br>
        <div class="row"> 
              $optionsUPGRADEid
         
        </div>
                    <b>allowedIds</b> <input type='text' class='popup-input-large' style='width:100%;' id='allowedIds' name='allowedIds' placeholder='Insert the ids separated by commas'> <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="upgradeAll">
    <label class="form-check-label" for="exampleCheck1">Upgrade All <br></label>
        <br>
                    <b>Response</b> <input type='text' class='popup-input-large' style='width:100%;' id='response' name='response'>
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

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveUpgrade(); hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);