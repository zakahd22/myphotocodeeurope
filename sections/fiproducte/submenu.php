<?php
include '../../sessio.php';
//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$type = $_POST['menu'];
$ID = $_POST['id'];
if ($type == 1) {
    ?>

    <?php
}
if ($type == 2) {
    ?>
    <img src='images/icons/submenu/addComponents.png' class='imgMenu2' <?php echo "onclick='edit(61 , 0);'"; ?>>

    <?php
}
?>