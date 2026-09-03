<?php

$serialnumber   = null;  
$pb_name        = null;
$distributor    = null;
$status         = null;
$date_sold      = null;
$datetOwner     = null;
$type           = null;
$types_ids      = array();
$types_names    = array();
$owner_id       = array();
$distributors_ids  = array();
$distributors_names = array();
$donglepairing_array = array();

/*Model*/
function getPbsInfo($CLD_CON,$ID){
    global $serialnumber,$pb_name,$distributor,$status,$date_sold,$datetOwner,$type, $owner_id;
    
    $CLD_CON->OpenRs(
       "SELECT rentals.name AS owner_name, app_b.serialnumber, app_b.name AS pb_name, app_b.CLD_status, app_b.CLD_date_sold, app_b.CLD_date_tOwner, distri.name AS dist_name, CLD_boothTypes.name AS
        type , app_b.owner AS owner_id
        FROM App_booths AS app_b
        LEFT JOIN rentals ON app_b.owner = rentals.id
        LEFT JOIN CLD_Distributors AS distri ON app_b.CLD_Distributor = distri.id
        LEFT JOIN CLD_boothTypes ON app_b.CLD_idType = CLD_boothTypes.id
        WHERE app_b.idBooth = $ID"
    );

    if($CLD_CON->FetchArray()){
        $serialnumber   = $CLD_CON->GetArrayField("serialnumber");
        $pb_name        = $CLD_CON->GetArrayField("pb_name");
        $distributor    = $CLD_CON->GetArrayField("dist_name");
        $status         = $CLD_CON->GetArrayField("CLD_status");
        $date_sold      = $CLD_CON->GetArrayField("CLD_date_sold");
        $datetOwner     = $CLD_CON->GetArrayField("CLD_date_tOwner");
        $type           = $CLD_CON->GetArrayField("type");
        $owner_id       = $CLD_CON->GetArrayField("owner_id");
    }
}

function getPbsTypes($CLD_CON){
    global $types_ids,$types_names;

    $CLD_CON->OpenRs(
       "SELECT  id, name
        FROM CLD_boothTypes;"
    );
    if($CLD_CON->GetRsRows()){
        $types_ids = array();
        $types_names = array();
        while ($CLD_CON->FetchArray()) {
            array_push($types_ids, $CLD_CON->GetArrayField("id"));
            array_push($types_names, $CLD_CON->GetArrayField("name"));
        }
    }
}

function getDistributors($CLD_CON){
    global $distributors_ids,$distributors_names;
    
    $CLD_CON->OpenRs(
       "SELECT  id, name
        FROM CLD_Distributors;"
    );
    if($CLD_CON->GetRsRows()){
        $distributors_ids = array();
        $distributors_names = array();
        while ($CLD_CON->FetchArray()) {
            array_push($distributors_ids, $CLD_CON->GetArrayField("id"));
            array_push($distributors_names, $CLD_CON->GetArrayField("name"));
        }
    }
}

