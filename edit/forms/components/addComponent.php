<?php
$title = "New Component";
$content = "";
$buttons = "";
$content .= "<p> Type :";
$content .= "<select id='cmpType' class='popup-input-large'>";
$content .= "<option value='0'>-- No selected Component --</option>";
$CLD_CON->OpenRs("SELECT id, descripcio FROM CLD_typeComponents");
while ($CLD_CON->FetchArray()) {
    $idComponent = $CLD_CON->GetArrayField("id");
    $descripcio = $CLD_CON->GetArrayField("descripcio");
    $content .= "<option value='$idComponent'>$descripcio</option>";
}
$content .= "</select></p>";
$content .= "<div class='popup-text popup-margin-top'>";
    $content .= "<div class='popup-text'>";
    $content .= "Method:<br/>"
        . "<input type='radio' name='method' value='onlyOne'> Typing SN"
        . "<input type='radio' name='method' value='withFile'> Text file by SN";
    $content .= "</div>";
    $content .= "<div class='popup-text popup-margin-top' style='display:none;' id='onlyOne'>";
        $content .= "Type Here the SN : <input type='text' class='popup-input-large' style='width:100%;' id='serialnumber1234'>";
        $content .= "<input type='button' class='miniAdd' onclick='addComponent()'>";
        $content .= "<p id='textAdded'></p>";
    $content .= "</div>";
    $content .= "<div class='popup-text popup-margin-top' style='display:none;' id='withFile'>";
        $content .= "Selecciona un fitxer de text em tots els SN cada un en una linia nova del document. Tenen de ser sn del mateix tipus de component.";
        $content .= "<div class='popupInputLarge'>";
            $content .= "<form id='compForm' action='addNew/components/withFile.php' enctype='multipart/form-data'>";
            $content .= "<input class='popup-input-large' type='file' name='textFile' id='compFile' accept='.txt'>";
            $content .= "<input type='hidden' name='type' id='hidType' value='0'>";
            $content .= "</form>";
        $content .= "</div>";
        $content .= "<div id='textAdded2' style='width:80%;margin-left:10%;height:30%;border:1px solid gray;overflow:auto;margin-bottom:20px;'></div>";
    $content .= "</div>";
$content .= "</div>";

$content .= <<<HTML
<script>
    $(document).ready(function() {
        $("*[type='radio']").change(function() {
            var method = $(this).attr("value");
            $("#onlyOne").hide();
            $("#withFile").hide();
            $("#" + method).fadeIn(500);
        });

        $("#compFile").on("change", function() {
            uploadFile();
        });
        
        $("#cmpType").on("change" , function(){
            $("#hidType").val($("#cmpType").val());
        });
        
    });

    function addComponent() {
        var sn = $("#serialnumber1234").val();
        if (sn.length > 0) {
            var cmp = $("#cmpType").val();
            if (cmp != 0) {
                var cName = $("#cmpType option:selected").text();

                var ajaxData = {sn: sn, typeCmp: cmp, cName: cName};
                $.ajax({
                    url: 'addNew/components/addOnlyOne.php',
                    type: 'POST',
                    success: function(data) {
                        $("#textAdded").html(data);
                    },
                    error: function() {

                    },
                    // Form data
                    cache: false,
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });

            } else {
                alert("Select the component type please.");
            }
        } else {
            alert("The serialnumber textbox is empty");
        }

    }

    function uploadFile() {
        if ($("#compFile").val() === "") {
        } else {
            if($("#hidType").val()===0){
             alert("Please select a component type.");
            }else{
            
            $("#compForm").ajaxForm({
                success: function(e) {
                    if (e === "ERROR") {
                        alert("Error");
                    } else {
                        $("#textAdded2").html(e);
                        $("#sv").show();
                        
                    }
                },
                error: function(e) {

                }
            });
            $("#compForm").submit();
            
        }
        }
    }
    function createSN(){
         var cmp = $("#cmpType").val();
            if (cmp !== 0) {
                var ajaxData = {type: cmp};
                $.ajax({
                    url: 'addNew/components/addWithFile.php',
                    type: 'POST',
                    success: function() {
                        closePopup();
                        hidePopupv2();
                        setSection("components", 1);
                    },
                    error: function() {

                    },
                    // Form data
                    cache: false,
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });

            } else {
                alert("Select the component type please.");
            }
    }
</script>
HTML;

$buttons .= "<input type='button' class='popup-confirm' id='sv' value='Save' onclick='createSN();' >";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);