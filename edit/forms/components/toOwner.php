<?php
if ($_SESSION['USERTYPE'] == 1) {
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals ORDER BY name ";
}
if ($_SESSION['USERTYPE'] == 2) {
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals ORDER BY name";
}
if ($_SESSION['USERTYPE'] == 6) {
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals ORDER BY name";
}
if ($_SESSION['USERTYPE'] == 3) {
    $disID = $_SESSION['USERID']; 
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals WHERE CLD_DistributorId = $disId ORDER BY name";
}
$CLD_CON->OpenRs("SELECT * FROM CLD_components WHERE serialnumber='$ID'");
if ($CLD_CON->FetchArray()) {
    $ownerID2 = $CLD_CON->GetArrayField("owner");
}
$distributors = array('DC', 'DCA', 'MATT');
$title = "Change owner for $ID ";

$content = "";
$buttons = "";
 
$content .= "<div class='popup-col' style='width: 650px; height:300px; overflow:auto; border:1px solid gray;' id='existingOwners'>";
//$content .= "<div style='width:90%;height:40%;margin-left:5%;margin-top:3%;overflow:auto;border:1px solid gray;'>";
$CLD_CON->OpenRs($consulta);
while ($CLD_CON->FetchArray()) {
    $ownerID = $CLD_CON->GetArrayField("id");
    $companyName = $CLD_CON->GetArrayField("name");
    $distributor = $CLD_CON->GetArrayField("CLD_DistributorId");
    $ownerEmail = $CLD_CON->GetArrayField("App_email");
//    $content .= "<p style='border:1px solid gray;line-height:80px;font-size:12pt;'>";
//    $content .= "<input type='radio' value='$ownerID' name='ownerID' style='margin-right:10px;margin-left:10px;display:inline;float:left;height:80px;width:30px;'>";
//    $content .= "<input type='hidden' value='$companyName' id='o$ownerID'>";
//    if (file_exists("../images/ownerIMG/$ownerID.jpg")) {
//        $content .= "<img src='images/ownerIMG/$ownerID.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
//    } else {
//        $content .= "<img src='images/ownerIMG/noPimg.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
//    }
//    if (empty($ownerEmail)) {
//        $content .= $companyName . " (No Contact Email - email No recivied)";
//    } else {
//        $content .= $companyName . " (ContactEmail : $ownerEmail)";
//    }
//    if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6 ) {
//        $dName = $distributors[$distributor];
//        $content .= "<span style='float:right;margin-right:10px;'>$dName</span>";
//    }
//    $content .= "</p>";
    
    $content .= "<div class='popup-row list-items'>";
        $content .= "<div id='listGroup' class='popup-row'>";
            $content .= "<div id='selectorOwner' class='popup-col'>";
                $content .= "<input type='radio' value='$ownerID' name='ownerID' style='margin-right:10px;margin-left:10px;display:inline;float:left;height:80px;width:30px;'>";
                $content .= "<input type='hidden' value='$companyName' id='o$ownerID'>";
            $content .= "</div>";
            $content .= "<div id='contentGroup' class='popup-row'>";
                $content .= "<div id='imageOwner' class='popup-col'>";
                    if (file_exists("../images/ownerIMG/$ownerID.jpg")) {
                        $content .= "<img src='images/ownerIMG/$ownerID.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
                    } else {
                        $content .= "<img src='images/ownerIMG/noPimg.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
                    }
                $content .= "</div>";
                $content .= "<div id='OwnerInfo' class='popup-col'>";
                    if (empty($ownerEmail)) {
                        $content .= $companyName . " (No Contact Email - email No recivied)";
                    } else {
                        $content .= $companyName . " (ContactEmail : $ownerEmail)";
                    }
                $content .= "</div>";
            $content .= "</div>";
        $content .= "</div>";
        $content .= "<div id='distributorOwner' class='popup-col'>";
            if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6 ) {
                $dName = $distributors[$distributor];
                $content .= "<span style='float:right;margin-right:10px;'>$dName</span>";
            }
        $content .= "</div>";
    $content .= "</div>";
    
}
$content .= "</div>";
if(!empty($ownerID2)){
 $content .= "<p style='border:1px solid gray;line-height:80px;font-size:12pt;width:84%;margin-left:7%;'>";
    $content .= "<input type='radio' value='0' name='ownerID' style='margin-right:10px;margin-left:10px;display:inline;float:left;height:80px;width:30px;'>";
    $content .= "<input type='hidden' value='Returned - No Owner' id='o0'>";
    $content .= "<img src='images/ownerIMG/noPimg.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
    $content .= "Returned - No Owner";
  $content .= "</p>";
}
$content .= "<p id='selOwner' style='margin-top:20px;text-align:center;'>-No selected owner-</p>";

$content .= <<<HTML
<script>
    $(document).ready(function() {
        $('input[name=ownerID]').change(function() {
            var v = $('input:radio[name=ownerID]:checked').val();
            var sel = $("#o" + v).val();
            $("#selOwner").html("Selected Owner : " + sel);
        });
    });

    function assignBooth(id) {
        var ownerId = $('input:radio[name=ownerID]:checked').val();
        var cName = $("#o" + ownerId).val();
        if ($("#selOwner").html() != "-No selected owner-") {
           if (confirm("Would do yo like assign the component "+ id +" to " + cName + "?")) {
              

                var ajaxData = {owner: ownerId, id: id };
                $.ajax({
                    url: 'edit/functions/components/setOwner.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data) {
                        if (data.search("ERROR") > -1) {
                            alert(data);
                        } else {
                            alert(data);
                            closePopup();
                            profile("components", "Info", id);
  
                        }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }
        } else {
            alert("Please select a Owner.");
        }
    }
</script>
HTML;

$buttons .= "<input type='button' class='popup-confirm' value='Save'  onclick='assignBooth(\"$ID\"); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);