<?php
$XX = true;
$CLD_CON2 = clone($CLD_CON);

$baseController->createModel('events');

$event = $baseController->eventsModel->getEvent($ID);

$rnd = rand(200000, 9999999);
if($event){
    $owner = $event[0]["rental_id"];
    $dataEvent = $event[0]['start_date'];
    $banner = $event[0]["CLD_banner"];
    $URL_A = $event[0]["CLD_banner_URL"];

    $date2 = substr($dataEvent, 0, 4) . "-" . substr($dataEvent, 4, 2) . "-" . substr($dataEvent, 6, 2);

    $title = "Banner";
    $content = "";
    $buttons = "";
   
    if ($banner == 1) {
        $fld = $dataEvent . $ID;
        $banner_found = false;
        $banner_path = G_PATH . "events/{$fld}/banner";
        
        // Array of possible extensions to check (lowercase only)
        $extensions = ['jpg', 'jpeg', 'gif'];
        
        // Loop through possible extensions
        foreach ($extensions as $ext) {
            // Check lowercase version
            if (file_exists($banner_path . '.' . $ext)) {
                $banner_IMG = "<img src='events/{$fld}/banner.{$ext}?version={$rnd}' style='width:200px;height:auto;'>";
                $banner_found = true;
                break;
            }
            
            // Check uppercase version
            $upper_ext = strtoupper($ext);
            if (file_exists($banner_path . '.' . $upper_ext)) {
                $banner_IMG = "<img src='events/{$fld}/banner.{$upper_ext}?version={$rnd}' style='width:200px;height:auto;'>";
                $banner_found = true;
                break;
            }
        }
        
        // If no banner files found, display the default banner
        if (!$banner_found) {
            $banner_IMG = "<img src='images/web/banners/banner-default.gif?version={$rnd}' style='width:200px;height:auto;'>";
        }
    }
    elseif ($banner == 0) {

        $CLD_CON2->OpenRs("SELECT b.banner , b.banner_url FROM CLD_banners b RIGHT JOIN CLD_timesBanners bt ON bt.id_banner= b.id WHERE b.rental_id=$owner AND (('$date2' BETWEEN start_date AND end_date AND end_date IS NOT NULL)  OR ('$date2' BETWEEN start_date AND '3000-01-01' AND end_date IS NULL))");
        
        if ($CLD_CON2->GetRsRows() == 0) {
            $URL_A = "http://www.digital-centre.com";
            $banner_IMG = "<img src='images/web/banners/banner-default.gif?version=$rnd'>";
        } 
        else {
            $CLD_CON2->FetchArray();
            $URL_A = $CLD_CON2->GetArrayField("banner_url");
            $banner = $CLD_CON2->GetArrayField("banner");
            $banner_IMG = "<img src='images/web/banners/$banner'>";
        }
    } 
    else {
        $XX = false;
        $content .= "<div class='popup-row popup-text'>The banner is <span style='color:red;'> &nbsp;OFF</span>.</div>";
    }

    if ($XX) {        $content .= "<div class='popup-text'>The banner is <span style='color:green;'> &nbsp;ON</span>.</div>";
        $content .= "In order to fit with all the screens, the banner must be of 500x150px and maximum 2MB";
        //$content .= "<p><img src='images/web/preferenceOK.png' style='width:32px;height:32px;cursor:pointer;position:relative;top:4px;' title='Deactivate' onclick='OnOffBanner($ID , 2);'> The banner is <span style='color:green;'>ON</span>.  </p>";

        $content .= "<div class='popup-row popup-nowrap'>";
            $content .= "<div class='popup-col'>";
                $content .= "Current URL:";
            $content .= "</div>";
            $content .= "<div class='popup-col'>";
                $content .= "<input type='text' class='popup-input-large' style='margin-top:-3px' value='$URL_A' id='linkBN'>";
            $content .= "</div>";
        $content .= "</div>";

        $content .= "<div class='popup-row'>";
            $content .= "<div class='popup-col'>";
                $content .= "Current banner:";
            $content .= "</div>";
            $content .= "<div class='popup-col'>";
                $content .= "<a href='$URL_A' target='_blank'>$banner_IMG</a>";
            $content .= "</div>";
        $content .= "</div>";

        $content .= "<div class='popup-row popup-center'>";
        $content .= "<form id='bnForm' action='edit/functions/events/uploadbnnr.php' enctype='multipart/form-data'>";
        $content .= "<input type='file' name='imgFile' id='imgFile' accept='image/jpeg,image/gif'>";
        $content .= "<input type='hidden' value='$ID' name='id'>";
        $content .= "</form>";
        $content .= "<div class='upload-status' style='display:none;margin-top:5px;color:#666;font-size:12px;'></div>";
        $content .= "</div>";

        $content .= "<div class='popup-row popup-center'>";
        $content .= "<p style='margin-bottom:10px;'>New Banner</p>";
        $content .= "<div class='preview' style='width:200px;'></div>";
        $content .= "</div>";
        
        $content .= "<input type='hidden' id='urlBN' value=''>";
        
        $buttons .= "<input type='button' class='popup-confirm' value='Turn OFF' onclick='OnOffBanner($ID, 2);' style=' background-color:#dd6b55;'>";
        $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveBanner($ID);'>";
        $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
    }else{
        $buttons .= "<input type='button' class='popup-confirm' value='Turn ON' onclick='OnOffBanner($ID , 0);'>";
        $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>"; 
    }
}
$content .= <<<HTML
<script>
$(document).ready(function() {
    $("#imgFile").on("change", function() {
        if ($("#imgFile").val() === "") {
            return;
        }
        
        // Client-side file size validation (2MB limit)
        var fileSize = this.files[0].size / 1024 / 1024; // size in MB
        if (fileSize > 2) {
            $(".preview").html("<div style='color:red;padding:10px;'>Error: File is too large.<br>Maximum size is 2MB. Your file is " + 
                fileSize.toFixed(2) + "MB.</div>");
            $(".upload-status").text("Error: File too large (max 2MB)");
            // Clear the file input
            $(this).val('');
            return;
        }
        
        // Show file size and upload indicator
        $(".preview").html("<div style='text-align:center;padding:20px;'>Uploading " + fileSize.toFixed(1) + "MB...<br><div class='progress-bar' style='width:100%;background:#eee;height:20px;'><div style='width:0%;background:#4CAF50;height:20px;transition:width 0.5s;'></div></div></div>");
        $(".upload-status").text("Starting upload...").show();
        
        var progressBar = $(".progress-bar div");
        var progressInterval = setInterval(function() {
            var width = parseInt(progressBar.width() / progressBar.parent().width() * 100);
            if (width < 90) {
                progressBar.css('width', (width + 5) + '%');
            }
        }, 500);
        
        // Use FormData for more reliable uploads
        var formData = new FormData(document.getElementById('bnForm'));
        
        $.ajax({
            url: 'edit/functions/events/uploadbnnr.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total * 100;
                        progressBar.css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                clearInterval(progressInterval);
                $(".upload-status").text("Upload complete!");
                
                console.log("Upload response:", response);
                
                if (response.indexOf("ERROR") === 0) {
                    $(".preview").html("<div style='color:red;padding:10px;'>Upload failed:<br>" + 
                        response.replace("ERROR:", "") + "</div>");
                    $(".upload-status").text("Error: " + response.replace("ERROR:", ""));
                } else {
                    var randomParam = Math.floor(Math.random() * 1000000 + 1);
                    $("#urlBN").val(response);
                    
                    // Fix URL construction
                    var imgUrl = 'images/ownerIMG/tmp/' + response;
                    
                    $(".preview").html("<img src='" + imgUrl + "' style='width:100%; height:auto;' onerror=\"this.onerror=null;this.src='images/web/banners/banner-default.gif';\">");
                    $(".preview").show(500);
                }
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                console.error("Upload error:", status, error);
                $(".preview").html("<div style='color:red;padding:10px;'>Upload failed:<br>" + error + "</div>");
                $(".upload-status").text("Error: " + error);
            }
        });
    });
});