function getDonglePairing($CLD_CON, $ID){
    global $donglepairing_array;
    $CLD_CON->OpenRs(
        "
        SELECT  App_boothDongle.idDongle,  booths.rand_string, rentals.name AS owner_name, CLD_Distributors.Name AS distributor_name , App_boothDongle.datetimeS, App_boothDongle.datetimeF
        FROM App_boothDongle
        LEFT JOIN booths
        ON booths.id = App_boothDongle.idDongle
        LEFT JOIN App_booths
        ON App_booths.idBooth = App_boothDongle.idBooth
        LEFT JOIN rentals
        ON rentals.id = booths.rental_id
        LEFT JOIN CLD_Distributors
        ON CLD_Distributors.id = booths.CLD_Distributor
        WHERE App_boothDongle.idBooth = $ID
        ORDER BY datetimeS DESC;"
    );
    
    if($CLD_CON->GetRsRows()){
        $i = 0;
        $donglepairing_array = array();
        while ($CLD_CON->FetchArray()) {
            $donglepairing_array[$i] = array();
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("idDongle"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("rand_string"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("owner_name"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("distributor_name"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("datetimeS"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("datetimeF"));
            $i ++; 
       }
    } 
}
/*End*/


function selector_types($types_ids, $types_names, $type){
    $html = "<div class='input_div'>";
    $html .=  "<div class='label'>Type</div>";
    $html .= "<select name='pb_type' id='type'>";
    $html .= "<option value=''>            </option>";
        for ($i = 0; $i < count($types_ids); $i++) {
            if($types_names[$i] == $type){
                $html .= "<option value='{$types_ids[$i]}' selected >{$types_names[$i]}</option>";
            }
            else{
                $html .= "<option value='{$types_ids[$i]}'>{$types_names[$i]}</option>";
            }
        }
    $html .= "</select>";  
    $html .= "</div>";

    return $html;
} 

function autocomplete_owner($owner, $owner_id){
    $html = "<div class='input_div'>";
    $html .= "<div class='label'>Owner</div>";
    $html .= "<input id='ownerName' name='owner_name_id'  value='$owner' />";
    $html .= "<input id='ownerNameId' name='owner_name' type='hidden' value='$owner_id' />";
    $html .= "</div>";
    
    return $html;
}

function selector_distributors($distributors_ids, $distributors_names, $distributor){
     $html = "<div class='input_div'>";
    $html .= "<div class='label'>Distributor</div>";
    $html .= "<select id='distributors' name='distributor_id'>";
        for ($i = 0; $i < count($distributors_ids); $i++) {
            if($distributors_names[$i] == $distributor){
                $html .= "<option value='{$distributors_ids[$i]}' selected >{$distributors_names[$i]}</option>";
            }
            else{
                $html .= "<option value='{$distributors_ids[$i]}'>{$distributors_names[$i]}</option>";
            }
        }
    $html .= "</select>";
    $html .= "</div>";

    return $html;
    
}

function tableDonglePairing($donglepairing_array, $ID){
    $html = "<div id='table' class='popup-margin-top'>";
        $html .= "<table class='matching_table'> ";
            $html .= "<tr>";
                $html .= "<td>String</td>";
                $html .= "<td>Owner</td>";
                $html .= "<td>Distributor</td>";
                $html .= "<td>Start Date</td>";
                $html .= "<td>Finish Date</td>";
                $html .= "<td> <div id='addDongleParing'></div></td>";
            $html .= "</tr>";
            $html .= "<tr id='rowAddNew'>";
                $html .= "<td colspan='6'>";
                    $html .= "<div id='addDongleParingContent'>";
                        $html .= "<div id='addInputContent'>";
                            $html .= "Insert the dongle String to pair with the PB: ";
                            $html .= "<input id='addDongleString' type='text' name='string' value='' autocomplete='off'/>";
                            $html .= "<input id='addDongleId' type='hidden' name='id' value=''/>";
                        $html .= "</div>";
                    $html .= "<div id='btnscontent'>";
                        $html .= "<div id='acceptAdd' pb='{$ID}'>";
                        $html .= "<i class='fa fa-check' aria-hidden='true'></i>";
                        $html .= "</div>";
                        $html .= "<div id='cancelAdd'>";
                            $html .= "<i class='fa fa-times' aria-hidden='true'></i>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</td>";
            $html .= "</tr>";
            
            if(count($donglepairing_array) > 0){
                for ($i = 0; $i < count($donglepairing_array); $i++) {
                    $html .= "<tr>";
                        $html .= "<td>{$donglepairing_array[$i][1]}</td>";
                        $html .= "<td><div class='cel_text_img'>{$donglepairing_array[$i][2]}</div></td>";
                        $html .= "<td><div class='cel_text_img'>{$donglepairing_array[$i][3]}</div></td>";
//                        $html .= "<td><div class='cel_text_img'>{$donglepairing_array[$i][2]}<img src='images/web/edit.png' class='edit_owner' pb='$ID' id='{$donglepairing_array[$i][0]}'></img></div></td>";
//                        $html .= "<td><div class='cel_text_img'>{$donglepairing_array[$i][3]}<img src='images/web/edit.png' class='edit_distri' pb='$ID' id='{$donglepairing_array[$i][0]}'></img></div></td>";
                        $html .= "<td>{$donglepairing_array[$i][4]}</td>";
                        $html .= "<td>{$donglepairing_array[$i][5]}</td>";
                        $html .= "<td><div class='delete' pb='{$ID}' id='{$donglepairing_array[$i][0]}' sdate='{$donglepairing_array[$i][4]}' fdate='{$donglepairing_array[$i][5]}'></div></td>";
                    $html .= "</tr>";
                }
            }
            else{
                $html .= "<tr>";
                $html .= "<td colspan='6'>Any dongle pairing yet</td>";
                $html .= "</tr>";
            }
        $html .= "</table>";
    $html .= "</div>";
    
    return $html;
}

getPbsInfo($CLD_CON,$ID);
getPbsTypes($CLD_CON);
getDistributors($CLD_CON);
getDonglePairing($CLD_CON, $ID);

if(isset($date_sold)){
    $date_sold = utils::datetime_to_date_std($date_sold, 'Y-m-d H:i:s', 'm/d/Y');
}
if(isset($datetOwner)){
    $datetOwner = utils::datetime_to_date_std($datetOwner, 'Y-m-d H:i:s', 'm/d/Y');
}

$html = "<script src='sections/photobooths/resources/js/info.js'></script>";
$html .= "<link rel='stylesheet' href='assets/libraries/font-awesome-4.7.0/css/font-awesome.min.css'> ";

$title = "PhotoBooth $ID Info";
    $html .=  "<div id='poup_conten'>";
        $html .= "<form id='pbs_info' method='post'>";
            $html .= "<div class='input_div'><div class='label'>Serialnumber</div> <input type='text' name='serialnumber' value='$serialnumber'><input id='pbId' name='pbs_id' type='hidden' value='$ID' /></div>";      
            $html .= "<div class='input_div'><div class='label'>Name</div> <input type='text' name='name' value='$pb_name'></div>";
            $html .= selector_types($types_ids, $types_names, $type);
//            $html .= autocomplete_owner($owner, $owner_id);
            $html .= selector_distributors($distributors_ids, $distributors_names, $distributor);
//            $html .= "<div class='input_div'>$date_sold</div>";
            $html .= "<div class='input_div'><div class='label'>Status</div> <input type='text' name='status' value='$status'></div>";
            $html .= "<div class='input_div'><div class='label'>Date_sold</div> <input type='text' name='date_sold' id='date_sold' value='$date_sold'></div>";
            $html .= "<div class='input_div'><div class='label'>DatetOwner</div> <input type='text' name='datetOwner' id='datetOwner' value='$datetOwner'></div>";
        $html .= "</form>";
    $html .= "</div>";
    $html .= tableDonglePairing($donglepairing_array,$ID);
//    $html .= "<div id='div_buttons'>";
//        $html .= "<div id='cancel'></div>";
//        $html .= "<div id='save'></div>";
//    $html .= "</div>";
    
$content = $html;

//$buttons .= "<div class='popup-confirm' id='saveV2'>Accept</div>";
//$buttons .= "<div class='popup-cancel' id='cancelV2'>Cancel</div>";

$buttons .= "<button type='button' class='popup-confirm' onclick='saveInfoPb({$ID}); hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);