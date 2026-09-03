<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT rental_id FROM events WHERE id=$ID");
if ($CLD_CON->FetchArray()) {
    $ownerID = $CLD_CON->GetArrayField("rental_id");
}
/* echo "<div style='width:100%;'>";
  echo "<h1>Titol</h1>";
  echo "<p>Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio Explicacio ExplicacioExplicacioExplicacio</p>";
  echo "</div>"; */
echo "<input type='hidden' id ='SELECTEDBOOTH' val='0'>";
echo "<div style='width:100%;padding-top:20px;margin-top:-2%; background-color:black; color: white;padding-bottom: 10px;'>";
$CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$ownerID GROUP BY CLD_idType");
$boothsTypes = "0";
while ($CLD_CON->FetchArray()) {
    if(!empty($CLD_CON->GetArrayField("CLD_idType"))){
    $boothsTypes .= "," . $CLD_CON->GetArrayField("CLD_idType");
    }
}

if ($boothsTypes == "0") {
    $where = "WHERE b.char != '-'";
} else {
    $where = "WHERE b.char != '-' AND b.id IN($boothsTypes)";
}
if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
    $CLD_CON->OpenRs("SELECT b.name , b.id , b.char  FROM CLD_boothTypes b $where");
    echo "<h1> PhotoBooth Screen Customization</h1>";
    echo "<p> Choose the PhotoBooth model and click on the green button to start customizing it.</p>";
    echo "<p>Your PhotoBooth models : <select id='types' class='selectText' style='font-size:10pt;'><p>";
    echo "<option value=0>-------------</option>";
    while ($CLD_CON->FetchArray()) {
        $id_type = $CLD_CON->GetArrayField("char");
        $_type = $CLD_CON->GetArrayField("id");
        $nom = $CLD_CON->GetArrayField("name");
        echo "<option value='$id_type##$_type'>$nom</option>";
    }
    echo "</select><input type='button' class='miniAdd' onclick='newUSB($ID , $ownerID)'></p>";
}
echo "</div>";
echo "<div style='width:100%;margin-top:10px;'>";
echo "<div class='leftBar' style='width:100%;height:120px;overflow:auto;'>";
$CLD_CON->OpenRs("SELECT id , creation_date , boothtype_char , CLD_idTypeBooth FROM usbs WHERE event_id=$ID");
$ii = 1;
while ($CLD_CON->FetchArray()) {
    $usbId = $CLD_CON->GetArrayField("id");
    $fld = $CLD_CON->GetArrayField("creation_date") . $CLD_CON->GetArrayField("id");
    $boothChar = $CLD_CON->GetArrayField("boothtype_char");
    $idType = $CLD_CON->GetArrayField("CLD_idTypeBooth");


    echo "<div style='width:210px;float:left;position:relative;display:inline;border-right:3px solid black;padding-right:8px; padding-left:8px;' id='usb$ii' class='USBBB'>";
    echo "<div style='width:100%;float:left;'>";
    if (empty($idType)) {
        echo "<img src='images/web/pb/$boothChar.png' style='height: 60%;'>";
    } else {
        echo "<img src='images/web/pb/$idType.png' style='height:60%;'>";
    }
    echo "</div>";
    echo "<p><select id='setString$usbId' onchange='canviaApartat($ID , \"$boothChar\" , $fld , this , $ii);' class='selectText' style='font-size:10pt;width: 185px;'>";
    echo "<option value='0'> ---- New Screen ---</option>";
    echo "<option value='1'> ---- Welcome Screen ---</option>";
    echo "<option value='2'> ---- Goodbye Screen ---</option>";
    echo "<option value='3'> ---- Custom Shots ---</option>";
    echo "<option value='4'> ---- Background Music ---</option>";
    if ($boothChar == 'A') {
        echo "<option value='5'> ---- Header Banner --- </option>";
    }
    echo "</select></p>";
    if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
        // echo "<input type='button' class='miniDownload' onclick='downloadZIP($usbId , $fld , $ID);' style='position:absolute;top:22%;'>";
        echo "<input type='button' class='miniTrash' onclick='deleteUSB($usbId , $fld , $ID);' style='position:absolute;top:5px;right: 15px;'> ";
    }
    if ($boothChar == "C") {
        echo "<span style='background-color:black;color:white;padding: 12px 16px;border-radius:100px;position: absolute;top: 0px;left: 41%;'>IN</span>";
    }
    if ($boothChar == "D") {
        echo "<span style='background-color:black;color:white;padding:12px 10px;border-radius:100px;position: absolute;top: 0px;left: 41%;'>OUT</span>";
    }

    echo "</div>";
    $ii++;
}
echo "</div>";
echo "</div>";
echo "<div style='width:100%;background-color:black;color:white;height:3px;'>";
echo "</div>";

