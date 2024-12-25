<?php

$baseController->createModel('Fcode_dongle');

$dongle = $baseController->Fcode_dongleModel->getFinancingDongleById($ID);

if($dongle){
    $dateAct = $dongle[0]["dateAct"];
}

if($dateAct){ include './forms/financingCode/formSuperEditAct.php';}
else{ include './forms/financingCode/formSuperEditNoAct.php';}

