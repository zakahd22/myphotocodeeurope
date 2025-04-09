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
  
       if (file_exists(G_PATH ."events/$fld/banner.jpg")) {
            $banner_IMG = "<img src='events/" . $dataEvent . $ID . "/banner.jpg?version=$rnd' style='width:200px; height:auto;'>";
        }
        if (file_exists(G_PATH . "/events/" . $dataEvent. $ID . "/banner.gif")) {
            $banner_IMG = "<img src='events/" . $dataEvent . $ID . "/banner.gif?version=$rnd' style='width:200px; height:auto;'>";
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
        $content .= "In order to fit with all the screens, the banner must be of 500x150px";
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
            //$content .= "<img src='images/web/flecha.png' style='width: 60%;height: 75px'>";
            $content .= "<form id='bnForm' action='edit/functions/events/uploadbnnr.php' enctype='multipart/form-data'>";
                $content .= "<input type='file' name='imgFile' id='imgFile'>";
                $content .= "<input type='hidden' value='$ID' name='id'>";
            $content .= "</form>";
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
HTML;
if ($XX) {
$content .= <<<HTML
        $(document).ready(function() {
            $("#imgFile").on("change", function() {
                if ($("#imgFile").val() === "") {
                } else {
                    $("#bnForm").ajaxForm({
                        beforeSend: function() {

                        },
                        success: function(e) {
                            if (e === "ERROR") {
                                alert("Error");
                            } else {
                                var x = Math.floor((Math.random() * 1000000) + 1);
                                $("#urlBN").val(e);
                                $(".preview").html("<img src='images/ownerIMG/tmp/" + e +"?version="+ x +"' style='width:100%; height:auto;' >");
                                $(".preview").show(500);
                            }
                        },
                        error: function(e) {

                        }
                    });
                    $("#bnForm").submit();

                }
            });
        });
HTML;
}
$content .= <<<HTML
    function OnOffBanner(id, onoff) {
        var ajaxData = {id: id, onoff: onoff};
        $.ajax({
            url: 'edit/functions/events/OnOffBanner.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
                    edit(17 , id);
                    hidePopupv2();
                    profile("events" , "cloud" , id);
                }
                else {
                    swal('Error', data, 'error');
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }

    function saveBanner(id) {
        var bnIMG = $("#urlBN").val();
        var link = $("#linkBN").val();
        
        if (bnIMG === "" &  link === "" ) {
            closePopup();
            profile("events", "cloud", id);
        } 
        else {
            var ajaxData = {id: id, bn: bnIMG , link : link};
            $.ajax({
                url: 'edit/functions/events/saveBanner.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    if (data === "OK") {
                        closePopup();
                        hidePopupv2();
                        profile("events", "cloud", id);
                    }
                    else {
                        swal('Error', data, 'error');
                    }
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);