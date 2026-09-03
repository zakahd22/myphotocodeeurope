<?php

include '../../sessio.php';
require_once G_PATH . "common/Classes/baseController.php";

/*Delete*/
$CLD_CON2 = clone($CLD_CON);
/**/

$baseController = new baseController();
$baseController->createModel('App_booths');
$baseController->createModel('CLD_Incidents');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('CLD_Inc_coments');

if(isset($_POST['fil'])){
    $sn= $_POST['sn'];
    
    $booth = $baseController->App_boothsModel->getBooth($sn);
    //$CLD_CON->OpenRs("SELECT idBooth FROM App_booths WHERE serialnumber='$sn'");
    if($booth){
        $idBooth = $booth[0]["idBooth"];
        $filters = "$idBooth";
    }
    else{
        $filters="1=0";
    }
    $incidents = $baseController->CLD_IncidentsModel->getAllIncident($filters);
    //$CLD_CON->OpenRs("SELECT * FROM CLD_Incidents WHERE $filters ORDER BY status , datetime DESC");
}
else{
    $incidents = $baseController->CLD_IncidentsModel->getAllIncident();
}

foreach ($incidents as $incident){
    $id        = $incident["id"];
    $idBooth   = $incident["idBooth"];
    $coment    = $incident["coment"];
    $datetime1 = $incident["datetime"];
    $code      = $incident["code"];
    $user1     = $incident["user"];
    $status    = $incident["status"];
    
    $datetime1 = date("F d, Y | H:i:s", strtotime($datetime1));
    $style = "incidencia incTipo$status";
    
    $booth = $baseController->App_boothsModel->getBoothWhereid($idBooth);

    if($booth){
            $idType    = $booth[0]["CLD_idType"];
            $boothChar = $booth[0]["type"];
            $sn        = $booth[0]["serialnumber"];
            
            $where = TRUE;
            if(!empty($idType)){
                $where = FALSE;
            }
    }
    
    $boothtype = $baseController->CLD_boothTypesModel->getBoothTypesModelsIncidents($where, $idType, $boothChar);
    if($boothtype){
        $sn_string = ($sn == null)? 'Any Serialnumber' : $sn;
        $boothName = $boothtype[0]['name'] . ' - ' . $sn_string . ' ';
    }
    
    $html .= "<div class='$style'>";
    $html .= "<p  style='border-bottom:1px solid white;'> $code | $datetime1 <span style='float:right;margin-right:10px;'>$user1</span></p>";
    $html .= "<p> $coment</p>";
    $html .= "<div class='comentsInc'  id='com$id'>";
    
    $coment_ = $baseController->CLD_Inc_comentsModel->Inc_coment($id);
    
    if($coment_){
        $coment2 = $coment_[0]["coment"];
        $datetime2 = $coment_[0]["datetime"];
        $datetime2 = date("F d, Y | H:i:s", strtotime($datetime2));
        $user2 = $coment_[0]["user"];
        
        $html .= "<div class='inComents'";
        $html .= "<p>$datetime2 <span style='float:right;margin-right:10px;'>$user2</span></p>";
        $html .= "<p>$coment2</p>";
        $html .= "</div>";
    }
    else{
        $html .= "<p>No Comments</p>";
    }
    
    $html .= "</div>";
    $html .= "<p style='border-top:1px solid white;margin-bottom:0px;height:36px;'>-<span class='link' onclick='openLink(\"PhotoBooths\" , $idBooth);'>$boothName</span>- <span style='float:right;margin-right:10px;'>";

    if ($status == 0) {
        $html .= "<input type='button' style='margin-right: 5px;position: relative;top: 7px;' class='miniEye' title='SET TO LOOKED' onclick='setToLooked($id , 1);'>";
    }
    if ($status == 1) {
        $html .= "<input type='button' style='margin-right: 5px;position: relative;top: 7px;' class='miniHANDOK' title='SET TO SOLVED' onclick='setToLooked($id , 2);'>";
    }
    if ($status == 2) {
        $html .= "<img src='images/web/ok.png' style='margin-right: 5px;position: relative;top: 7px;' title='SOLVED'>";
    }

    $html .= "<input type='button' style='position: relative;top: 7px;margin-right: 5px;' class='miniComents' onclick='openCloseComents(\"com$id\")' title='LOOK/UNLOOK COMMENTS'>";
    $html .= "</span></p>";
    $html .= "</div>";
}

echo $html;

?>
<script>
    function openCloseComents(d) {
        $("#" + d).slideToggle(1500);
    }
   
    function setToLooked(id , status){
        var tStat;
        if(status === 1){
            tStat = "looked";
        }
        if(status === 2){
            tStat = "solved";
        }

        var ajaxData = {id: id , status:status};
        swal({
            title: 'Are you sure?',
            text: "Would do you like set this incidence to "+tStat+"?",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then(function () {
            $.ajax({
                url: 'sections/incidents/functions/setIncidentStatus.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    if(data === "OK"){
                       setSection("incidents" , 1);
                    } else{
                       swal("error","Have been a error , please try again", "Error");
                    }
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        });
//        if (confirm("Would do you like set this incidence to "+tStat+"?")) {
//            var ajaxData = {id: id , status:status};
//            $.ajax({
//                url: 'sections/incidents/functions/setIncidentStatus.php',
//                type: 'POST',
//                //Ajax events
//                success: function(data) {
//                   if(data === "OK"){
//                       setSection("incidents" , 1);
//                   }else{
//                       swal("error","Have been a error , please try again", "Error");
//                   }
//                },
//                // Form data
//                data: ajaxData,
//                contentType: 'application/x-www-form-urlencoded'
//            });
//        }
    }
</script>