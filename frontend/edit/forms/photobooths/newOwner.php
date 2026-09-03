<?php
$userType = $_SESSION['USERTYPE'];
$userID = $_SESSION['USERID'];
$title = "New Owner";

$baseController->createModel('CLD_Distributors');
$baseController->createModel('CLD_subDistributors');

$content = "";
$buttons = "";

$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col'>";
        $content .= "<div class='popup-row'>";
            $content .= "Company Name *";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<input type='text' class='popupInputLarge' id='companyName'>";
        $content .= "</div>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<div class='popup-row'>";
            $content .= "Contact Email";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<input type='text' class='popupInputLarge' id='contactEmail'>";
        $content .= "</div>";
    $content .= "</div>";
$content .= "</div>";
    
$content .= "<hr>";

switch ($userType) {
    case 1:
        
        $content .= "<div class='popup-row popup-margin-top'>";
            $content .= "<div class='popup-row'>";
                $content .= "Distributor *";
            $content .= "</div>";
            $content .= "<div class='popup-row'>";
                $content .= "<select class='popupInputLarge' id='distributor1234'>";
                $content .= "<option value='0'> ---- Select a Distributor ----</option>";
                
                $distributors = $baseController->CLD_DistributorsModel->getDistributors();
                
                foreach ($distributors as $distributor){
                    $id = $distributor["id"];
                    $name = $distributor["Name"];
                    $content .= "<option value='$id'> $name </option>";
                }
                
                $content .= "</select>";
            $content .= "</div>";
        $content .= "</div>";
        
        $content .= "<div class='popup-row popup-margin-top'>";
            $content .= "<div class='popup-row'>";
                $content .= "Sub-Distributor *";
            $content .= "</div>";
            $content .= "<div class='popup-row'>";
                $content .= "<select name='subdistributor' id='subdistributor' class='popupInputLarge'>";
                $content .= "<option value='0'>No sub-distributor</option>";
                
                $subDistributors = $baseController->CLD_subDistributorsModel->getCLD_SubDistributors();
                
                foreach ($subDistributors as $subDistributor){
                    $id = $subDistributor["id"];
                    $name = $subDistributor["name"];
                    $content .= "<option value='$id'> $Name </option>";
                }
                
                $content .= "</select>";
            $content .= "</div>";
        $content .= "</div>";

        break;
    case 2:
        $content .= "<div class='popup-row popup-margin-top'>";
            $content .= "<div class='popup-row'>";
                $content .= "Distributor *";
            $content .= "</div>";
            $content .= "<div class='popup-row'>";
                $content .= "<select class='popupInputLarge' id='distributor1234'>";
                $content .= "<option value='0'> ---- Select a Distributor ----</option>";
                
                $distributors = $baseController->CLD_DistributorsModel->getDistributors();
                
                foreach ($distributors as $distributor){
                    $id = $distributor["id"];
                    $name = $distributor["Name"];
                    $content .= "<option value='$id'> $name </option>";
                }
                
                $content .= "</select>";
            $content .= "</div>";
        $content .= "</div>";
        
        $content .= "<div class='popup-row popup-margin-top'>";
            $content .= "<div class='popup-row'>";
                $content .= "Sub-Distributor *";
            $content .= "</div>";
            $content .= "<div class='popup-row'>";
                $content .= "<select name='subdistributor' id='subdistributor' class='popupInputLarge'>";
                $content .= "<option value='0'>No sub-distributor</option>";
                
                $subDistributors = $baseController->CLD_subDistributorsModel->getCLD_SubDistributors();
                
                foreach ($subDistributors as $subDistributor){
                    $id = $subDistributor["id"];
                    $name = $subDistributor["name"];
                    $content .= "<option value='$id'> $Name </option>";
                }

                $content .= "</select>";
            $content .= "</div>";
        $content .= "</div>";
        break;
    case 3:
        if ($_SESSION['USERID'] < 3) {
            $content .= "<div class='popup-row popup-margin-top'>";
                $content .= "<div class='popup-row'>";
                    $content .= "<h2>Distributors Info</h2>";
                $content .= "</div>";
                $content .= "<div class='popup-row'>";
                    $content .= "Sub-Distributor *";
                $content .= "</div>";
                $content .= "<div class='popup-row'>";
                    $content .= "<select name='subdistributor' id='subdistributor' class='popupInputLarge'>";
                    $content .= "<option value='0'>No sub-distributor</option>";
                    
                    $subDistributors = $baseController->CLD_subDistributorsModel->getCLD_SubDistributors();
                
                    foreach ($subDistributors as $subDistributor){
                        $id = $subDistributor["id"];
                        $name = $subDistributor["name"];
                        $content .= "<option value='$id'> $Name </option>";
                    }

                    $content .= "</select>";
                $content .= "</div>";
            $content .= "</div>";
            
        } else {
            $content .= "<input type='hidden' name='subdistributor' id='subdistributor' value='0'>";
        }
        $content .= "<input type='hidden' name='distributor1234' id='distributor' value='$userID'>";
        break;
}

$buttons .= "<input type='button' id='saveBtn' class='popup-confirm' value='Save' onclick='saveOwner($ID);' style='height:auto;'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='edit(40 , $ID);' style='height:auto;'>";

$content .= <<<HTML
<script>
    function saveOwner(idBooth) {
//        $(this).unbind("click");
        $('#saveBtn').removeAttr("onclick");
        var contactEmail = $("#contactEmail").val();
        loadingPopup();
        if (contactEmail.length > 0) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!regex.test(contactEmail)) {
                alert("The email is not correct");
                unloadingPopup();
                $('#saveBtn').attr("onclick", 'saveOwner($ID)');
                return;
            }
        }
        var companyName = $("#companyName").val();
        if (companyName.length === 0) {
            alert("Company Name is required.");
            unloadingPopup();
            $('#saveBtn').attr("onclick", 'saveOwner($ID)');
            return;
        }
        var dis = $("#distributor1234").val();
        if (dis === "0") {
            alert("Select a distributor please");
            unloadingPopup();
            $('#saveBtn').attr("onclick", 'saveOwner($ID)');
            return;
        }

        var subDistributor = $("#subdistributor").val();
        var ajaxData = {idBooth: idBooth, subDis: subDistributor, companyName: companyName, email: contactEmail, distributor: dis};
        $.ajax({
            url: 'edit/functions/owner/newOwnerAndAssigBooth.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                unloadingPopUp();
                if (data.search("ERROR") > -1) {
                    unloadingPopup();
                    alert(data);
                } else {
//                    openLink("Owners", data);
//                    profile("owner", "addresses", data);
                    edit(43, data);
                }

            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);