function OnOffBanner(id, onoff) {
    var statusText = (onoff == 2) ? "OFF" : "ON";
    
    $(".upload-status").text("Turning banner " + statusText + "...").show();
    
    $.ajax({
        url: 'edit/functions/events/OnOffBanner.php',
        type: 'POST',
        data: {id: id, onoff: onoff},
        success: function(data) {
            $(".upload-status").hide();
            if (data === "OK") {
                edit(17, id);
                hidePopupv2();
                profile("events", "cloud", id);
            } else {
                swal('Error', data, 'error');
            }
        },
        error: function(xhr, status, error) {
            $(".upload-status").hide();
            swal('Error', 'Failed to update banner status: ' + error, 'error');
        }
    });
}

function saveBanner(id) {
    var bnIMG = $("#urlBN").val();
    var link = $("#linkBN").val();
    
    if (bnIMG === "" && link === "") {
        closePopup();
        profile("events", "cloud", id);
        return;
    }
    
    $(".upload-status").text("Saving banner...").show();
    
    $.ajax({
        url: 'edit/functions/events/saveBanner.php',
        type: 'POST',
        data: {id: id, bn: bnIMG, link: link},
        success: function(data) {
            $(".upload-status").hide();
            if (data === "OK") {
                closePopup();
                hidePopupv2();
                profile("events", "cloud", id);
            } else {
                swal('Error', data, 'error');
            }
        },
        error: function(xhr, status, error) {
            $(".upload-status").hide();
            swal('Error', 'Failed to save banner: ' + error, 'error');
        }
    });
}
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);