<?php

$baseController->createModel('rentals');

$rental = $baseController->rentalsModel->getRentalsById($ID);
$app_email = "";
if($rental){
    $app_email = $rental[0]["App_email"];
    $validate = $rental[0]["ValidatedAlertEmail"];
    $name = $rental[0]["name"];
}

$title = "Alerts Email";
$content = "";
$buttons = "";

$content .= "<div class='popup-text' style='max-width:600px;'>";
$content .= "Alerts e-mail is where MyPhotoCode will send all alerts, notifications and reports.<br/>";
if(empty($app_email)){
    $content .= "Your alerts e-mail is not defined , type the new one in the box below.";
}else{
    $content .= "Your alerts e-mail is <b>$app_email</b>.<br/>"
            . "If you want to change it, type the new one in the box below.";
    if($validate == 0){
        $content .= "<br><br><center><b style='color:red;'>This e-mail is not validated.</b></center>";
    }
}
$content .= "</div>";

$content .= "<input type='email' class='popupInputLarge popup-margin-top' id='alertEmail' VALUE='$app_email' required>";

$content .= "<div class='popup-text popup-margin-top' style='color:red;' id='popupError'></div>";


if($validate == 0){
    $buttons .= "<input type='button' class='popup-confirm' value='Resend' onClick='reSendOwnerAlertEmail($ID); hidePopupv2();'>";
}

$buttons .= "<input type='button' class='popup-confirm' value='Save' onClick='setOwnerAlertEmail($ID);'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$msgTest = "'";
if(G_TEST) $msgTest = "<br/>' + message";

$content .= <<<HTML
<script>
    var oldMail = "{$app_email}";
    var name = "{$name}";
    function setOwnerAlertEmail(id){
        $('#popupError').html("");
        var email = $("#alertEmail").val();
        if(oldMail != email){
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(regex.test(email) || email.length ==0){
                var ajaxData = {id: id , email : email, name : name};
                $.ajax({
                    url: 'edit/functions/owner/saveAlertEmail.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data) {
                      if(data.trim()==="OK"){
                          //closePopup();
                          hidePopupv2();
                          msgSweetAlert('success', "", id);
                      }else{
                        hidePopupv2();
                        msgSweetAlert('error', data.trim(), id);
                      }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }else{
                $('#popupError').html("The email is not correct");
            }
        }
        else {
            $('#popupError').html("The same email");
            hidePopupv2();
        }
    }
    
    function reSendOwnerAlertEmail(id){
        var ajaxData = {id: id , email : oldMail, name : name};
        $.ajax({
            url: 'edit/functions/owner/saveAlertEmail.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data.trim()==="OK"){
                  //closePopup();
                  hidePopupv2();
                 
                  msgSweetAlert('success', "", id);
              }else{
                    hidePopupv2();
                    msgSweetAlert('error', data.trim(), id);
              }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
    
    function msgSweetAlert(type, message, id){
        if(type === 'success'){
            sweetAlert(
                'Check your inbox',
                'It is important for you to validate this alert mail, please check your inbox',
                'success'
            ).then(function() {
                profile("owner" , "info" , id);
            });
        }
        else if(type === 'error'){
            sweetAlert(
                'Error ocurred',
                'Something went wrong! {$msgTest},
                'error'
            ).then(function() {
                profile("owner" , "info" , id);
            });
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);
