<?php
$userType = $_SESSION['USERTYPE'];
$userID = $_SESSION['USERID'];

$baseController->createModel('App_booths');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('CLD_subDistributors');


$boothsModel = $baseController->App_boothsModel->getBoothWhereid($ID);
if($boothsModel){
    $snPhotobooth = $boothsModel[0]["serialnumber"];
    $idType = $boothsModel[0]["CLD_idType"];
}

$boothsType = $baseController->CLD_boothTypesModel->getBoothTypeName($idType);
if($boothsType){
    $type = $boothsType[0]["name"];
}

$title = "Select the owner for $type( SN: $snPhotobooth )";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "Search by Name:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='text' id='cName' class='popupInputLarge'>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='button' class='okB okButton' onclick='getExistingOwners();' style='margin-top:0px; top:-4px;'>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-col' style='width: 650px; height:300px; overflow:auto; border:1px solid gray;' id='existingOwners'></div>";
$content .= "<p id='selOwner' style='text-align:center;'>-No selected owner-</p>";
$content .= "<hr>";

//$content .= "<div style='width:90%;height:40%;margin-left:5%;margin-top:3%;overflow:auto;border:1px solid gray;' id='existingOwners'>";
//$content .= "</div>";
//$content .= "<p id='selOwner' style='margin-top:20px;text-align:center;'>-No selected owner-</p>";
//$content .= "<hr>";

switch ($userType) {
    case 1:
        $content .= "<div class='popup-row>";
            $content .= "<div class='popup-col'>";
                $content .= "Sub-Distributor";
            $content .= "</div>";
        $content .= "</div>";
        $content .= "<div class='popup-row>";
            $content .= "<div class='popup-col'>";
                $content .= "<select name='subdistributor' id='subdistributor' class='popupInputLarge'>";
                    $content .= "<option value='0'>No sub-distributor</option>";
                    
                    $subDistributors = $baseController->CLD_subDistributorsModel->getCLD_SubDistributors();
                    
                    foreach ($subDistributors as $subDistributor){
                        $idSub = $subDistributor["id"];
                        $nameSub = $subDistributor["name"];
                        $content .= "<option value='$idSub'>$nameSub</option>";
                    }
                $content .= "</select>";
            $content .= "</div>";
        $content .= "</div>";
        break;
    case 2:
        $content .= "<div class='popup-row>";
            $content .= "<div class='popup-col'>";
                $content .= "Sub-Distributor";
            $content .= "</div>";
        $content .= "</div>";
        $content .= "<div class='popup-row>";
            $content .= "<div class='popup-col'>";
                $content .= "<select name='subdistributor' id='subdistributor' class='popupInputLarge'>";
                    $content .= "<option value='0'>No sub-distributor</option>";
                    
                    $subDistributors = $baseController->CLD_subDistributorsModel->getCLD_SubDistributors();
                    
                    foreach ($subDistributors as $subDistributor){
                        $idSub = $subDistributor["id"];
                        $nameSub = $subDistributor["name"];
                        $content .= "<option value='$idSub'>$nameSub</option>";
                    }
                    
                $content .= "</select>";
            $content .= "</div>";
        $content .= "</div>";
        break;
    case 3:
        if ($_SESSION['USERID'] < 3) {
            $content .= "<div class='popup-row>";
                $content .= "<div class='popup-col'>";
                    $content .= "Sub-Distributor";
                $content .= "</div>";
            $content .= "</div>";
            $content .= "<div class='popup-row>";
                $content .= "<div class='popup-col'>";
                    $content .= "<select name='subdistributor' id='subdistributor' class='popupInputLarge'>";
                        $content .= "<option value='0'>No sub-distributor</option>";
                        
                        $subDistributors = $baseController->CLD_subDistributorsModel->getCLD_SubDistributors();
                    
                        foreach ($subDistributors as $subDistributor){
                            $idSub = $subDistributor["id"];
                            $nameSub = $subDistributor["name"];
                            $content .= "<option value='$idSub'>$nameSub</option>";
                        }
                        
                    $content .= "</select>";
                $content .= "</div>";
            $content .= "</div>";
        } else {
            $content .= "<input type='hidden' name='subdistributor' id='subdistributor' value='0'>";
        }
        $content .= "<input type='hidden' name='distributor' id='distributor' value='$userID'>";
        break;
}

$buttons = "";
$buttons .= "<button type='button' class='popup-confirm' value='' onclick='assignBooth($ID);'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' value='' onclick='edit(40 , $ID);'>Cancel</button>";

$content .=<<<HTML
<script>    
    $(document).ready(function() {
                getExistingOwners();

    });
    function getExistingOwners() {
         $("#selOwner").html("-No selected owner- ");
        var extO = $("#cName").val();
        var ajaxData;
        if (extO.length > 0) {
            ajaxData = {fil: 0, nameOwner: extO};
        } else {
            ajaxData = {nameOwner: extO};
        }

        $.ajax({
            url: 'edit/functions/owner/getExistingOwners.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                $("#existingOwners").html(data);
                $('input[name=ownerID]').change(function() {
            var v = $('input:radio[name=ownerID]:checked').val();
            var sel = $("#o" + v).val();
            $("#selOwner").html("Selected Owner : " + sel);

        });
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }

    function assignBooth(idBooth) {
        var ownerId = $('input:radio[name=ownerID]:checked').val();
        var cName = $("#o" + ownerId).val();
        loadingPopup();
        if (ownerId != null) {
            if (confirm("Would do yo like assign this PhotoBooth to " + cName + "?")) {
                var subDistributor = $("#subdistributor").val();
                var ajaxData = {owner: ownerId, idBooth: idBooth, subDis: subDistributor};
                $.ajax({
                    url: 'edit/functions/photobooths/assignBoothOldOwner.php',
                    type: 'POST',
                    //Ajax events
                    before: function(){loadPopUp();},
                    success: function(data) {
                         unloadingPopUp();
                        if (data.search("ERROR") > -1) {
                            unloadingPopup();
                            alert(data);
                        } else {
                            alert(data);
                            hidePopupv2();
                            closePopup();
                            profile("photobooths", "info", idBooth);
                        }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }
        } else {
            unloadingPopup();
            alert("Please select a Owner.");
        }
    }
</script>
HTML;
$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);