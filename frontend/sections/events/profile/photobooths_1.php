<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT rental_id FROM events WHERE id=$ID");
if ($CLD_CON->FetchArray()) {
    $ownerID = $CLD_CON->GetArrayField("rental_id");
}
echo "<div class='leftBar' style='width:20%;border-right:3px solid gray;height:100%;overflow:auto;display:inline;float:left;padding-left:2%;'>";

/* echo "<h1>Photobooths in the Event:</h1>";
  $CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$ownerID GROUP BY CLD_idType");
  $boothsTypes= "0";
  while($CLD_CON->FetchArray()){
  $boothsTypes .= "," . $CLD_CON->GetArrayField("CLD_idType");

  }
  if($boothsTypes=="0"){
  $where = "WHERE b.char != '-'";
  }else{
  $where = "WHERE b.char != '-' AND b.id IN($boothsTypes)";
  }
  if($_SESSION['USERTYPE']<5 || $_SESSION['USERTYPE']==6){
  $CLD_CON->OpenRs("SELECT b.name , b.id , b.char FROM CLD_boothTypes b $where");
  echo "<p>Choose the PhotoBooth’s model thar you will be using at the event and edit it.<p>";
  echo "<p><select id='types' class='selectText' style='font-siz:10pt;'>";
  echo "<option value=0>-------------</option>";
  while($CLD_CON->FetchArray()){
  $id_type = $CLD_CON->GetArrayField("char");
  $nom = $CLD_CON->GetArrayField("name");
  echo "<option value='$id_type'>$nom</option>";
  }
  echo "</select><input type='button' class='okB okButton' onclick='newUSB($ID , $ownerID)'></p>";
  echo "<hr>";
  echo "<div style='width:100%;height:80%; border-bottom:1px solid black;overflow: auto;'> ";
  }else{
  echo "<div style='width:100%;height:50%; border-bottom:1px solid black;overflow: auto;'> ";
  } */
echo "<h2>Event PhotoBooths</h2><hr>";

if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {

    echo "<div style='width:100%;border-bottom:1px solid black;'> ";
} else {
    echo "<div style='width:100%;border-bottom:1px solid black;'> ";
}
$CLD_CON->OpenRs("SELECT id , creation_date , boothtype_char FROM usbs WHERE event_id=$ID");
while ($CLD_CON->FetchArray()) {
    $usbId = $CLD_CON->GetArrayField("id");
    $fld = $CLD_CON->GetArrayField("creation_date") . $CLD_CON->GetArrayField("id");
    $boothChar = $CLD_CON->GetArrayField("boothtype_char");
    echo "<div style='width:100%;border-bottom:1px solid gray;float:left;position:relative;'>";
    echo "<div style='width:95%;float:left;margin-left:2%;'>";
    echo "<img src='$URL/images/web/pb/$boothChar.png' style='width:80%;margin-left:10%;margin-right:8%;'>";
    echo "</div>";
    echo "<p>Select section to edit <select id='setString$usbId' onchange='canviaApartat($ID , \"$boothChar\" , $fld , this);' class='selectText' style='font-size:10pt;'>>";
    echo "<option value='0'> ---- None ---</option>";
    echo "<option value='1'> ---- Welcomes ---</option>";
    echo "<option value='2'> ---- Byes ---</option>";
    echo "<option value='3'> ---- Custom Shots ---</option>";
    echo "<option value='4'> ---- Background Music ---</option>";
    if ($boothChar == 'A') {
        echo "<option value='5'> ---- Header Banner --- </option>";
    }
    echo "</select></p>";
    if ($_SESSION['USERTYPE'] < 5 ) {
        echo "<input type='button' class='miniDownload' onclick='downloadZIP($usbId , $fld , $ID);' style='position:absolute;top:22%;'>";
        echo "<input type='button' class='miniTrash' onclick='deleteUSB($usbId , $fld , $ID);' style='position:absolute;top:39%;'> ";
    }
    echo "</div>";
}
echo "</div>";
echo "</div>";


echo "<div id='contentUSB' style='width:75%;display:inline;float:left;height:100%;'>";

echo "<h1>Photobooths in the Event:</h1>";
$CLD_CON->OpenRs("SELECT CLD_idType FROM App_booths WHERE owner=$ownerID GROUP BY CLD_idType");
$boothsTypes = "0";
while ($CLD_CON->FetchArray()) {
    $boothsTypes .= "," . $CLD_CON->GetArrayField("CLD_idType");
}
if ($boothsTypes == "0") {
    $where = "WHERE b.char != '-'";
} else {
    $where = "WHERE b.char != '-' AND b.id IN($boothsTypes)";
}
if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
    $CLD_CON->OpenRs("SELECT b.name , b.id , b.char FROM CLD_boothTypes b $where");
    echo "<p>Choose the PhotoBooth model that you will be using at the event and click OK.<p>";    
    echo "<p><select id='types' class='selectText' style='font-size:10pt;'>";
    echo "<option value=0>-------------</option>";
    while ($CLD_CON->FetchArray()) {
        $id_type = $CLD_CON->GetArrayField("char");
        $nom = $CLD_CON->GetArrayField("name");
        echo "<option value='$id_type'>$nom</option>";
    }
    echo "</select><input type='button' class='okB okButton' onclick='newUSB($ID , $ownerID)'></p>";
    echo "<hr>";   
} 

echo "<h1>Edit the Photobooth</h1>";
echo "<p>A la columna de l'esquerra es troben els models de PhotoBooths que vindran al event. En cada un d'ells es poden fer tres accions: </p>";
echo "<div style='width:25%;display:inline;float:left;margin-top:20px;margin-left:5%;'>";
echo "<img src='images/web/example.jpg' style='width:100%;'>";
echo "</div><div style='width:65%;display:inline;float:left;margin-top:20px;'>";
echo "<ol style='font-size:13pt;'><li style='margin-top:3px;'>  Descarragar el arxiu comprimit (USB Stick)</li>";
echo "<li style='margin-top:3px;'> Borrarlo , juntament em tots els seus fitxers</li>";
echo "<li style='margin-top:3px;'> Seleccionem un dels apartats  Welcomes , Byes , Background Music i Customs Shots. Un cop seleccionat en aquesta zona apareixeran els fitxers anteriorment afegits i en podrem afegir de nous</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
?>

<script>
    var c1 = "";
    $(document).ready(function(){
            c1= $("#contentUSB").html();
        
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
    function canviaApartat(id, booth, folder, sel) {

        var apartat = sel.value;
        var ajaxData = {id: id, booth: booth, folder: folder, apartat: apartat};
        $.ajax({
            url: 'sections/events/profile/pb/photoboothsSections.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                $("#contentUSB").html(data);
                if(data == ""){
                     $("#contentUSB").html(c1);
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });


    }

    function canviaApartat2(id, booth, folder, apartat) {

        var ajaxData = {id: id, booth: booth, folder: folder, apartat: apartat};
        $.ajax({
            url: 'sections/events/profile/pb/photoboothsSections.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                $("#contentUSB").html(data);
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });


    }

</script>