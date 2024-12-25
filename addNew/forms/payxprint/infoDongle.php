<?php
require_once dirname(__FILE__) . '/../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

if(isset($_POST['s'])){
    $step = $_POST['s'];
}
if(isset($_POST['id'])){
    $id = $_POST['id'];
}

switch($step){
    case 1:
        $sql = "SELECT booths.id as id, booths.dongle as dongle, booths.reference as reference, booths.rand_string as rand_string, booths.rental_id as rental_id 
                FROM booths
                INNER JOIN rentals
                ON rentals.id = booths.rental_id
                WHERE NOT EXISTS (
                    SELECT Pay_print_dongle.idDongle
                    FROM Pay_print_dongle
                    WHERE Pay_print_dongle.idDongle = booths.id
                )
                AND rentals.CLD_DistributorId = {$id}";

        $CLD_CON->OpenRs($sql);
        if($CLD_CON->GetRsRows() > 0){    
            $selection_values = <<<HTML
                <select id='dongleSelection' class='popupInputLarge' name='dongleType' value="">
                    <option id='' class='null_to_pxp_dongle' value=''>Select the dongle to convert</option>
HTML;
            
             while ($CLD_CON->FetchArray()) {
                $dongleId = $CLD_CON->GetArrayField("id");
                $rand_string = $CLD_CON->GetArrayField("rand_string");

                $selection_values .= "<option id='{$dongleId}' class='to_pxp_dongle' value='{$dongleId}'>{$rand_string}</option>";
            }
             $selection_values .= "</select>";
        }
        else{
            $selection_values = <<<HTML
                <select id='dongleSelection' class='popupInputLarge' name='dongleType' value="" disabled >
                    <option id='' class='null_to_pxp_dongle' value=''>No compatible Dongles</option>
                </select>
HTML;
        }

        $html = <<<HTML
            <table>
                <tr>
                    <td colspan="2">
                        <h4>Step 1:</h4>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Select the dongle you want to convert to Pay x Print:
                    </td>
                </tr>
                <tr>
                    <td>
                        Dongle:
                    </td>
                    <td>
                        {$selection_values}
                    </td>
                </tr>
            </table>
HTML;
        break;
    
    case 2:
        $sql = "SELECT booths.dongle as dongle, booths.reference as reference, booths.rand_string as rand_string, rentals.name as owner_name, rentals.App_email as owner_mail 
                FROM booths
                LEFT JOIN rentals
                ON rentals.id = booths.rental_id
                WHERE booths.id = {$id}";

        $CLD_CON->OpenRs($sql);

        if($CLD_CON->FetchArray()) {
            $rand_string = $CLD_CON->GetArrayField("rand_string");
            $owner_name = $CLD_CON->GetArrayField("owner_name");
            $owner_mail = $CLD_CON->GetArrayField("owner_mail");

            $html = <<<HTML
               <table>
                    <tr>
                        <td colspan="2">
                            <h4>Step 2:</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h4>Dongle Information</h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Dongle String:
                        </td>
                        <td>
                            {$rand_string}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Owner:
                        </td>
                        <td>
                            {$owner_name}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Alert Email:
                        </td>
                        <td>
                            {$owner_mail}
                        </td>
                    </tr>
                    <tr>
                       <td colspan="2">
                            <br />
                            Are you sure you want to convert this Dongle to Pay x Print? <br />
                            Click Accept to confirm.
                       </td>
                    </tr>
                </table>
                <input id='{$id}' class='dongle_id popupInputLarge' type="hidden"></input>
HTML;
            }
            else{
                echo "Error: Something wrong happened";
            }
        break;
    
    case 3:
        $sql = "SELECT booths.dongle as dongle, booths.reference as reference, booths.rand_string as rand_string, rentals.name as owner_name, rentals.App_email as owner_mail 
                FROM booths
                LEFT JOIN rentals
                ON rentals.id = booths.rental_id
                WHERE booths.id = {$id}";

        $CLD_CON->OpenRs($sql);

        if($CLD_CON->FetchArray()) {
            $rand_string = $CLD_CON->GetArrayField("rand_string");
            $owner_name = $CLD_CON->GetArrayField("owner_name");
            $owner_mail = $CLD_CON->GetArrayField("owner_mail");
            echo <<<HTML
                <table>
                    <tr>
                        <td colspan="2">
                            <h4>Step 3:</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h4>New Dongle-{$rand_string} Customization</h4>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_title">
                            <p>This dongle owns to:</p>
                        </td> 
                        <td> 
                            {$owner_name}
                        </td>
                    </tr>
                    <tr>
                        <td class="td_title"> 
                            <p>Alert email:</p>
                        </td>
                        <td> 
                            {$owner_mail}
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
                    </table> 
                </div>
                <input id='{$id}' class='dongle_id' type="hidden"></input>

HTML;
        }
        break;
    
    default:
        break;
}

echo $html;
