<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
echo "<div class='inContent'>";
echo "<h1>Components</h1>";

echo "<div class='box' style='width:70%;margin-left:15%;padding-top:10px;padding-bottom:10px;'>";
$CLD_CON->OpenRs("SELECT b.CLD_idType , b.type FROM App_booths  b WHERE b.idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $idType = $CLD_CON->GetArrayField("CLD_idType");
    $typeChar = $CLD_CON->GetArrayField("type");
    if (empty($idType)) {
        $CLD_CON2->OpenRs("SELECT c.id FROM CLD_boothTypes c WHERE c.char='$typeChar' LIMIT 1");
        if ($CLD_CON2->FetchArray()) {
            $idType = $CLD_CON2->GetArrayField("id");
        }
    }
}

if ($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 2) {
    $CLD_CON->OpenRs("SELECT * FROM CLD_boothsComponents WHERE id_typeBooth=$idType ORDER BY id_component , optional");
    while ($CLD_CON->FetchArray()) {
        $c = 0;
        $idcomponent = $CLD_CON->GetArrayField("id_component");
        $qty = $CLD_CON->GetArrayField("quantitat");
        $optional = $CLD_CON->GetArrayField("optional");
        $CLD_CON2->OpenRs("SELECT descripcio FROM CLD_typeComponents WHERE id = $idcomponent");
        if ($CLD_CON2->FetchArray()) {
            $descr_comp = $CLD_CON2->GetArrayField("descripcio");
        }

        $CLD_CON2->OpenRs("SELECT serialnumber FROM CLD_components WHERE type=$idcomponent AND booth=$ID");
        while ($CLD_CON2->FetchArray()) {
            $sn = $CLD_CON2->GetArrayField("serialnumber");

            echo "<div style='border:1px solid gray;overflow:hidden;margin-bottom:10px;'>";
            echo "<p style='border-bottom:1px solid gray;'> $descr_comp</p>";
            echo "<div style='width:40%;display:inline;float:left;overflow:hidden;text-align:center;'>";
            echo "<img src='images/web/components/c$idcomponent.png' style='height:50px;'>";
            echo "</div>";
            echo "<div style='width:55%;display:inline;float:left;overflow:hidden;'>";
            echo "<p>SN : $sn &nbsp;<input type='button' class='miniTrash' title='Remove from this PhotoBooth' onclick='removeCMP(\"$sn\" , $ID);'></p>";
            echo "<p><input type='text' class='textInput' id='cN$c$idcomponent$optional'> <input type='button' class='okB okButton' onClick='setComponent(\"$sn\" , \"cN$c$idcomponent$optional\" , $ID , $idcomponent);'></p>";
            echo "</div>";
            echo "</div>";
            $c++;
        }
        while ($c < $qty) {
            $sn = "Undefined";
            if ($optional == 1) {
                $sn = "Undefined - Optional";
            }
            echo "<div style='border:1px solid gray;overflow:hidden;margin-bottom:10px;'>";
            echo "<p style='border-bottom:1px solid gray;'> $descr_comp</p>";
            echo "<div style='width:40%;display:inline;float:left;overflow:hidden;text-align:center;'>";
            echo "<img src='images/web/components/c$idcomponent.png' style='height:50px;'>";
            echo "</div>";
            echo "<div style='width:55%;display:inline;float:left;overflow:hidden;'>";
            echo "<p>SN : $sn</p>";
            echo "<p><input type='text' class='textInput' id='cN$c$idcomponent$optional'> <input type='button' class='okB okButton' onClick='setComponent(\"\" , \"cN$c$idcomponent$optional\" , $ID , $idcomponent);'></p>";
            echo "</div>";
            echo "</div>";
            $c++;
        }
    }
}
if ($_SESSION['USERTYPE'] == 3 || $_SESSION['USERTYPE']==6) {
    $CLD_CON->OpenRs("SELECT * FROM CLD_boothsComponents WHERE id_typeBooth=$idType ORDER BY id_component , optional");
    while ($CLD_CON->FetchArray()) {
        $c = 0;
        $idcomponent = $CLD_CON->GetArrayField("id_component");
        $qty = $CLD_CON->GetArrayField("quantitat");
        $optional = $CLD_CON->GetArrayField("optional");
        $CLD_CON2->OpenRs("SELECT descripcio FROM CLD_typeComponents WHERE id = $idcomponent");
        if ($CLD_CON2->FetchArray()) {
            $descr_comp = $CLD_CON2->GetArrayField("descripcio");
        }

        $CLD_CON2->OpenRs("SELECT serialnumber FROM CLD_components WHERE type=$idcomponent AND booth=$ID");
        while ($CLD_CON2->FetchArray()) {
            $sn = $CLD_CON2->GetArrayField("serialnumber");

            echo "<div style='border:1px solid gray;overflow:hidden;margin-bottom:10px;'>";
            echo "<p style='border-bottom:1px solid gray;'> $descr_comp</p>";
            echo "<div style='width:40%;display:inline;float:left;overflow:hidden;text-align:center;'>";
            echo "<img src='images/web/components/c$idcomponent.png' style='height:50px;'>";
            echo "</div>";
            echo "<div style='width:55%;display:inline;float:left;overflow:hidden;'>";
            echo "<p>SN : $sn</p>";
            echo "</div>";
            echo "</div>";
            $c++;
        }
        while ($c < $qty) {
            $sn = "Undefined";
            if ($optional == 1) {
                $sn = "Undefined - Optional";
            }
            echo "<div style='border:1px solid gray;overflow:hidden;margin-bottom:10px;'>";
            echo "<p style='border-bottom:1px solid gray;'> $descr_comp</p>";
            echo "<div style='width:40%;display:inline;float:left;overflow:hidden;text-align:center;'>";
            echo "<img src='images/web/components/c$idcomponent.png' style='height:50px;'>";
            echo "</div>";
            echo "<div style='width:55%;display:inline;float:left;overflow:hidden;'>";
            echo "<p>SN : $sn</p>";
            echo "</div>";
            echo "</div>";
            $c++;
        }
    }
}

echo "</div>";
echo "</div >"
?>

<script>
    function setComponent(oldC, newCid, boothid , type) {

        var newC = $("#" + newCid).val();
        if (newC.length === 0) {
            alert("The SN of new component is empty.");
            return;
        }
        if (newC === oldC) {
            alert("The SN of new component is same of old component.");
            return;
        }
        var ajaxData = {oldC : oldC , newC : newC , id : boothid , type:type};
        $.ajax({
            url: 'sections/photobooths/functions/setComponents.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {
                    profile("photobooths", "components", boothid);
                } else {
                    alert(data);
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
        
    }
    function removeCMP(sn , boothid ){
    if(confirm("Are you sure want reomve "+sn+" from this Photobooth?")){
    var ajaxData = {id: sn  , boothid : boothid};
        $.ajax({
            url: 'sections/photobooths/functions/removeCMP.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {
                    profile("photobooths", "components", boothid);
                } else {
                    alert(data);
                }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
    }



</script>