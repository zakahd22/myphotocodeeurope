<?php

//include '../../../sessio.php';
//require_once G_PATH . 'common/conexio.php';
//
$baseController = new baseController;
$baseController->createModel('App_boothDongle');
$baseController->createModel('Fcode_reg');

function utf8_converter($array){
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
    });
 
    return $array;
}

$html = "<script src='sections/financingCode/resources/js/fCodeEdit.js'></script>";
$html .= "<link rel='stylesheet' href='sections/financingCode/resources/css/fCodeEdit.css'>";

$CLD_CON->OpenRs("SELECT booths.rand_string AS string,rentals.name AS owner, App_booths.serialnumber AS serialnumber, 
                         Fcode_dongle.idPB AS idPb, Fcode_dongle.allowTest AS allowTest, Fcode_dongle.codeAct AS codeAct,
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
    $allowTest  = $CLD_CON->GetArrayField("allowTest");
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
<!--
            <div class='labelDiv'>
                <div class='label'><b>Serial:</b>&nbsp {$serial}</div>
            </div>
            <div id='falselabel'>
            </div>
            <div class='labelDiv'>
                <div class='label'><b>idPB:</b>&nbsp {$idPB}</div>
            </div>
-->
        </div>
        <form id='financing_info' method='post'>
            <div id='positionDiv1'>
                <div class='input_div'>
                    <div class='label'>
                        Code act.
                        <input type='text' name='codeAct'value='{$codeAct}'>
                    </div>
                </div>
                <div class='input_div'>
                    <div class='label'>
                        Code Reset
                        <input type='text' name='codeReset' value='{$codeReset}'>
                    </div>
                </div>
                <div class='input_div'>
                    <div class='label'>
                        IdPb
                        <input type='num' name='idPb' value='{$idPB}'>
                    </div>
                </div>
                <div class='input_div_checkbox'>
                    Allow test
                    <input class='input_checkbox' type='checkbox' name='allowTest' value='1' $checked>
                    <input type='hidden' name='dongle' value='{$ID}'>
                </div>
            </div>
        </form>
HTML;
      
                    
    $idPbs = $baseController->App_boothDongleModel->getPbsDongle($ID);
 
    if($idPbs){
       foreach ($idPbs as $idPb){
           $binds .= "{$idPb['idBooth']},"; 
       }
       $binds = substr($binds, 0, -1);

       $html .= "<div id='pbsList'>Pbs binds: $binds.</div>";
    }
                
    $html .= <<<HTML
        <div id='fCodeTable'>
            <table class='matching_table'>
                <tr>
                    <td>Id</td>
                    <td>Date (mm/dd/YYYY)</td>
                    <td>Plays</td>
                    <td>Code</td>
                    <td>Puk</td>
                    <td>
                        <div id='addnewbtn' dong='{$ID}'></div>
                    </td>
                </tr>
                <tr id="rowAddNew">
                    <td></td>
                    <td>
                        <input class='tableInput' id='newDateEnd' value=''>
                    </td>
                    <td>
                        <input class='tableInput' id='newGracePlays' value='' >
                    </td>
                    <td>
                        <input class='tableInput' id='newCode'  maxlength="5" value=''>
                    </td>
                    <td>
                        <input class='tableInput inputPuk'  id='newPuk' maxlength="5" value=''>
                    </td>
                    <td>
                        <div class='contentRow'>
                            <div id='saveNewRow'></div>
                            <div id='cancelNew'></div>  
                        </div>
                    </td>
                </tr>
HTML;
                        
           
                        
    $CLD_CON->OpenRs("SELECT *
                  FROM Fcode_reg
                  WHERE idDongle = $ID
                  ORDER BY dateEnd");
    
    $dongles = $baseController->Fcode_regModel->getFcode($ID);
    
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
                <input class='tableInput' id='DateEnd_{$id}' value='{$dateEnd}'>
            </td>
            <td>
                <input class='tableInput inputPlay' id='GracePlays_{$id}' value='{$gracePlays}' >
            </td>
            <td>
                <input class='tableInput inputCode' id='Code_{$id}' value='{$code}' maxlength="5">
            </td>
            <td>
                <input class='tableInput inputPuk'  id='Puk_{$id}' value='{$puk}' maxlength='5'>
            </td>
            <td>
                <div class='contentRow'>
                    <div onclick='saveRow($id)' class='save' id='{$id}'></div> 
                    <div onclick='deleteRow($id)' class='delete' id='{$id}'></div> 
                </div>
            </td>
        </tr> 
HTML;
        
    }
            $html .="</table>";
        $html .= "</div>";
    $html .= "</div>";   
}
$content = $html;

$buttons .= "<button type='button' class='popup-confirm' onclick='saveInfoFinancing({$ID}); hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);


$array_result = utf8_converter($array_result);

echo json_encode($array_result);