echo "<div id='contentUSB' style='min-height:80%;width:100%;'>";
echo "<h1> PhotoBooth Screen Customization</h1>";
echo "<div style='display:inline;float:left;width:30%;margin-left:4%;'>";
echo "<h3>Step1 - Select model PhotoBooth and Add</h3>";
echo "<img src='images/web/step1_screen.jpg' style='width:90%;margin-left:5%;margin-top:20px;'>";
echo "</div>";

echo "<div style='display:inline;float:left;width:30%;border-left:2px solid black;border-right:2px solid black;'>";
echo "<h3>Step2 - Select section to customize</h3>";
echo "<img src='images/web/step2_screen.jpg' style='width:90%;margin-left:5%;margin-top:20px;'>";
echo "</div>";

echo "<div style='display:inline;float:left;width:30%;'>";
echo "<h3>Step3 - Add&Delete files in the selected section</h3>";
echo "<img src='images/web/step3_screen.jpg' style='width:90%;margin-left:5%;margin-top:20px;'>";
echo "</div>";
/*echo "<h1>Edit the Photobooth</h1>";
echo "<p >A la columna de l'esquerra es troben els models de PhotoBooths que vindran al event. En cada un d'ells es poden fer tres accions: </p>";
echo "<div style='width:25%;display:inline;float:left;margin-top:20px;margin-left:5%;'>";
echo "<img src='images/web/example.jpg' style='width:100%;'>";
echo "</div><div style='width:65%;display:inline;float:left;margin-top:20px;'>";
echo "<ol style='font-size:13pt;'><li style='margin-top:3px;'>  Descarragar el arxiu comprimit (USB Stick)</li>";
echo "<li style='margin-top:3px;'> Borrarlo , juntament em tots els seus fitxers</li>";
echo "<li style='margin-top:3px;'> Seleccionem un dels apartats  Welcomes , Byes , Background Music i Customs Shots. Un cop seleccionat en aquesta zona apareixeran els fitxers anteriorment afegits i en podrem afegir de nous</li>";
echo "</ol>";*/
echo "</div>";

echo "</div>";
?>

<script>
    var c1 = "";
    $(document).ready(function() {
        c1 = $("#contentUSB").html();

    });


    function newUSB(id, owner) {
        var type = $("#types").val();
        if (type != 0) {
            var ajaxData = {id: id, type: type, owner: owner};
            $.ajax({
                url: 'sections/events/functions/newUSB.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    if (data === "OK") {
                        profile("events", "photobooths", id);
                    }
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });


        }
    }
    function canviaApartat(id, booth, folder, sel, a) {
    $("#SELECTEDBOOTH").val(a);
        $(".USBBB").css("background-color", "transparent");
        $("#usb" + a).css("background-color", "#FFDB58");
        var apartat = sel.value;
        var ajaxData = {id: id, booth: booth, folder: folder, apartat: apartat , bb: a};
        $.ajax({
            url: 'sections/events/profile/pb/photoboothsSections.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                $("#contentUSB").html(data);
                $(".contingut").animate({scrollTop: $(".contingut").height()}, 1000);

                if (data == "") {
                    $("#contentUSB").html(c1);
                    $(".USBBB").css("background-color", "transparent");
                    $(".contingut").animate({scrollTop: 0}, 1000);

                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });


    }

    function canviaApartat2(id, booth, folder, apartat ,a) {
        $("#SELECTEDBOOTH").val(a);
        $(".USBBB").css("background-color", "transparent");
        $("#usb" + a).css("background-color", "#FFDB58");
        var ajaxData = {id: id, booth: booth, folder: folder, apartat: apartat, bb:a};
        $.ajax({
            url: 'sections/events/profile/pb/photoboothsSections.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                $("#contentUSB").html(data);
                $(".contingut").animate({scrollTop: 0}, 1000);
            },
                    
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });


    }

</script>