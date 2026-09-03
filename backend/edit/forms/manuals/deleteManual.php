<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$baseController = new baseController();

$json = json_decode($_POST["data"], TRUE);
$id_manual = $json[0];
$i = $json[1];
utils::log($id_manual, "logasd");
if($i == 1){
    foreach ($id_manual as $id){
        $CLD_CON->OpenRs("select data FROM manualsItems WHERE manual_id = $id");
        while ($CLD_CON->FetchArray()) {
            $data[] = $CLD_CON->GetArrayField("data"); 
        }

        foreach ($data as &$data1){
            $CLD_CON->OpenRs("select data from manualsItems WHERE data = '{$data1}' && manual_id != $id");
            while ($CLD_CON->FetchArray()) {
                $data2 = $CLD_CON->GetArrayField("data"); 
            }
            if($data2 == NULL){

                /*elimina archiu*/
                utils::log("elimina", "logasd");
        //        unlink("../../../manuals/$data1");
            }else{
                /*NO elimina archiu*/
                utils::log("NO elimina", "logasd");
            }
        }

        $CLD_CON->OpenRs("DELETE FROM `manualsItems` WHERE manual_id = $id");
        $CLD_CON->OpenRs("DELETE FROM `manualsBooths` WHERE manual_id = $id");
        $CLD_CON->OpenRs("DELETE FROM `manuals` WHERE id = $id");
    }
}else{
    $CLD_CON->OpenRs("select data FROM manualsItems WHERE manual_id = $id_manual");
    while ($CLD_CON->FetchArray()) {
        $data[] = $CLD_CON->GetArrayField("data"); 
    }

    foreach ($data as &$data1){
        $CLD_CON->OpenRs("select data from manualsItems WHERE data = '{$data1}' && manual_id != $id_manual");
        while ($CLD_CON->FetchArray()) {
            $data2 = $CLD_CON->GetArrayField("data"); 
        }
        if($data2 == NULL){

            /*elimina archiu*/
            utils::log("elimina", "logasd");
    //        unlink("../../../manuals/$data1");
        }else{
            /*NO elimina archiu*/
            utils::log("NO elimina", "logasd");
        }
    }

    $CLD_CON->OpenRs("DELETE FROM `manualsItems` WHERE manual_id = $id_manual");
    $CLD_CON->OpenRs("DELETE FROM `manualsBooths` WHERE manual_id = $id_manual");
    $CLD_CON->OpenRs("DELETE FROM `manuals` WHERE id = $id_manual");
}


echo json_encode($array_result);



