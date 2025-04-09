<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';


$ID = $_POST['id'];
echo "<div class='inContent'>";
echo "<h1 style='margin-left:0px;'>Shared E-mails</h1>";
echo "<p style='margin-left:0px;'>Type the text you want to appear in the E-mail when your customers send their photos via E-mail.</p>"; 
//<br>The keyword #PoV# will be changed to video or photo depending on what your customers are sharing.

$subject = "Hey, take a look at this photo that I took at a DC Photobooth.";
$text1 = "Check this out!  I took this photo at a DC Photobooth";
$text2 = "Come visit our DC Photobooth.";

$CLD_CON->OpenRs("SELECT text FROM CLD_emailsText WHERE type=0 AND event=$ID");
if ($CLD_CON->FetchArray()) {
    $subject = $CLD_CON->GetArrayField("text");
}
$CLD_CON->OpenRs("SELECT text FROM CLD_emailsText WHERE type=1 AND event=$ID");
if ($CLD_CON->FetchArray()) {
    $text1 = $CLD_CON->GetArrayField("text");
}
$CLD_CON->OpenRs("SELECT text FROM CLD_emailsText WHERE type=2 AND event=$ID");
if ($CLD_CON->FetchArray()) {
    $text2 = $CLD_CON->GetArrayField("text");
}
echo "<div style='width:48%;display:inline;float:left;'>";
echo "<div style='width:100%;height:20%;'>";
echo "<p >Subject E-mail :</p>"; 
echo "<textarea id='text0v' style='width:95%;height:65%;margin-left:12px;' class='areaText' onblur='changeTextos(this,0,$ID);'>$subject</textarea><input type='hidden' id='text0' value='$subject'>";
echo "</div>";
echo "<div style='width:100%;height:25%;'>";
echo "<p>Text 1 (Header E-mail):</p>";
echo "<textarea id='text1v' style='width:95%;height:65%;margin-left:12px;' class='areaText' onblur='changeTextos(this,1,$ID);'>$text1</textarea><input type='hidden' id='text1' value='$text1'>";
echo "</div>";
echo "<div style='width:100%;height:25%;'>";
echo "<p>Text 2 (Footer E-mail):</p>";
echo "<textarea id='text2v' style='width:95%;height:65%;margin-left:12px;' class='areaText'  onblur='changeTextos(this,2,$ID)';>$text2</textarea><input type='hidden' id='text2' value='$text2'>";
echo "</div>";

echo "</div>";
echo "<div style='width:48%;display:inline;float:left;'>";
echo "<div style='width:100%;height:20%;'>";
echo "<img src='images/web/templateEmail.png' style='width:90%;margin-top:58px;margin-left:1%;'>";
echo "</div>";

echo "</div>";


?>

<!--<script>
   

    function changeTextos(text, type, event) {
        var c;
        switch (type) {
            case 0:
                c = "<?php echo $subject; ?>";
                break;
            case 1:
                c = "<?php echo $text1; ?>";
                break;
            case 2:
                c = "<?php echo $text2; ?>";
                break;
        }
        if ($(text).val() !== c) {
            var txt = $(text).val();
            var ajaxData = {id: event, texto: txt, type: type};
            $.ajax({
                url: 'sections/events/functions/setEmailText.php',
                type: 'POST',
                success: function(data) {
                    if (data == "OK") {
                        switch (type) {
                            case 0:
                                t=c;
                                break;
                            case 1:
                                t1=c;
                                break;
                            case 2:
                                t2=c;
                                break;
                        }
                    } else {
                        error(data);
                    }
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }

    }
    

</script>-->
