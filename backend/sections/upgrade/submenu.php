<?php
//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
// ID
// Passem 999 per a consultant

$ID = $_POST['id'];

$type = $_POST['menu'];
if($ID==999){
    $type=0;
}
if($type == 1){
?>
	<img src='images/icons/submenu/addNewComponents.png' class='dMenu2'  style='float: right;z-index: 10;position: absolute;right: 0px;' onClick='addNew(5 , 0);'>	
<?php
}
if($type == 2){
?>
		
			
	<img src='images/icons/submenu/infoComponents.png' class='dMenuSelected2' <?php echo "onclick='profile(\"upgrade\" , \"Info\" , $ID)'";?>>
			

                    
<?php
}

?>