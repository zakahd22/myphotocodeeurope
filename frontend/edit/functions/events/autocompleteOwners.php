<?php
    include '../../../sessio.php';
    require_once G_PATH . 'common/conexio.php';


    $ownerId = $_POST['Owner'];
    
    if($ownerId !== ""){
//        echo json_encode(array(array(value => 1, label => 'almendrado'), array(value => 2, label => 'aleix')));
        
        $CLD_CON->OpenRs("SELECT id , name FROM rentals WHERE name LIKE '{$ownerId}%' ORDER BY name");
        $result = array();
        $i = 0;
        while ($CLD_CON->FetchArray()) {
            $result[$i]['value'] = $CLD_CON->GetArrayField("id");
            $result[$i]['label'] = $CLD_CON->GetArrayField("name") . " (id: {$result[$i]['value']})";
            $i++;
        }
        
        echo json_encode($result);
    }
    
?>