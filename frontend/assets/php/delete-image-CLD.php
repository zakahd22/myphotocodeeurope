<?

include '../../sessio.php';
include '../../conexio.php';


$id = $_REQUEST['id'];
switch ($id) {

    case "logo" :
        $usb_id = $_REQUEST['usb_id'];
        $CLD_CON->OpenRs("SELECT creation_date FROM usbs WHERE id = $usb_id");
        if ($CLD_CON->FetchArray()) {
            $usb_ref = $CLD_CON->GetArrayField("creation_date") . "" . $usb_id;
            if (file_exists("../../usbs/" . $usb_ref . "/PhotoIdUpload/Logo.jpg")) {
                unlink("../../usbs/" . $usb_ref . "/PhotoIdUpload/Logo.jpg");
            }

            $CLD_CON->Execute("UPDATE usbs SET logo=0 WHERE id=$usb_id");
        }
        echo $usb_id;
        break;

    case "banner" :

        $usb_id = $_REQUEST['usb_id'];
               $usb_id = $_REQUEST['usb_id'];
        $CLD_CON->OpenRs("SELECT creation_date FROM usbs WHERE id = $usb_id");
        if ($CLD_CON->FetchArray()) {
            $usb_ref = $CLD_CON->GetArrayField("creation_date") . "" . $usb_id;
        if (file_exists("../../usbs/" . $usb_ref . "/PhotoIdEvents/Wedding/Header/1.jpg")) {
        unlink("../../usbs/" . $usb_ref . "/PhotoIdEvents/Wedding/Header/1.jpg");
        }
       $CLD_CON->Execute("UPDATE usbs SET banner=0 WHERE id=$usb_id");
        }
       echo $usb_id;

        break;

    case "background" :

        $event = $_REQUEST['event'];
        $event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$event"));
        $event_ref = $event['start_date'] . $event['id'];

        unlink("../../events/" . $event_ref . "/background.jpg");

        header("Location:" . $baseUrl . "/rental/events/edit/" . $event_ref);

        break;
}
?>