<form id='noSolvedForm'>
    <p style='border:1px solid darkblue; padding:5px; margin-top: 50px;margin-bottom: 30px;' class='text'>        
        We are not able to a solution to your issue via our support application. Please fill in the form below and a support representative will contact you as soon as possible.
    </p>
    <div style='width: 100%;display:block;float: left;margin-bottom: 15px;'>
    <img src ="https://myphotocode.com/support/images/title/nameT.png" class='imgTitle' >
    <input type="text" name='contact' id='contactName' class='textInput'/>
    </div>
    <div style='width: 38.5%;display:inline;float: left;margin-bottom: 15px;'>
    <img src ="https://myphotocode.com/support/images/title/emailT.png" class='imgTitle'>
    <input type='text' name='email' id='contactEmail' style='width: 90%;' class='textInput' />
    </div>
    <div style='width:49%;display: inline;float: left;margin-bottom: 15px;'>        
    <img src ="https://myphotocode.com/support/images/title/phone.png" class='imgTitle' >
    <input type='text' name="telefon" id='contactPhone' style='width: 74%;' class='textInput'/>
    </div>
    <div style='width:100%;display: block;float: left;'>
    <img src ="https://myphotocode.com/support/images/title/comments.png" class='imgTitle' style='margin-top: 0px;'>
    <textarea name="comments" id='comments' class='areaText'></textarea>
    </div>
    <div style='width: 100%;display:block;float: left;margin-bottom: 15px;'>
    <p id='error' style='text-align:center;display:block;width:100%;' class='text'></p>
    </div>
</form>
<?php
$finish = "<input  type='button' class='sendEmail' onclick='Solved($problema , 0);'>";
$last = "<input type='button' class='lastQuestion' onclick='lastQuestion()'>";
?>
