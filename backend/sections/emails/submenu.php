<?php
include '../../sessio.php';
//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$type = $_POST['menu'];
$ownerID = $_SESSION['USERID'];

if($type == 1){
?>
    <img src='images/icons/submenu/sharedemail_e.png' class='dMenuSelected2' onclick='setSection("emails" , 1 );' >
    <img src='images/icons/submenu/questions_e.png' class='dMenu2' onclick='setSection("emails" , 2 , <?php echo $ownerID;?>);' >
    <img src='images/icons/submenu/download.png' class='dMenu2' onclick='downloadAllEmails(<?php echo $ownerID;?>);' style='float: right;z-index: 10;position: absolute;right: 0px;' id='dwl'>		
<?php
}




if($type == 2){
    
?>
<img src='images/icons/submenu/sharedemail_e.png' class='dMenu2'  onclick='setSection("emails" , 1 );' >
                <img src='images/icons/submenu/questions_e.png' class='dMenuSelected2' onclick='setSection("emails" , 2 , <?php echo $ownerID;?>);' >
                
		<img src='images/icons/submenu/download.png' class='dMenu2' onclick='downloadAllQuestionsEmails(<?php echo $ownerID;?>);' style='float: right;z-index: 10;position: absolute;right: 0px;' id='dwl'>
             

		
<?php
}
/*
 * <img src='images/icons/submenu/sharedemail_e.png' class='dMenuSelected2' onclick='setSection("emails" , 1 );' >
<img src='images/icons/submenu/questions_e.png' class='dMenu2' onclick='profile("emails" , "QuestionsEmails" , <?php echo $ownerID;?>);' >
 * 		<img src='images/icons/submenu/download.png' class='dMenu2' onclick='downloadAllQuestionsEmails(<?php echo $ownerID;?>);' style='float: right;z-index: 10;top: -33px;position: absolute;right: 0px;' id='dwl'>

 */
?>
