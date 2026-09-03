<?php
include '../../sessio.php';
//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$ID = $_POST['id'];
$type = $_POST['menu'];
if($type == 1){
?>
		
<?php
}
if($type == 2){
?>
		
			
				<img src='images/icons/submenu/myInfo.png' class='dMenuSelected2' <?php echo "onclick='profile(\"owner\" , \"info\" , $ID)'";?> id='info'>
				<img src='images/icons/submenu/address.png' class='dMenu2' <?php echo "onclick='profile(\"owner\" , \"addresses\" , $ID)'";?> id='addresses'>
				<img src='images/icons/submenu/contact.png' class='dMenu2' <?php echo "onclick='profile(\"owner\" , \"contacts\" , $ID)'";?> id='contacts'>
                                


<?php
                    if($_SESSION['USERTYPE']==1 || $_SESSION['USERTYPE']==2 || $_SESSION['USERTYPE']==6){
                    ?>
                             <img src='images/icons/submenu/photobooths_owner.png' class='dMenu2' <?php echo "onclick='profile(\"owner\" , \"PhotoBooths\" , $ID)'";?> id='PhotoBooths_2'>
			     <img src='images/icons/submenu/events_owner.png' class='dMenu2' <?php echo "onclick='profile(\"owner\" , \"Events\" , $ID)'";?> id='Events_2'>   
                    <?php
                    }
                    

}

?>