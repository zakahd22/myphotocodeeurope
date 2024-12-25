<?
    require_once "../../common/global.php";
    include G_PATH.'common/general.php';

    $usb_id = $_REQUEST['usb'];

    $usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));

    if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");

    $usb_ref = $usb['creation_date'].$usb['id'];

    unlink(G_PATH."usbs/".$usb_ref."/PhotoIdUpload/BGmusic.mp3");			

    mysql_query("UPDATE usbs SET bgmusic=0 WHERE id=$usb_id");

    header("Location:../../rental/usbs/edit/".$usb_ref."#bgmusic");
?>