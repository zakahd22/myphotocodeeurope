<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

echo "<div class='inContent'>";
echo "<p>Introdueix el numero de serie del PhotoBooth : <input type='text' class='textInput' style='width:50%;' id='sn'> <img src='images/web/ok.png' style='margin-top:2px;margin-right:5px;cursor:pointer;' title='SOLVED' onclick='cargarForm();'></p>";
echo "<hr>";
echo "<div style='width:100%' id='componentsForm'></div>";
echo "</div>";
?>

<script>
    function cargarForm() {
        var sn = $("#sn").val();
        if (sn.length < 13 || sn.length > 13) {
            alert("Escriu el numero de serie correctament siusplau. El numero de serie es composa de 13 numeros. (Ex : 0022000220001)");
        } else {
            
            var ajaxData = {sn : sn};
            $.ajax({
                url: 'sections/fiproducte/functions/getForm.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    $("#componentsForm").html(data);
                },
                // Form data
                cache: false,
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }

    }
</script>