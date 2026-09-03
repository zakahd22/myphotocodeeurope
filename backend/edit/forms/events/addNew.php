<?php
$title = "Create new event";
$content = "";
$buttons = "";

if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6 ) {
    $content .= "<div class='popup-margin-top'>";
    $content .= "Owner : <input id='owner1234' class='popup-input-large' style='width:auto;'>"; // class='popup-input-large' style='width:80%;' 
    $content .= "<input id='ownerId' type='hidden' value=''>";
    $content .= "</div>";
//    $content .= "<p style='margin-left:3%;'> Owner : <select id='owner1234' class='popup-input-large' style='width:80%;' >";
//    $content .= "<option value='0'>--- No selected Owner ---</option>";
//    $CLD_CON->OpenRs("SELECT id , name FROM rentals ORDER BY name");
//    while ($CLD_CON->FetchArray()) {
//        $idOwner = $CLD_CON->GetArrayField("id");
//        $OwnerName = $CLD_CON->GetArrayField("name");
//        $content .= "<option value='$idOwner'>$OwnerName</option>";
//    }
//    $content .= "</select></p>";

}
else if ($_SESSION['USERTYPE'] == 4) {
    $idOwner = $_SESSION['USERID'];
    $content .= "<input type='hidden' id='ownerId' value='$idOwner'>";
}

$content .= "<p class='popup-margin-top' style='margin-left:3%;'> Title : <input type='text' id='title22' class='popup-input-large' style='width:80%;'></p>";
$content .= "<div class='popup-margin-top'>";
    $content .= "Date <br/>";
    $content .= '<div id="datepicker"></div>';
$content .= "</div>";
$content .= "<div class='popup-margin-top'>";
    $content .= "Private<input type='checkbox' id='private' checked>";
$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' value='Save'  onClick='addEvent(); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML
    <script>
        $(document).ready(function() {
            $("#datepicker").datepicker({
                dateFormat: 'yymmdd',
                minDate: 0
            });
        
            autocompleteOwner();
        });

        function autocompleteOwner(){
            var limit = 0;
//            var availableTags1 = [
//                {"value":"1","label":"ActionScript"},
//                {"value":"2","label":"AppleScript"},
//                {"value":"3","label":"Asp"}];
//            $("#owner1234").autocomplete({
//                source: availableTags1,
//                focus: function (event, ui) {
//                    event.preventDefault();
//                    $("#owner1234").val(ui.item.label);
//                },
//                select: function(event, ui) {
//                    event.preventDefault();
////                    $("#owner1234").val(ui.item.label);
//                }
//            });
        
            $("#owner1234").keyup(function() {
                var value = $("#owner1234").val();
                if(value !== "" && value.length > 0 && limit === 0){
                    limit = 1;               
                    $.ajax({
                        url: "edit/functions/events/autocompleteOwners.php",        
                        dataType: "json",
                        type: "POST",
                        data: {
                            Owner: value
                        },
                        success: function(data) {
                            //data = ["ActionScript","AppleScript","Asp"];
                            $("#owner1234").autocomplete({
                                source: data,
                                minlenght: 1,
                                autoFocus: true,
                                focus: function (event, ui) {
                                    event.preventDefault();
                                    $("#owner1234").val(ui.item.label);
                                    $('#ownerId').val(ui.item.value);
                                },
                                select: function(event, ui) {
                                    event.preventDefault();
//                                    $("#owner1234").val(ui.item.label);
                                },
                                open: function() {
                                  $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                                },
                                close: function() {
                                  $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                                }
                            });
                        }
                    });
                }
                else if(limit === 1 && value.length === 0){
                    limit = 0;
                }
            });
        }


        function addEvent() {
            var owner = $("#ownerId").val();
            //$("#owner1234").val();
            var title = $("#title22").val();
            var data = $("#datepicker").val();
            var private = 0;
            if ($("#private").is(":checked")) {
                private = 1;
            }

            if (owner === 0) {
                alert("Select a owner please");
                return;
            }
            if (title.length === 0) {
                alert("Type a event title please");
                return;
            }

            var ajaxData = {owner: owner, title: title, data: data, private: private};
            $.ajax({
                url: 'addNew/events/addNewEvent.php',
                type: 'POST',
                success: function(data) {
                    if (data === "OK") {
                        closePopup();
                        setSection("events", 1);
                    } else {
                        alert(data);
                    }
                },
                error: function() {

                },
                // Form data
                cache: false,
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
    </script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);