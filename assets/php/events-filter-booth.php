<?
    require_once "../../common/global.php";
    
    $events_filter_booth = $_REQUEST['booth'];

    $_SESSION['events_filter_booth'] = $events_filter_booth;

    header("Location:../../rental/events");

?>