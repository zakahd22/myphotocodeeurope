<?php
$OrderID = $_POST['id'];
$checked = null;

$title = "";
$content = "";
$buttons = "";

$sql = "SELECT Pay_print_order.idOrder as orderNum,  Pay_print_order.idDongle as idDongle, booths.rand_string as rand_string, rentals.name as owner_name, rentals.App_email as owner_mail, Pay_print_order.quantitat as quantitat, Pay_print_order.validatedDate as validatedDate, Pay_print_order.preu as preu
        FROM Pay_print_order
        LEFT JOIN booths
        ON booths.id = Pay_print_order.idDongle
        LEFT JOIN rentals
        ON rentals.id = booths.rental_id
        WHERE Pay_print_order.idOrder = {$OrderID}";
        
$CLD_CON->OpenRs($sql);
if($CLD_CON->FetchArray()){   
    $orderNum = $CLD_CON->GetArrayField("orderNum");
    $dongleID = $CLD_CON->GetArrayField("id");
    $dongleString = $CLD_CON->GetArrayField("rand_string");
    $dongleOwner = $CLD_CON->GetArrayField("owner_name");
    $ownerMail = $CLD_CON->GetArrayField("owner_mail");
    $quantitat = $CLD_CON->GetArrayField("quantitat");
    $validatedDate = $CLD_CON->GetArrayField("validatedDate");
    $preu = $CLD_CON->GetArrayField("preu");

    if(isset($validatedDate)){
        $title = "Info Order {$orderNum}";
        $disabled = "disabled"; 
        $class_disabled = "disabledInput";
        $validated = <<<HTML
            <td class='td_title'>
                validated:
            </td>
            <td>
                {$validatedDate}
            </td>
HTML;
        
        $buttons = "<input type='button' class='popup-cancel' value='Cancel' onClick='hidePopupv2();'>";

    }
    else{
        $title = "Validate Order {$orderNum}";
        $disabled = ""; 
        $validated = "";
        $class_disabled = "";
        $buttons = <<<HTML
            <input type='button' class='popup-confirm' value='Save' onClick='validate_payprintDongle({$orderNum}); hidePopupv2();'>
            <input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>
HTML;
    }
    
    $content .= <<<HTML
            <table>
             <tr>
                <td class="td_title">
                    <p>String:</p>
                </td> 
                <td> 
                    {$dongleString}
                </td>
            </tr>
            <tr>
                <td class="td_title">
                    <p>Owner:</p>
                </td> 
                <td> 
                    {$dongleOwner}
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Alert email:</p>
                </td>
                <td> 
                    {$ownerMail}
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Quantity:</p>
                </td>
                <td> 
                    <input class="textInput numberInput {$class_disabled}" id="quantitat" type="number" name="quantitat" value="{$quantitat}" min="1" {$disabled}>
                </td>
            </tr>
            <tr>
                <td class="td_title"> 
                    <p>Price:</p>
                </td>
                <td> 
                    <input class="textInput numberInput {$class_disabled}" id="preu" type="number" name="preu" value="{$preu}" min="1" {$disabled}>
                </td>
            </tr>
            <tr>
                {$validated}
            </tr>
            </table>
HTML;
}
else{
    $content .= "Unexpected Error";
}

$content .= <<<HTML
    <script>
        $(document).ready(function(){
            /*
            $(".").click(function {
                
            });
            */
        });
         
        function validate_payprintDongle(dongle_id){
            var quantitat = 0;
            var preu = 0;
            
            quantitat = $("#quantitat").val();
            preu = $("#preu").val();

            var ajaxData = {id: dongle_id, q: quantitat, p: preu};
            $.ajax({
                url: 'edit/functions/payxprint/validateOrder.php',
                type: 'POST',
                //Ajax events
                success: function(data){
                    if(data != "OK"){
                        alert(data);
                    }
                    closePopup();
                    setProfileAndSubmenu("payxprint" , "orders" ,  {$_SESSION['USERID']})
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
        
    </script>
                                
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);