<?php
//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$type = $_POST['menu'];
$ID = $_POST['id']; //OWNER ID
if($type == 2){
?>
<img src='images/icons/submenu/film.png' class='dMenuSelected2' <?php echo "onclick='profile(\"alerts\" , \"paperAlert\" , $ID)'";?>>
<img src='images/icons/submenu/cash.png' class='dMenu2' <?php echo "onclick='profile(\"alerts\" , \"moneyAlert\" , $ID)'";?>>
<img src='images/icons/submenu/offline.png' class='dMenu2' <?php echo "onclick='profile(\"alerts\" , \"offlineAlert\" , $ID)'";?>>
<img src='images/icons/submenu/printer.png' class='dMenu2' <?php echo "onclick='profile(\"alerts\" , \"printerError\" , $ID)'";?>>
<!--<img src='images/icons/submenu/paper.png' class='dMenu2' <?php echo "onclick='profile(\"alerts\" , \"paperError\" , $ID)'";?>>-->
<img src='images/icons/submenu/camera.png' class='dMenu2' <?php echo "onclick='profile(\"alerts\" , \"cameraError\" , $ID)'";?>>
<img src='images/icons/submenu/ControlBoard.png' class='dMenu2' <?php echo "onclick='profile(\"alerts\" , \"boardError\" , $ID)'";?>>

		
<?php
}

?>