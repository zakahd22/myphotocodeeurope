<?php
/* 
 * Retorna owners en funció ajax
 */
include 'sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('rentals');
?>
<div id="name-list">
                 <!-- <div class="rowOwnerSuggest" onClick="selectname('','');">Show All Owner's PhotoBooths</div>-->
<?php 
// here we search if term exist and then process the below lines of code
if(!empty($_POST["searchterm"])) 
{
    // the query responsible for fetch matched data
        $rentals = $baseController->rentalsModel->getRentalsListByNameOrUsername($_POST["searchterm"]); 
        ?>
        
        <?php 
        if(!empty($rentals)) {
            // prepare the list for append
        ?>
                
                    
                <?php
                foreach($rentals as $rental){
                ?>
                    <div class="rowOwnerSuggest" onClick="selectname('<?php echo $rental["id"]; ?>','<?php echo $rental["name"]; ?>');"><?php echo $rental["name"]." ".$rental["username"]; ?></div>
                <?php 
		} 
        }        
		?>
               
        <?php  
} 
?>
 </div>