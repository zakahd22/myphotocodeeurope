<?php
$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber , CLD_Status FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");

    $status = $CLD_CON->GetArrayField("CLD_Status");
}

$title = "Photobooth $sn to Returned";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Explain why the photobooth is returned.";
$content .= "</div>";

$content .= "<textarea style='width:80%;margin-left:10%;height:170px;' id='coment' class='popupInputLarge' maxlength='200'></textarea>";
$content .= "<p style='width:80%;margin-left:10%;text-align:right;margin-top:0px;margin-bottom:0px; ' id='lng'>0/200</p>";

$content .= "<div class='popup-row' style='width:300px;'>";
    $content .= "<div class='popup-col popup-center' style='width:300px;'>";
        $content .= "<p><input type='checkbox' class='popupInputLarge' style='height:auto; width:auto; float:left;' id='Damaged'>";
        $content .= "Is Damaged</p>";
    $content .= "</div>";
$content .= "</div>";



// Add custom confirmation dialog HTML
$content .= "<div id='customConfirm' style='display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;'>";
$content .= "<div style='position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:5px; width:400px; text-align:center;'>";
$content .= "<h3 style='color:#d9534f; margin-top:0;'>Warning!</h3>";
$content .= "<p>Old booth data (alerts, audits, events, photos, etc) will be deleted, are you sure?</p>";
$content .= "<button onclick='confirmYes()' style='background:#d9534f; color:white; border:none; padding:8px 15px; margin:5px; cursor:pointer;'>Yes, delete data</button>";
$content .= "<button onclick='confirmNo()' style='background:#f0f0f0; border:none; padding:8px 15px; margin:5px; cursor:pointer;'>Cancel</button>";
$content .= "</div></div>";

$buttons .= "<button type='button' class='popup-confirm' onclick='toDamage($ID, $status);'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    // Store values globally
    var globalId, globalFrom, globalDamage, globalComment;
    
    function toDamage(id, from) {
        // Get values
        var damage = 0;
        if(document.getElementById("Damaged").checked) {
            damage = 1;
        }
        var coment = document.getElementById("coment").value;
        
        if(coment.length === 0) {
            alert("Comment is empty and is required");
            return false;
        }
        
        // Store values globally
        globalId = id;
        globalFrom = from;
        globalDamage = damage;
        globalComment = coment;
        
        // Show custom confirmation
        document.getElementById('customConfirm').style.display = 'block';
    }
    
    function confirmYes() {
        // Hide custom confirmation
        document.getElementById('customConfirm').style.display = 'none';
        
        // Hide the original popup
        hidePopupv2();
        
        // Show some loading indication
        document.body.style.cursor = 'wait';
        
        // Create and send the request manually
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'edit/functions/photobooths/toReturned.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            document.body.style.cursor = 'default';
            
            if (xhr.status === 200) {
                if (xhr.responseText === "OK") {
                    // Success
                    profile("photobooths", "info", globalId);
                } else {
                    // Error
                    alert("Error: " + xhr.responseText);
                }
            } else {
                alert("Error: " + xhr.status);
            }
        };
        
        // Prepare the data
        var data = 'id=' + encodeURIComponent(globalId) + 
                   '&coment=' + encodeURIComponent(globalComment) + 
                   '&from=' + encodeURIComponent(globalFrom) + 
                   '&damage=' + encodeURIComponent(globalDamage);
        
        // Send the request
        xhr.send(data);
    }
    
    function confirmNo() {
        // Just hide the custom confirmation
        document.getElementById('customConfirm').style.display = 'none';
    }
    
    // Simple character counter
    document.getElementById("coment").addEventListener("keyup", function() {
        var length = this.value.length;
        document.getElementById("lng").innerHTML = length + "/200";
    });
</script>
HTML;

$array_result = array('title' => $title, 'content' => $content, 'buttons' => $buttons);
echo json_encode($array_result);