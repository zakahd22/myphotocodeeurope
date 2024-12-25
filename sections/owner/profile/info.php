<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once "../../../common/global.php";
$ID = $_POST["id"];


$baseController = new baseController;
$baseController->createModel('rentals');
$baseController->createModel('CLD_Login');
$baseController->createModel('CLD_ownerConnections');

$rentals = $baseController->rentalsModel->getRentalsById($ID);

if ($rentals) {
    $companyName = stripslashes($rentals[0]["name"]);
    $alertEmail = $rentals[0]["App_email"];
    $validatedAlertEmail = $rentals[0]["ValidatedAlertEmail"];
    $user = $rentals[0]["username"];
    $pwd = $rentals[0]["password"];
}

$profileIMG_ruta = "../../../images/ownerIMG/$ID.jpg";
if (file_exists($profileIMG_ruta)) {
    $rnd = rand(0, 800000) / rand(1, 5000);
    $profileIMG = "images/ownerIMG/$ID.jpg?version=$rnd;";
    $del = "<input type='button' class='miniTrash' style='float:right;bottom:35px;background-color:white;' onClick='deleteImgProfile($ID);'>";
} else {
    $profileIMG = "images/ownerIMG/noPimg.jpg";
    $del = "";
}

$html = "<div class='inContent'>";
$html .= "<div class='imgProfile'>";
$html .= "<img src ='$profileIMG'>";
if ($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 4) {
    $html .= $del;
    $html .= "<input type='button' class='editButton' style='float:right;bottom:35px;background-color:white;' onClick='edit(1 , $ID);'>";
}
$html .= "</div>";
$html .= "<div class='infOwnerContent'>";

$login = $baseController->CLD_LoginModel->getLoginWhereUserIdUserType($ID, 4);
$ban = $login[0]["banned"];
if (count($login) == 1) {
    if ($_SESSION['USERTYPE'] == 1) {
    if($ban == 0){
        $html .= "<div class='misatge2'> <div class='ban_text' style='background-color: #25d525; width: 173px; padding: 5px; text-align: center; border: 1px solid;'>unbanned user</div> "
                . "<input type='button' value='ban user' class='ban' onclick='banUser($ID, 0)' style='display: block; margin-left: 186px; margin-top: -37px;'></div>";
        
        $html .= "<div class='misatge' style='display: none' > <div class='ban_text' style='background-color: #d52525; width: 173px; padding: 5px; text-align: center; border: 1px solid;'>Banned user </div>"
                . "<input type='button' value='unban user' class='unban' onclick='banUser($ID, 1)' style='display: none; margin-left: 186px; margin-top: -37px;'></div>";
    }else{
        $html .= "<div class='misatge2' style='display: none;'> <div class='ban_text' style='background-color: #25d525; width: 173px; padding: 5px; text-align: center; border: 1px solid;'>unbanned user</div>"
                . "<input type='button' value='ban user' class='ban' onclick='banUser($ID, 0)' style='display: none; margin-left: 186px; margin-top: -37px;'></div>";
        
        $html .= "<div class='misatge' > <div class='ban_text' style='background-color: #d52525; width: 173px; padding: 5px; text-align: center; border: 1px solid;'>Banned user</div>"
                . "<input type='button' value='unban user' class='unban' onclick='banUser($ID, 1)' style='display: block; margin-left: 186px; margin-top: -37px;'></div>";
    }
}
if ($_SESSION['USERTYPE'] == 1) {
    $html .= "<h1>$companyName <input type='button' class='editButton' onClick='edit(31 , $ID);'></h1>";
} else {
    $html .= "<h1>$companyName</h1>";
}



    if ($_SESSION['USERTYPE'] == 1) {

        $html .= "<p> USERNAME : $user  <input type='button' class='editButton' onClick='edit(32 , $ID);'></p>";
    } else {
        $html .= "<p> USERNAME : $user </p>";
    }

    $redValidate = "";
    $validateText = "";
    if ($validatedAlertEmail == 0) {
        $redValidate = "style='color:red;' ";
        $validateText = "<p {$redValidate} >Email not validated</p>";
    }
    $html .= "<p {$redValidate}> ALERT-EMAIL : $alertEmail ";
    if ($_SESSION['USERTYPE'] != 6) {
        $html .=  "<input type='button' class='editButton' onClick='edit(2 , $ID);'></p>";
    }
    $html .= $validateText;
    $html .= "<p> PASSWORD: ************** ";
    if ($_SESSION['USERTYPE'] != 6) {
        $html .= "<input type='button' class='editButton' onClick='edit(3 , $ID);'>";
    }
    if ($_SESSION['USERTYPE'] == 1) {
        $html .= "<img src='images/web/miniEye.png' title='$pwd' style='position:relative;top: 7px;'>";
    }
    $html .= "</p>";

    if ($_SESSION['USERTYPE'] < 4 ) {
        if (!empty($alertEmail)) {
            $html .= "<input type='button' value='Resend Welcome Email' onclick='resendWelcomeEmail($ID);' class='resendWelcome'>";
        } else {
            $html .= "<p>It is not possible to send welcome e-mail because there is no alert e-mail</p>";
//            $html .= "<p>No se puede enviar welcome email porque no hay alerts email</p>";
        }
    }
} else {
    if ($_SESSION['USERTYPE'] < 4 ) {
        $html .= "<p> ALERT-EMAIL : $alertEmail <input type='button' class='editButton' onClick='edit(2 , $ID);'></p>";
        $html .= "<p> NO USER AND PASSWORD. <input type='button' class='editButton' onClick='edit(65 , $ID);'></P>";
    }
}

if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6) {
    $html .= "<h1>Last Connections</h1>";

    $ownerConections = $baseController->CLD_ownerConnectionsModel->getownerConnection($ID, 4);

    if (count($ownerConections) > 0) {
        foreach ($ownerConections as $ownerConection) {
            $data = $ownerConection["data"];
            $pais = $ownerConection["pais"];
            $state = $ownerConection["state"];
            $ciutat = $ownerConection["ciutat"];

            $data = date("F d, Y | H:i:s", strtotime($data));

            $html .= "<ul class='lastConections'><li style='width:50%;'>$data</li> <li style='width:50%;text-align:right;'> $ciutat , $state / $pais </li></ul>";
        }
    }
    if (count($ownerConections) == 0) {
        $html .= "<p style='margin-top:10px;'>No connections</p>";
    }
}




$html .= "</div>";
$html .= "</div>";

echo $html;

?>

<script>
    function resendWelcomeEmail(id) {
        loading();
        var ajaxData = {id: id};
        $.ajax({
            url: 'sections/owner/functions/resendWelcomeEmail.php',
            type: 'POST',
            success: function (data) {
                profile("owner", "info", id);
            },
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
    function banUser(ID, estat){
        var resposta = confirm("¿segur que el vols banejar?");
        if(resposta == true){
            var dades_user = [ID, estat];
            var data = JSON.stringify(dades_user);
            $.ajax({
                url: 'sections/owner/functions/banUser.php',
                dataType: 'html',
                type: 'POST',
                data: {
                    dades : JSON.stringify(dades_user)
                },
                success: function(data) {
                    if(estat == 0){
                        $('.ban').css("display", "none");
                        $('.unban').css("display", "block");
                        $('.misatge').css("display", "block");
                        $('.misatge2').css("display", "none");
                    }else{
                        $('.unban').css("display", "none");
                        $('.ban').css("display", "block");
                        $('.misatge2').css("display", "block");
                        $('.misatge').css("display", "none");
                    }
                }
            })
        }
    }
    
</script>