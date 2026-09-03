<?
    require_once "../../common/global.php";
    
    $usbs_order = $_REQUEST['order'];

    $_SESSION['usbs_order'] = $usbs_order;

    header("Location:../../rental/usbs");

?>