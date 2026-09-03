<?php
header('Content-Type: application/xml');
include "../../conf.php";
include "../../conexio.php";

if(isset($_REQUEST['shop'])){
    $shop = $_REQUEST['shop'];
    $CLD_CON2 = clone($CLD_CON);
    $CLD_CON3 = clone($CLD_CON);
    $CLD_CON->OpenRs("SELECT * FROM SHP_Comandes WHERE printed=0 AND estat=1");
    
      $xml = '<?xml version="1.0" encoding="UTF-8"?>';
      $xml .= '<comandes>';
    
    while($CLD_CON->FetchArray()){
        $number = "SN";
        $id = $CLD_CON->GetArrayField("id");
        $n2 = $CLD_CON->GetArrayField("n2");
        $address_id =  $CLD_CON->GetArrayField("address");
        $contact_id =  $CLD_CON->GetArrayField("contact");
        $impost = $CLD_CON->GetArrayField("impostos");
        $currency_id = $CLD_CON->GetArrayField("currency");
        $fecha = $CLD_CON->GetArrayField("fecha");
        
       $CLD_CON2->OpenRs("SELECT symbol FROM SHP_currency WHERE id=$currency_id");
        if($CLD_CON2->FetchArray()){
            $currency_symbol = $CLD_CON2->GetArrayField("symbol");
        }
        
        $CLD_CON2->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE comanda=$id");
        if($CLD_CON2->FetchArray()){
            $producte_id= $CLD_CON2->GetArrayField("producte");
            $CLD_CON3->OpenRs("SELECT * FROM SHP_products WHERE id = $producte_id");
            if($CLD_CON3->FetchArray()){
                $name_p = $CLD_CON3->GetArrayField("name"); 
            }
            $qty = $CLD_CON2->GetArrayField("qty");
            $preu = $CLD_CON2->GetArrayField("preu");
            
            $photoCode = $CLD_CON2->GetArrayField("photoCode");
            $CLD_CON3->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON e.id=p.event_id WHERE code ='$photoCode'");
            if($CLD_CON3->FetchArray()){
                $photoURL = "events/". $CLD_CON3->GetArrayField("start_date") . $CLD_CON3->GetArrayField("id") . "/" . $photoCode . ".jpg"; 
                              list($width, $height) = getimagesize(G_PATH . $photoURL);
                 if ($height > $width) {
                     $photoType= 1;
                 }else{
                      if ($height > 1000) {
                        $photoType= 2; 
                      }else{
                         $photoType= 3;
                      }
                 }
            }
            
        }
        
        
        $CLD_CON2->OpenRs("SELECT  * FROM SHP_address WHERE id=$address_id");
        if($CLD_CON2->FetchArray()){
            $street = $CLD_CON2->GetArrayField("Street");
            $number = $CLD_CON2->GetArrayField("Number");
            $city = $CLD_CON2->GetArrayField("City");
            $state = $CLD_CON2->GetArrayField("State");
            $country = $CLD_CON2->GetArrayField("Country");
            $floor = $CLD_CON2->GetArrayField("floor");
            $zip = $CLD_CON2->GetArrayField("zip");
        }
        $CLD_CON2->OpenRs("SELECT  * FROM SHP_Contacts WHERE id=$contact_id");
        if($CLD_CON2->FetchArray()){
            $name = $CLD_CON2->GetArrayField("Name");
            $lname = $CLD_CON2->GetArrayField("Last_Name");
            $phone = $CLD_CON2->GetArrayField("Phone");
            $email = $CLD_CON2->GetArrayField("email");
        }
        

        
        
        $xml .= '<comanda>';
        $xml .= '<reference>' . $id . '</reference>';
        $xml .= '<n2>'.$n2.'</n2>';
        $xml .= '<fecha>' . $fecha . '</fecha>';
        $xml .= '<currency>'.$currency_symbol . '</currency>';
        $xml .= '<producte>';
            $xml .= '<photo>';
                $xml .= '<purl>'.$photoURL.'</purl>';
                $xml .= '<codi>'.$photoCode.'</codi>';
                $xml .= '<tipo>'.$photoType.'</tipo>';
            $xml .= '</photo>';
            $xml .= '<id>'.$producte_id.'</id>';
            $xml .= '<namep>'.$name_p. "</namep>";
            $xml .= '<qty>'.$qty. "</qty>";
            $xml .= '<preu>'.$preu. "</preu>";

        $xml .= '</producte>';
        $xml .= '<address>';
            $xml .= '<street>' . $street . "</street>";
            $xml .= '<num>' . $number . "</num>";
            $xml .= '<city>' . $city . "</city>";
            $xml .= '<state>' . $state . "</state>";
            $xml .= '<country>' . $country . "</country>";
            $xml .= '<zip>' . $zip . "</zip>";
            $xml .= '<floor>' . $floor . "1</floor>";
        $xml .= '</address>';
        $xml .= '<contact>';
            $xml .= '<name>'. $name . '</name>';
            $xml .= '<lastname>'. $lname . '</lastname>';
            $xml .= '<phone>'. $phone . '</phone>';
            $xml .= '<email>'. $email . '</email>';
        $xml .= '</contact>';
        $xml .= '</comanda>';
      
    }
    $xml .= '</comandes>';
    
    echo "" . $xml ."";
}else{
    echo "ERROR";
}
?>
