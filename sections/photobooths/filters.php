<?php
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";


$baseController = new baseController();
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('CLD_Distributors');
$baseController->createModel('App_boothBootDC');

$html = "";
$a = "";

if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6) {

$html .= <<<HTML
    <div style='background-color:#35B5E7;' class='fDiv'>
        <table >
            <tr>
                <td>
                    SN
                </td>
                <td>
                    <input type='text' id='serialnumber' class='textInput'>
                </td>
                <td>
                    Owner
                </td>
                <td> 
                    <input type='text' id='owner' class='textInput'>
                </td>
                <td>
                    Dongle String
                </td>
                <td>
                    <input type='text' id='dStr' class='textInput'>
                </td>
                <td> 
                    IdPb:
                </td>
                <td>
                    <input type='text' id='idPb' class='textInput numInput'>
                </td>
            </tr>
            
            <tr>
                <td>Status</td><td>
                    <select class='selectText' id='stat'>
                        <option value='N'>-Status-</option>
                        <option value='0'>-Production-</option>
                        <option value='1'>-Finish Product-</option>
                        <option value='2'>-Distributor Stock-</option>
                        <option value='3'>-Owner-</option>
                        <option value='4'>-Returned-</option>
                        <option value='5'>-Returned & Damaged-</option>
                        <option value='6'>-Damaged-</option>
                        <option value='7'>-Incomplete-</option>
                        <option value='8'>-Refurbished-</option>
                    </select>
                </td>
                <td > Type </td><td>
                    <select class='selectText' id='tipo' style='text-align:left;'>
                        <option value='0'>-Type-</option>
HTML;
                        $html .= getBoothTypes($baseController);     
$html .= <<<HTML
                    </select>
                </td>
                <td>
                    Distributor
                </td>
                <td>
                    <select class='selectText' id='distributor' style='text-align:left;'>
                    <option value='0'>-Distributor-</option>
HTML;
                        $html .= getDistributorsOptions($baseController);
                        
                        
if($_SESSION['USERTYPE'] == 1){
$html .= <<<HTML
                    </select>
                </td>
                <td>
                    UPGRADEid
                </td> 
                <td>
                    <select class='selectText' id='UPGRADEid' style='text-align:left;'>
                           <option value='0'>-UPGRADEid-</option>
HTML;
                        $html .= getUPGRADEidOptions($baseController);
}                        
$html .= <<<HTML
                    </select>
                </td>    
                <td>
                    <input type='button' class='okB okButton' onclick='filtersPhotoBooths();' style='top:-5px;'>
                </td>   
            </tr>
        </table>
    </div>
HTML;
  
} 
if ($_SESSION['USERTYPE'] < 6 && $_SESSION['USERTYPE'] > 3) {

$html .= <<<HTML
    <div style='background-color:#35B5E7;' class='fDiv'>
        <table >
            <tr>
                <td>
                    SN
                </td>
                <td>
                    <input type='text' id='serialnumber' class='textInput'>
                </td>                
                <td>
                    Dongle String
                </td>
                <td>
                    <input type='text' id='dStr' class='textInput' style='width: 60px'>
                </td>
                <td> 
                    IdPb
                </td>
                <td>
                    <input type='text' id='idPb' class='textInput numInput'>
                </td>
        <td > Type </td><td>
                    <select class='selectText' id='tipo' style='text-align:left;'>
                        <option value='0'>-Type-</option>
HTML;
                        $html .= getBoothTypes($baseController);     
$html .= <<<HTML
                    </select>
                </td>
                
                <td>
                    <input type='button' class='okB okButton' onclick='filtersPhotoBooths();' style='top:-5px;'>
                </td>  
            </tr>
        </table>
    </div>
HTML;
  
} 
if ($_SESSION['USERTYPE'] == 3) {
    $html .= <<<HTML
        <div style='background-color:#35B5E7;' class='fDiv'>
            <table >
                <tr>
                    <td>SN</td><td><input type='text' id='serialnumber' class='textInput'></td>
                    <td>Status</td><td>
                        <select class='selectText' id='stat'>
                            <option value='N'>-Status-</option>
                            <option value='2'>-Stock-</option>
                            <option value='3'>-Owner-</option>
                            <option value='4'>-Returned-</option>
                            <option value='5'>-Returned & Damaged-</option>
                            <option value='6'>-Damaged-</option>
                            <option value='7'>-Incomplete-</option>
                            <option value='8'>-Refurbished-</option>
                        </select>
                    </td>
                    <td > Type </td><td colspan=3>
                        <select class='selectText' id='tipo' style='text-align:left;'>
                            <option value='0'>-Type-</option>
HTML;
                            $html .= getBoothTypes($baseController);
    $html .= <<<HTML
                        </select>
                    </td>
                    <td>
                        <input type='button' class='okB okButton' onclick='filtersPhotoBooths();' style='top:-5px;'>
                    </td>
                </tr>
            </table>
            <input type='hidden' id='dStr' class='textInput'>
            <input type='hidden' id='owner' class='textInput'>
        </div>
HTML;
}


