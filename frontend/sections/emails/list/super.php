<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('registre_emails');

$regEmails = $baseController->registre_emailsModel->getRegistreEmails($LIMIT);
$select_nolimit = "SELECT e.code FROM registre_emails e GROUP BY e.email ORDER BY e.email";
$totalrows = count($baseController->registre_emailsModel->getRegistreEmails());

$html ="<div class='inContent'>";
$count = 0;
foreach ($regEmails as $regEmail){ $count++;}
if($count == 0){echo "You do not have any shared emails";}

$j = 0;
foreach ($regEmails as $regEmail){
    $emailPhoto = $regEmail['code'];
    $emailSend = $regEmail['email'];
    $emailDate = $regEmail['fecha'];
    $emailDate = date("F d, Y | H:i", strtotime($emailDate));

    $id = "p" . $j;
    $j++;
    
    $html .= <<<HTML
    <ul class='regEmailUL'>
        <li style='width:32%' title='Sended Photo' class='link' onclick='viewPhoto($emailPhoto.jpg);'>
            $emailPhoto
        </li>
        <li style='width:32%;' title='Email'>
            $emailSend
        </li>
        <li style='width:32%;' title='Date'>
            $emailDate
        </li>
    </ul>
HTML;
}

$html .=  "</div>";

echo $html;

$s = "emails";
$color="#91C0D3";
include '../../pagescount.php';
