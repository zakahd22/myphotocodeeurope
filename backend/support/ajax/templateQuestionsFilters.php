<?php
include '../sessio.php';
include '../conexio.php';
?>
<p style='margin-top:40px;'></p>
<hr>
<p class='first title2' > Solution Code </p>
<select id='questionCode' class='first'>
    <?php
     echo "<option value='0'> -- </option>";
    $CLD_CON->OpenRs("SELECT id FROM SAT_questions");
    while($CLD_CON->FetchArray()){
        $ID = $CLD_CON->GetArrayField("id");
        echo "<option value='$ID'> Q$ID </option>";
    }
    ?>
    
</select>
<hr/>
<p class='first title2' > Question </p>
<textarea id='questionFilter' class='first' rows='4'></textarea>
<hr>
<p class='first title2'> Answer </p>
<textarea id='answerFilter' class='first' rows='4'></textarea>
<hr>
<p class='first title2'> Number answers</p>
<select type='text' id='answersNumeberF' style='left:9%;position:absolute;'> 
    <option value='-1'> --none-- </option>
    <option value='0'> 0 answers</option>
    <option value='1'> 1 answers</option>
    <option value='2'> 2 answers</option>
    <option value='3'> 3 answers</option>
    <option value='4'> 4 answers</option>
    <option value='5'> 5 answers</option>
    <option value='6'> 6 answers</option>
    <option value='7'> 7 answers</option>
    <option value='8'> 8 answers</option>
    <option value='9'> 9 answers</option>
    <option value='10'> 10 answers</option>
    <option value='11'> 11 answers</option>
    <option value='12'> 12 answers</option>
    <option value='13'> 13 answers</option>
    <option value='14'> 14 answers</option>
    <option value='15'> 15 answers</option>
    <option value='16'> 16 answers</option>
    <option value='17'> 17 answers</option>
    <option value='18'> 18 answers</option>
    <option value='19'> 19 answers</option>
    <option value='20'> 20 answers</option>
</select>