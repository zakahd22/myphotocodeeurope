<?php

require_once 'common/global.php';
require_once G_PATH . 'common/conexio.php';

$json = json_decode($_POST["data"], TRUE);

$code = $json[0];
$pass = $json[1];

$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT user , userType , datef FROM CLD_forgot_pws WHERE code='$code'");
if ($CLD_CON->FetchArray()) {
    $user = $CLD_CON->GetArrayField("user");
    $userType = $CLD_CON->GetArrayField("userType");
    $CLD_CON2->Execute("UPDATE CLD_Login SET password='$pass' WHERE id_user=$user AND userType=$userType");
    if ($userType == 4) {
        $CLD_CON2->Execute("UPDATE rentals SET password='$pass' WHERE id=$user");
    }
    echo "The password has been successfully changed, please go to the <a href='$URL_BASE'>home page</a> and login.";
} else {
    echo "There is no request to change the password of this user.";
}
?>
