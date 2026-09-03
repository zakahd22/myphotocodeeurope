<?php
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';

?>

<div style='background-color:#80889c;' class='fDiv'>
     <table >
        <tr>
            <td>UPGRADEid</td>
            <td><input type='text' id='UPGRADEid' class='textInput'></td>
            <td>idBootDC</td>
            <td>
                <select class='selectTextNoCenter' id='idBootDC'>
                    <option value='0'>---------------</option>
                    <?php
                    
                     $CLD_CON->OpenRs("SELECT idBootDC , textline FROM App_bootDC ORDER BY textline");
                     while($CLD_CON->FetchArray()){
                         $id = $CLD_CON->GetArrayField("idBootDC");
                         $name= $CLD_CON->GetArrayField("textline");
                         echo "<option value='$id'>$id - $name</option>";
                     }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>allowedIds</td>
            <td><input type='text' id='allowedIds' class='textInput'></td>
            <td>response   </td>
            <td><input type='text' id='responseVar' class='textInput'>   </td>
            <td>
                <input type='button' class='okB okButton' onclick='filtersUpgrades();' style='top:-5px;'>
            </td>
        </tr>
    </table>
<!--    <input type='button' class='okB okButton' onclick='filtersUpgrades()();' style='position:absolute;right:10px;top:51%;'>-->
    </div>

    


<script>
    
        $(document).ready(function() {

                            $("#UPGRADEid").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersUpgrades()();
                                }
                            });
                            $("#allowedIds").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersUpgrades()();
                                }
                            });
                           
                        });
    
    
    function filtersUpgrades(){
        var UPGRADEid = $("#UPGRADEid").val();
        var idBootDC = $("#idBootDC").val();
        var allowedIds = $("#allowedIds").val();
        var responseVar = $("#responseVar").val();
        
        if(UPGRADEid.length >0 ||  allowedIds.length > 0 || idBootDC !== '0' || responseVar.length > 0){
            var data = { UPGRADEid : UPGRADEid , allowedIds: allowedIds , idBootDC:idBootDC, responseVar: responseVar};
            filters("upgrade" , data);
        }
        
    }
    
</script>