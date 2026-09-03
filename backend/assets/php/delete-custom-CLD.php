<?

include '../../sessio.php';
include '../../conexio.php';

$usb_id = $_REQUEST['usb_id'];
$set = $_REQUEST['set'];
$CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $usb_id");
if ($CLD_CON->FetchArray()) {
    $usb_ref = $CLD_CON->GetArrayField("creation_date") . "" . $usb_id;
    if (file_exists("../../usbs/" . $usb_ref . "/PhotoIdEvents/CustomShots/" . $set . ".jpg")) {
        $file = "../../usbs/" . $usb_ref . "/PhotoIdEvents/CustomShots/" . $set . ".jpg";
        unlink($file);
    }

    $CLD_CON->Execute("UPDATE usbs SET custom$set=0 WHERE id=$usb_id");

    for ($x = ($set + 1); $x < 12; $x++) {
        $custom = $CLD_CON->GetArrayField("custom$x");
        if ($custom != 0) {

            $prev = $x - 1;

            $file = "../../usbs/" . $usb_ref . "/PhotoIdEvents/CustomShots/" . $x . ".jpg";
            if (file_exists($file))
                rename($file, "../../usbs/" . $usb_ref . "/PhotoIdEvents/CustomShots/" . $prev . ".jpg");
                 
            
            $CLD_CON->Execute("UPDATE usbs SET custom$prev=1 WHERE id=$usb_id");
            $CLD_CON->Execute("UPDATE usbs SET custom$x=0 WHERE id=$usb_id");
        }
    }
}
echo $usb_id;
?>