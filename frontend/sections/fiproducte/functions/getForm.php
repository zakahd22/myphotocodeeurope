<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$sn = $_POST['sn'];

$CLD_CON->OpenRs("SELECT * FROM App_booths WHERE serialnumber='$sn' AND CLD_Status=0");
if ($CLD_CON->FetchArray()) {
    $ID = $CLD_CON->GetArrayField("idBooth");
    $idType = $CLD_CON->GetArrayField("CLD_idType");
    if (empty($idType)) {
        $model = substr($sn, 2, 2);
        $CLD_CON->OpenRs("SELECT id , name FROM CLD_boothTypes WHERE CLD_modelSN = $model");
        if ($CLD_CON->FetchArray()) {
            $typeName = $CLD_CON->GetArrayField("name");
            $idType = $CLD_CON->GetArrayField("id");
        }
    } else {
        $CLD_CON->OpenRs("SELECT name FROM CLD_boothTypes WHERE id = $idType");
        if ($CLD_CON->FetchArray()) {
            $typeName = $CLD_CON->GetArrayField("name");
        }
    }

    echo "<h1>Formulari per PhotoBooth $typeName</h1>";
    echo "<table style='width:60%;margin-left:5%;border:1px solid gray;' id='tbl'>";
    echo "<tr style='border:1px solid gray;'><td style='border:1px solid gray;'>Descripcio Component</td><td style='border:1px solid gray;'>Serial Number</td></tr>";
    $CLD_CON->OpenRs("SELECT * FROM CLD_boothsComponents WHERE id_typeBooth=$idType ORDER BY id_component , optional");
    $c=0;
    
    while ($CLD_CON->FetchArray()) {
        $w =0;
        $idcomponent = $CLD_CON->GetArrayField("id_component");
        $qty = $CLD_CON->GetArrayField("quantitat");
        $optional = $CLD_CON->GetArrayField("optional");
        $CLD_CON2->OpenRs("SELECT descripcio FROM CLD_typeComponents WHERE id = $idcomponent");
        if ($CLD_CON2->FetchArray()) {
            $descr_comp = $CLD_CON2->GetArrayField("descripcio");
        }

        $CLD_CON2->OpenRs("SELECT serialnumber FROM CLD_components WHERE type=$idcomponent AND booth=$ID");
        while ($CLD_CON2->FetchArray()) {
            $sn2 = $CLD_CON2->GetArrayField("serialnumber");
            echo "<tr  >";
            echo "<td style='border:1px solid gray;'>$descr_comp</td>";
            echo "<td style='border:1px solid gray;'><input type='text' class='textInput' data-info='c$c' value='$sn2'> <input type='hidden' id='c$c' value='1#$idcomponent#$optional#$ID#$sn2'></td>";
            echo "</tr>";
            $w++;
            $c++;
        }
        while ($w < $qty) {

            if ($optional == 1) {
                $s = "Optional";
            }
            echo "<tr >";
            echo "<td style='border:1px solid gray;'>$descr_comp</td>";
            echo "<td style='border:1px solid gray;'><input type='text' class='textInput' data-info='c$c' value=''> $s <input type='hidden' id='c$c' value='2#$idcomponent#$optional#$ID'></td>";
            echo "</tr>";
            $w++;
            $c++;
        }
    }
    if($idType != 4 && $idType != 13 && $idType != 14){
         echo "<tr >";
            echo "<td style='border:1px solid gray;'>Dongle String</td>";
            echo "<td style='border:1px solid gray;'><input type='text' class='textInput' id='dongleS'></td>";
         echo "</tr>";
    }else{
       echo " <input type='hidden' class='textInput' id='dongleS' value='#ND#'>";
    }
    
    
    echo "</table>";
    echo "<div style='position: absolute;top: 35%;right: 20px;width: 25%;border: 1px solid gray;padding: 21px;text-align: center;'>";
        echo "<p>Control Quality <select class='selectText' id='controlQuality'>";
        echo "<option value='0'>---</option>";
        echo "<option value='JS'>JS</option>";
        echo "<option value='JT'>JT</option>";
        echo "</select></p>";
        echo "<img src='images/web/toStock.jpg' style='width:30%;display:inline;border:10px outset gray;cursor:pointer;margin:5px;' title ='Finish Factory Product(STOCK)' onclick='toFinish($ID)'>";
        echo "<img src='images/web/toIncomplete.jpg' style='width:30%;display:inline;border:10px outset gray;cursor:pointer;margin:5px;' title ='To Incomplet Product' onclick='edit(64 , $ID)'>";
    echo "</div>";
} else {
    echo "<p> El PhotoBooth $sn no esta en produccio o no existeix</p>";
}

?>

<script>
    
    function toFinish(id){
      var cQ = $("#controlQuality").val();
      var ajaxData;
        if(cQ === "0"){
            alert("Please select the Control Quality Person");
        }else{
            var dongle = $("#dongleS").val();
            if(dongle.length !== 3){
                alert("La String del dongle es composa te 3 caracters alfanumerics");
                return;
            }
            if(dongle == "#ND#"){
               ajaxData= {id:id , cQuality:cQ , noDongle:1};
            }else{
                ajaxData = {id:id , cQuality:cQ , dongleString:dongle};
            }
            
            
             $.ajax({
                    url: 'sections/fiproducte/functions/toFinshishProduct.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {
                            alert("El proces ha funcionat correctement, el PhotoBooth ha passat a Finish Product");
                            setSection("fiproducte", 2);
                        } else {
                            alert(data);                           
  
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
        }
    }
    
    $(document).ready(function() {
        $('#tbl input[type=text]').keydown(function(e) {
            //get the next index of text input element
            var idx = $('input[type=text]').index(this);
            var next_idx = idx + 1;
            if (e.keyCode == 13) {

                var inp = $('input[type=text]:eq(' + idx + ')');
                var idHidden = inp.attr("data-info");
                var info = $("#"+idHidden).val();
                var i = info.split("#");
                var newSN = inp.val();
                var cmpID = i[1];
                var optional = i[2];
                var booth = i[3];
                if (i[0] === "2") {
                    SNout = "";
                }else{
                    var SNout = i[4];
                }
                if (newSN === SNout) {
                    inp.blur();
                    $('input[type=text]:eq(' + next_idx + ')').focus();
                } else {
                    var ajaxData = {SNout: SNout, newSN: newSN, cmpID: cmpID, id: booth, optional: optional};
                    $.ajax({
                        url: 'sections/fiproducte/functions/addcmp.php',
                        type: 'POST',
                        //Ajax events
                        success: function(data) {
                            var d;
                            if (data === "OK") {
                                if (newSN.lenght > 0) {
                                    d = "1#" + newSN + "#" + cmpID + "#" + optional + "#" + booth;
                                } else {
                                    d = "2#" + cmpID + "#" + optional + "#" + booth;
                                }
                                $("#"+idHidden).val(d);
                                inp.blur();
                                $('input[type=text]:eq(' + next_idx + ')').focus();
                            } else {
                                alert(data);
                            }
                        },
                        // Form data
                        cache: false,
                        data: ajaxData,
                        contentType: 'application/x-www-form-urlencoded'
                    });

                }



            }
        });

    });
    
</script>