<?
    require_once "../../common/global.php";
	
    $events_order = $_REQUEST['order'];

    $_SESSION['events_order'] = $events_order;

    header("Location:../../rental/events");

?>