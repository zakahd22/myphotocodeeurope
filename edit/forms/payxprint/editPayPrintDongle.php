<?php
$DongleID = $_POST['id'];
$checked = null;

$title = "";
$content = "";
$buttons = "";

$sql= "SELECT booths.rand_string as rand_string, rentals.name as owner_name, rentals.App_email as owner_mail, Pay_print_dongle.minStock as minStock, Pay_print_dongle.quantitat as quantitat, Pay_print_dongle.preu as preu,  Pay_print_dongle.saldo as saldo
        FROM booths
        LEFT JOIN Pay_print_dongle
        ON Pay_print_dongle.idDongle = booths.id
        LEFT JOIN rentals
        ON rentals.id = booths.rental_id
        WHERE booths.id = {$DongleID}";
        
$CLD_CON->OpenRs($sql);
if($CLD_CON->FetchArray()){
    $dongleString = $CLD_CON->GetArrayField("rand_string");
    $rentalName = $CLD_CON->GetArrayField("owner_name");
    $rentalMail = stripslashes($CLD_CON->GetArrayField("owner_mail"));
    $minStock = $CLD_CON->GetArrayField("minStock");
    $quantitat = $CLD_CON->GetArrayField("quantitat");
    $preu = $CLD_CON->GetArrayField("preu");
    $saldo = $CLD_CON->GetArrayField("saldo");

    $title = "PayxPrint {$dongleString}-Dongle";
    
    $content .= <<<HTML
            <table>
            <tr>
                <td class="td_title">
                    <p>This dongle owns:</p>
                </td> 
                <td> 
                    {$rentalName}
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Alert email:</p>
                </td>
                <td> 
                    {$rentalMail}
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Minimum Stock:</p>
                </td>
                <td> 
                    <input class="popupInputLarge" id="min_stock" type="number" name="min_stock" value="{$minStock}" min="1">
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Quantity:</p>
                </td>
                <td> 
                    <input class="popupInputLarge" id="quantitat" type="number" name="quantitat" value="{$quantitat}" min="1">
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Price:</p>
                </td>
                <td> 
                    <input class="popupInputLarge" id="preu" type="number" name="preu" value="{$preu}" min="1">
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Balance:</p>
                </td>
                <td> 
                    {$saldo}
                </td>
            </tr>
            </table> 
HTML;
                    
$buttons .= "<input type='button' class='popup-confirm' value='Save' onClick='update_payprintDongle($minStock, $quantitat, $preu);  hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2(); setSection(\"payxprint\" , 2 , {$USERID});'>";

}
else{
    $content .= "Unexpected Error";
}

$content .= <<<HTML
    <script>
        $(document).ready(function(){
            $(".miniTrash").click(function() {
                delete_payprintDongle({$DongleID});
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
                            closePopup();
                            setSection("payxprint" , 2 , {$USERID}); 
                       }
                    },
                    // Form data
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }
        }
                
        function update_payprintDongle(old_minStock, old_quantitat, old_preu) {
            var minStock=0, quantitat=0, preu=0;

            minStock = $("#min_stock").val();
            quantitat = $("#quantitat").val();
            preu = $("#preu").val();
            
            //alert("minstock = "+minStock+"quantity="+quantitat+"price="+preu);
            var ajaxData = {id: {$DongleID}, ms: minStock, qty: quantitat, pr: preu};
            $.ajax({
                url: 'edit/functions/payxprint/saveDongleData.php',
                type: 'POST',
                //Ajax events
                success: function(data){
                    //alert(data);
                    if(data === "OK"){
                        closePopup();
                        setSection("payxprint" , 2 , {$USERID});
                    }
                    else{
                        alert(data);
                    }
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
    </script>
    </html>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);