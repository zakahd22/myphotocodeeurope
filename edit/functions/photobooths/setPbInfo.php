<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$json = json_decode($_POST["dades"], TRUE);

for($i=0; $i< count($json); $i++){
    if ($json[$i]['name']){
        $array[$json[$i]['name']] = $json[$i]['value'];
    }
}

$sqlDateSold = "NULL";
$datetOwner = "NULL";
$pb_type = "NULL";
$status = "NULL";

if($array['date_sold'] != ""){    
    $array['date_sold'] = utils::datetime_to_date_std($array['date_sold'], 'm/d/Y', 'Y/m/d');
    $array['date_sold'] = str_replace('/', '', $array['date_sold']);
    $sqlDateSold = utils::datepicker_to_date_std($array['date_sold']);
    $sqlDateSold = "'" . utils::date_std_to_datetime($sqlDateSold) . "'";    
}
if($array['datetOwner'] != ""){
    $array['datetOwner'] = utils::datetime_to_date_std($array['datetOwner'], 'm/d/Y', 'Y/m/d');
    $array['datetOwner'] = str_replace('/', '', $array['datetOwner']);
    $datetOwner = utils::datepicker_to_date_std($array['datetOwner']);
    $datetOwner = "'" . utils::date_std_to_datetime($datetOwner) . "'";
}
if($array['status'] != ""){
    $status = $array['status'];
}
if($array['pb_type'] != ""){
    $pb_type = $array['pb_type'];
}

$result = "Error";

$sql = "
    UPDATE App_booths 
    SET name = '{$array['name']}',serialnumber = '{$array['serialnumber']}', CLD_Distributor = {$array['distributor_id']},
    CLD_date_tOwner = {$datetOwner}, CLD_date_sold = {$sqlDateSold}, CLD_status = {$status}, CLD_idType = {$pb_type}
    WHERE idBooth = {$array['pbs_id']}
";
    
if($CLD_CON->Execute($sql)){
    $result = TRUE;
}

echo $result;