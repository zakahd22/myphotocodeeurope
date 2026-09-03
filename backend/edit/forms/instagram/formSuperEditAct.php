<?php

$baseController->createModel('InstagramSuggestions');

function utf8_converter($array){
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
    });
    return $array;
}


$html = "<script src='sections/instagram/resources/js/instagramEdit.js'></script>";
$html .= "<link rel='stylesheet' href='sections/instagram/resources/css/instagramEdit.css'>";

$CLD_CON->OpenRs("SELECT booths.rand_string AS string,rentals.name AS owner, App_booths.serialnumber AS serialnumber, 
                         Fcode_dongle.idPB AS idPb, Fcode_dongle.dateAct AS dateAct, Fcode_dongle.codeAct AS codeAct,
                         Fcode_dongle.codeReset AS codeReset
                  FROM Fcode_dongle
                  LEFT JOIN booths
                  ON Fcode_dongle.idDongle = booths.id
                  LEFT JOIN rentals
                  ON booths.rental_id = rentals.id
                  LEFT JOIN App_booths
                  ON Fcode_dongle.idPB = App_booths.idBooth
                  WHERE Fcode_dongle.idDongle = $ID");

if($CLD_CON->FetchArray()){
    $string     = $CLD_CON->GetArrayField("string");
    $owner      = $CLD_CON->GetArrayField("owner");
    $serial     = $CLD_CON->GetArrayField("serialnumber");
    $idPB       = $CLD_CON->GetArrayField("idPb");
    $dateAct    = $CLD_CON->GetArrayField("dateAct");
    $codeAct    = $CLD_CON->GetArrayField("codeAct");
    $codeReset  = $CLD_CON->GetArrayField("codeReset");
    
    $checked = "";
    if($allowTest == 1){
        $checked = "checked";
    }
    
    $title = "FinancingCode";
    
    $html .= <<<HTML
        <div id='popup_conten'>
            <div id='positionDiv0'>
            <div class='labelDiv'>
                <div class='label'><b>String:</b>&nbsp {$string}</div>
            </div>
            <div class='labelDiv'>
                <div class='label'><b>Owner:</b>&nbsp {$owner} </div>
            </div>
            <div class='labelDiv'>
                <div class='labelDateAct'><b>DateAct:</b>&nbsp {$dateAct}</div>
            </div>
            <div id='falselabel'>
            <div class='labelDiv'>
                <div class='label'><b>Serial:</b>&nbsp {$serial}</div>
            </div>
            </div>
            <div class='labelDiv'>
                <div class='label'><b>idPB:</b>&nbsp {$idPB}</div>
            </div>
            <div class='labelDiv'>
                <div class='label'><b>Code act.:</b>&nbsp {$codeAct}</div>
            </div>
            <div id='falselabel'>
            <div class='labelDiv'>
                <div class='label'><b>Code Reset:</b>&nbsp {$codeReset}</div>
            </div>
        </div>
            
        <div id='fCodeTable'>
            <table class='matching_table'>
                <tr>
                    <td>Id</td>
                    <td>Date</td>
                    <td>Plays</td>
                    <td colspan='2'>Code</td>
                    <td colspan='2'>Puk</td>
                </tr>
HTML;
                
    $CLD_CON->OpenRs("SELECT *
                  FROM Fcode_reg
                  WHERE idDongle = $ID
                  ORDER BY dateEnd");
    
    $dongles = $baseController->Fcode_regModel->getFcode($ID);
    
    foreach ($dongles as $dongle){
        $id          = $dongle["id"];
        $dateEnd     = $dongle["dateEnd"];
        $gracePlays  = $dongle["gracePlays"];
        $code        = $dongle["code"];
        $puk         = $dongle["puk"];
        $codeSent    = $dongle["codeSent"];
        $pukSent     = $dongle["pukSent"];
        

        if($dateEnd){
            $dateEnd = utils::date_std_to_datetime($dateEnd, 'm/d/Y', 'Y-m-d');
        }
        if($codeSent){
            $codeSent = utils::datetime_to_date_std($codeSent);
            $codeSent = utils::format_date_std($codeSent);
        }
        if($pukSent){
            $pukSent = utils::datetime_to_date_std($pukSent);
            $pukSent = utils::format_date_std($pukSent);
        }
        
        $html .= <<<HTML
        <tr>
            <td>
                {$id}
            </td>
            <td>
                $dateEnd
            </td>
            <td>
                $gracePlays
            </td>
            <td>
                $code
            </td>
            <td>
                {$codeSent}
                <img src='images/web/send.png'  class='finCodesend sendCode'></img>
            </td>
            <td>
                $puk
            </td>
            <td>
                {$pukSent}
                <img  src='images/web/send.png' class='finCodesend sendPuk'></img>
            </td>
        </tr> 
HTML;
                
    }
            $html .="</table>";
        $html .= "</div>";
    $html .= "</div>";   
}


$content = $html;

$buttons .= "<button type='button' class='popup-confirm' onclick='saveInfoFinancing({$ID}); hidePopupv2(); '>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$json = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

$json = utf8_converter($json);

echo json_encode($json);



