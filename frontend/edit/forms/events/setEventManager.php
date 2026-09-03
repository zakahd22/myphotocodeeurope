<?php
require_once G_PATH . "common/Classes/baseController.php";
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT * FROM events WHERE id = $ID");

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('CLD_EventsManegers');

$events = $baseController->eventsModel->getEvent($ID);
if($events){
    $invitedName = $events[0]["CLD_invitedName"];
    $invitedEmail = $events[0]["CLD_invitedEmail"];
    $securityCode= $events[0]["CLD_SecurityCode"];
    $eventManagerID= $events[0]["CLD_eventManegerId"];
    
    $title = "Event Manager";
    
    if(empty($invitedEmail)){
        $x=1;
        $content .= "<div class='popup-text'>";
            $content .= "<p>There are no invitations.<br />Type a name and an email of the person you want to invite.</p>";
        $content .= "</div>";
        
        $content .= "<div class='popup-row'>";    
            $content .= "<div class='popup-col'>";
                $content .= "Invited Name:";
            $content .= "</div>";
            $content .= "<div class='popup-col'>";
                $content .= "<input type='text' class='popup-input-large' id='inName' style='margin-top:0px'>";
            $content .= "</div>";
        $content .= "</div>";
        $content .= "<div class='popup-row  popup-margin-top'>";    
            $content .= "<div class='popup-col'>";
                $content .= "Invited E-mail:";
            $content .= "</div>";
            $content .= "<div class='popup-col'>";
                $content .= "<input type='text' class='popup-input-large' id='inEmail' style='margin-top:0px'>";
            $content .= "</div>";
        $content .= "</div>";
        
    }
    else{
        $content .= "<p>Invited Name : -$invitedName-</p>";
        $content .= "<p>Invited E-mail : -$invitedEmail-</p>";
        $content .= "<p>Security Code : -$securityCode-</p>";
        if(empty($eventManagerID)){
            $x=2;
            $content .= "<p>The event manager is not registered. If event manager not receive the invitation e-mail click on RESEND.</p>";
        }
        else{
            
//            $eventsManegers = $baseController->CLD_EventsManegersModel->getEventsManagers($eventManagerID);
            
//            utils::log($eventsManegers, "logPhortosORM");
            
            $eventManager = $baseController->CLD_EventsManegersModel->getCLD_EventsManegers($eventManagerID);
            if($eventManager){
               $manager = $eventManager[0]["name"] . " " . $eventManager[0]["surname"];
               $emailManager = $eventManager[0]["email"];
            }
            $content .= "<p> Registered Name: $manager</p>";
            $content .= "<p> Registered E-mail: $emailManager</p>";
            $x= 3;
        }
    }
    if($x==1){
        $buttons .= "<input type='button' class='popup-confirm' value='Send Invitation'  onclick='inviteManager($ID); hidePopupv2();'>";
        $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";
    }
    if($x==2){
        $buttons .= "<input type='button' class='popup-danger' value='Delete Event Manager'  onclick='delInviteManager($ID);'>";
        $buttons .= "<input type='button' class='popup-confirm' value='Resend Invitation'  onclick='reInviteManager($ID); hidePopupv2();'>";
        $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

    }
}

$content .= <<<HTML
<script> 
function inviteManager(id){
    var name = $("#inName").val(); 
    var email = $("#inEmail").val();
    var ajaxData = {inName : name , inEmail : email , id : id};
    loadingPopup();
    $.ajax({
            url: 'edit/functions/events/inviteEventManager.php',
            type: 'POST',
     
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("events", "info", id);
                } else {
                    unloadingPopup();              
                    alert(data);
                    profile("events", "info", id);
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
    });
    
    
}

function reInviteManager(id){
    var ajaxData = {id : id};
    loadingPopup();
    $.ajax({
            url: 'edit/functions/events/reInviteManager.php',
            type: 'POST',

            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("events", "info", id);
                } else {
                    unloadingPopup();
                    alert(data);
                    profile("events", "info", id);
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
    });
}
        
function delInviteManager(id){
    var ajaxData = {id : id};
    loadingPopup();
    $.ajax({
        url: 'edit/functions/events/delInviteManager.php',
        type: 'POST',

        success: function(data) {
            if (data === "OK") {
                hidePopupv2();
                profile("events", "info", id);
            } else {
                unloadingPopup();
                alert(data);
                profile("events", "info", id);
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
