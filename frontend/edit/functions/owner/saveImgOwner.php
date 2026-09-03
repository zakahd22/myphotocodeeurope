<?php

$ID = $_POST['id'];
if (file_exists("../../../images/ownerIMG/tmp/" . $ID . ".jpg")) {
    if (copy("../../../images/ownerIMG/tmp/" . $ID . ".jpg", "../../../images/ownerIMG/" . $ID . ".jpg")) {
        unlink("../../../images/ownerIMG/tmp/" . $ID . ".jpg");
        echo "OK";
    } else {
        echo "NO";
    }
} else {
    echo "NO";
}
?>
