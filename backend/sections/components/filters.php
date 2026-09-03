<?php
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';

?>

<div style='background-color:#8989BA;' class='fDiv'>
     <table >
        <tr>
            <td>SN</td>
            <td><input type='text' id='serialnumber' class='textInput'></td>
            <td>Owner</td>
            <td>
                <select class='selectText' id='owner'>
                    <option value='0'>---------------</option>
                    <?php
                    
                     $CLD_CON->OpenRs("SELECT id , name FROM rentals ORDER BY name");
                     while($CLD_CON->FetchArray()){
                         $id = $CLD_CON->GetArrayField("id");
                         $name= $CLD_CON->GetArrayField("name");
                         echo "<option value='$id'>$name</option>";
                     }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Distributor</td><td>
                <select class='selectText' id='dis'>
                    <option value='0'>-Distributor-</option>
                    <option value='1'>-DC-</option>
                    <option value='2'>-DCA-</option>
                    <option value='3'>-MATT-</option>
                </select>
            </td>
            <td > Type </td><td>
                <select class='selectText' id='tipo'>
                    <option value='0'>-Type-</option>
                    <?php
                    $CLD_CON->OpenRs("SELECT * FROM CLD_typeComponents");
                    while($CLD_CON->FetchArray()){
                        $id= $CLD_CON->GetArrayField("id");
                        $name = $CLD_CON->GetArrayField("descripcio");
                        
                        echo "<option value='$id'>$name</option>";
                    }
                    ?>
                </select>
            </td>
            <td>
                <input type='button' class='okB okButton' onclick='filtersComponents();' style='top:-5px;'>
            </td>
        </tr>
    </table>
<!--    <input type='button' class='okB okButton' onclick='filtersComponents();' style='position:absolute;right:10px;top:51%;'>-->
    </div>

    


<script>
    
        $(document).ready(function() {

                            $("#serialnumber").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersComponents();
                                }
                            });
                           
                        });
    
    
    function filtersComponents(){
        var sn = $("#serialnumber").val();
        var owner = $("#owner").val();
        var dis = $("#dis").val();
        var type = $("#tipo").val();
        if(sn.length >0 || owner.length > 0 || dis !== '0' || type !== '0'){
            var data = { sn : sn , owner: owner , dis:dis, type: type , fil : 1};
            filters("components" , data);
        }
        
    }
    
</script>