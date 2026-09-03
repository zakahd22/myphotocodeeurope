<?php
require_once '../../../common/global.php';
include "../../../sessio.php";
require_once G_PATH . 'common/conexio.php';

function isPayxPrint($startData, $minStock, $quantitat, $preu){
    $now = time();
    return (($startData <= $now) && ((isset($minStock)) || (isset($quantitat)) || (isset($preu))));
}

function checkFilters(){
    /*
    USE OF global!
     */
    
    $title = "";
    $value = "";
    $and = "";
    $i = 0;
    
    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }
    if(isset($_POST['value'])){
        $value = $_POST['value'];
    }
    
    if($title != "" && $value != ""){
        switch($title){
            case 'Owner':
                $and = "AND rentals.name LIKE '%{$value}%' ";
                break;
            
            case 'String':
                $and = "AND booths.rand_string LIKE '%{$value}%' ";
                break;
            
            default:
                break;
        }
        
        $result = array(
            'title' => $title,
            'value' => $value,
            'and' => $and
        );
        $i++;
    }
   
    return $result;
}

$filter = checkFilters();

$sql = "SELECT booths.id as id, booths.rand_string as rand_string, rentals.name as owner_name, Pay_print_dongle.quantitat as quantitat, Pay_print_dongle.preu as preu, Pay_print_dongle.saldo as saldo
        FROM Pay_print_dongle
        LEFT JOIN booths
        ON booths.id = Pay_print_dongle.idDongle
        LEFT JOIN rentals
        ON rentals.id = booths.rental_id
        WHERE booths.CLD_Distributor = {$USERID} ";

if(count($filter) > 0){
    $sql .= $filter['and'];
}
        
$CLD_CON->OpenRs($sql);

echo "<div class='inContent'>";
if($CLD_CON->GetRsRows() > 0){
    while ($CLD_CON->FetchArray()) {
        $dongleID = $CLD_CON->GetArrayField("id");
        $dongleString = $CLD_CON->GetArrayField("rand_string");
        $dongleOwner = $CLD_CON->GetArrayField("owner_name");
        $quantitat = $CLD_CON->GetArrayField("quantitat");
        $preu = $CLD_CON->GetArrayField("preu");
        $saldo = $CLD_CON->GetArrayField("saldo");

        echo <<<HTML
            <ul class='regDongleUL' onclick="edit(67, {$dongleID})">
                <li  style='width:20%;' title='String'>{$dongleString}</li>
                <li  style='width:20%' title='Owner Name'>Owner: {$dongleOwner}</li>
                <li  style='width:20%' title='quantity'>Qty: {$quantitat}</li>
                <li  style='width:20%' title='price'> {$preu}</li>
                <li  style='width:20%' title='balance'> Remaining: $saldo</li>
            </ul>
HTML;
    }
}
else{
    if(count($filter) > 0){
         echo "No matches for <b>{$filter['title']} '{$filter['value']}'</b> ";
    }
    else{
        echo "You do not have any dongle yet!";
    }
}

echo "</div>";
include '../../pagescount.php';

echo <<<HTML
    <script>
        $(document).ready(function(){
            $(".miniTrash").click(function(e) {
                e.stopPropagation();
                delete_payprintDongle({$dongleID});
            });                
        });
                
        function delete_payprintDongle(Did){
            if (confirm('Are you sure you want to delete this PayxPrint Dongle?')) {
                var ajaxData = {id: Did};
                $.ajax({
                    url: 'edit/functions/payxprint/deleteDongleData.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data){
                        //alert(data);
                        if(data === "OK"){
                            setSection("payxprint" , 2, {$USERID}); 
                        }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }
        }
    </script>
HTML;
?>