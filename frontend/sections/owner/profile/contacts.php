<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
$ID = $_POST['id'];


$c = 1;
echo "<div class='inContent'>";
echo "<h1> Contacts <input type='button' class='miniAdd' onclick='edit(36 , $ID);'></h1>";
echo "<p>In this section you can add useful contacts.</p>";
$CLD_CON->OpenRs("SELECT * FROM CLD_Contactes WHERE rental_id=$ID ORDER BY name");
while ($CLD_CON->FetchArray()){
    $contacte_id = $CLD_CON->GetArrayField("id");
    $name = $CLD_CON->GetArrayField("name") . " " . $CLD_CON->GetArrayField("surnames");
    $phone = $CLD_CON->GetArrayField("phone");
    $email = $CLD_CON->GetArrayField("email");
    $jobRank = $CLD_CON->GetArrayField("carrec");
    $zonaHoraria = $CLD_CON->GetArrayField("city");
    $preference = $CLD_CON->GetArrayField("preference");
    echo "<div class='contacte'>";
    echo "<h3> $name , $jobRank</h3>";
    echo "<p> Email : $email</p>";
    echo "<p> Phone : $phone </p>";
    echo "<p> TimeZone : $zonaHoraria</p>";
    echo "<input type='button' class='editButton' style='float:right;bottom:25px;' onClick='edit(4 , $contacte_id);'>";
    echo "<input type='button' class='miniTrash' style='float:right;bottom:22px;' onClick='deleteContacts($contacte_id)'>";
     if($preference==1){
        echo "<img src='images/web/preferenceOK.png' style='position:absolute;top:10px;right:10px;width:36px;height:36px;' title='PRIMARY CONTACT'>";
    }else{
        echo "<img src='images/web/preferenceNoOk.png' style='position:absolute;top:10px;right:10px;cursor:pointer;' onclick='setToPrimary($contacte_id , $preference , $ID);' title='SET TO PRIMARY CONTACT'>";
    }
    echo "</div>";
    
    $c++;
}

echo "</div>";
?>
<script> 
    function deleteContacts(id){

       if(confirm("You are sure you want to delete this contact?")){
                var ajaxData = {id: id};
                $.ajax({
                    url: 'sections/owner/functions/deleteContacts.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {                           
                            closePopup();
                            profile("owner", "contacts", <?php echo $ID;?>);
                        } else {
                            alert(data);
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
       }
    }
    function setToPrimary(id , p , o){
      var ajaxData = {id: id , preference : p , owner : o};
                $.ajax({
                    url: 'sections/owner/functions/setToPrimaryContact.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {                           
                            closePopup();
                            profile("owner", "contacts", o);
                        } else {
                            alert(data);
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
    
    }
</script>