$html .= <<<HTML
<script>
    $(document).ready(function() {
        $("#serialnumber").keyup(function(event) {
            if (event.which == 13) {
                filtersPhotoBooths();
            }
        });
        $("#dStr").keyup(function(event) {
            if (event.which == 13) {
                filtersPhotoBooths();
            }
        });
        $("#owner").keyup(function(event) {
            if (event.which == 13) {
                filtersPhotoBooths();
            }
        });
        $("#idPb").keyup(function(event) {
            if (event.which == 13) {
                filtersPhotoBooths();
            }
        });
    });


    function filtersPhotoBooths() {
        var sn          = $("#serialnumber").val();
        var dStr        = $("#dStr").val();
        var owner       = $("#owner").val();
        var status      = $("#stat").val();
        var type        = $("#tipo").val();
        var distributor = $("#distributor").val();
        var idPb        = $("#idPb").val();
        var UPGRADEid   = $("#UPGRADEid").val();
        
        var data = {sn: sn, dStr: dStr, owner: owner, status: status, type: type, fil: 1 , distributor : distributor, idPb : idPb, UPGRADEid : UPGRADEid};
            filters("photobooths", data);

    }
</script>
     



   
HTML;

//if (sn.length > 0 || dStr.length > 0 || owner.length > 0 || idPb.length > 0 || status != 'N' || type !== '0' ||distributor !== '0') {
            
//        }

echo $html;


function getBoothTypes($baseController){
    $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypesModels();
    
    $html =  "";
            
    foreach ($boothTypes as $boothType){
        $id = $boothType["id"];
        $snM = $boothType["CLD_modelSN"];
        $name = $boothType["name"];

    $html .= "<option value='$id'> $snM - $name</option>";
    }
    
    return $html;
}


function getDistributorsOptions($baseController){
    $distributors = $baseController->CLD_DistributorsModel->getDistributors();
    
    $html =  "";
    
    foreach ($distributors as $distributor){
        $idD = $distributor["id"];
        $nameD = $distributor["Name"];
        
        $html .= "<option value='$idD'>$nameD</option>";
    }
    
    return $html;
}

function getUPGRADEidOptions($baseController){
    $UPGRADEids = $baseController->App_boothBootDCModel->getUPGRADEids();
    
    $html =  "";
    
    foreach ($UPGRADEids as $UPGRADEid){
        $idU = $UPGRADEid["UPGRADEid"];
        $nameU = $UPGRADEid["UPGRADEid"];
        
        $html .= "<option value='$idU'>$nameU</option>";
    }
    
    return $html;
}



function getOwner__(){
    
    $html = "<div>";
    $html .= "Trace 1";
   
    for($i = 0; $i > 10; $i++){
        $html .= "Trace" . $i; 
    }
    $html .= "Trace 2";
    $html .= "Trace 3";
    $html .= "Trace 4";
    $html .= "</div>";
    
    return $html;
}