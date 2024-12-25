<?php

//$code = 'PVGVKF76EV';
require_once "../../common/global.php";
require_once G_PATH . 'common/conexio.php';
$code=$_GET['code'];
echo "La foto encara no esta disponible vols rebre una notificacio quan estigui disponible?";
?>
<script src='https://code.jquery.com/jquery-1.9.1.js'></script>
<script src='https://code.jquery.com/ui/1.10.1/jquery-ui.js'></script>
<!--<script src='totalshare/functions.js'></script>-->
<body>
    <div>
        <input type='button' onclick='avisaSMS();' id='si' value='send SMS'>
        <input type='button' onclick='avisaMail();' id='si' value='send Email'>
        <button onclick="location.href='//https://www.myphotocode.com'" type="button">Cancel</button>
    </div>
    <div id='dades'>
        <div style='display: none' id='sms'>
            <!--<form >-->
                <input style="width: 55px;" type='text' id='pref' value="+34">
                <input  type='text' id='txt'>
                <input type='submit' onclick='envia("<?php echo $code; ?>", 1);' id='enviasms' value='envia'>
            <!--</form>-->
        </div>
        <div style='display: none' id='mail'>
            <input  type='text' id='txtmail'>
            <input type='button' onclick='envia("<?php echo $code; ?>", 0);' id='enviamail' value='envia'>
        </div>
    </div>
    <div id='complet' style='display: none'> You will receive a message when your photo is online</div>
</body>
<script> 
function avisaSMS(){
    $('#sms').show();
    $('#mail').hide();
}

function avisaMail(){
    $('#mail').show();
    $('#sms').hide();
}

function envia(code, metode){
//    alert(1);
    dades = document.getElementById('txt').value;
    if (dades == ""){
        dades = document.getElementById('txtmail').value;
        var array = [code, metode, dades, "web"];
    }else{
        pref = document.getElementById('pref').value;
        var array = [code, metode, dades, "web", pref];
    }
    var data = JSON.stringify(array);
    alert(data);
    $.ajax({
        url: 'gestor.php',
        type: 'POST',
        dataType: 'html',
        data: {
            data : data,
        },
        success: function(){
            $('#complet').show();
            setTimeout("location.href='https://www.myphotocode.com'",5000)
        }
    });
}
    
</script>




