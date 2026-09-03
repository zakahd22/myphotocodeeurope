<?php
include '../sessio.php';
include '../conexio.php';
?>
<p style='margin-top:40px;'></p>
<p class='first title2' > Solution Code </p>
<select id='solutionCode' class='first'>
    <?php
     echo "<option value='0'> -- </option>";
    $CLD_CON->OpenRs("SELECT id FROM SAT_solutions");
    while($CLD_CON->FetchArray()){
        $ID = $CLD_CON->GetArrayField("id");
        echo "<option value='$ID'> S$ID </option>";
    }
    ?>
    
</select>
<hr/>
<p class='first title2' > Solution text </p>
<textarea id='solutionFilter' class='first' rows='4'></textarea>
<hr/>
<p class='first title2'> Next Question</p>
<div type='text' id='answersNumeberF' style='border:2px solid darkblue;left:9%;position:absolute;width:86%;height:150px;overflow-x:hidden;overflow-y:auto;' size='6'> 
    <ul id="qsts" style="width:100%;list-style: none;margin:0px;padding:0px;">
    <li  value ='0' style='background-color:darkcyan;color:white;border-bottom:1px solid grey;text-align:center;cursor:pointer;'> --none-- </li>
    <?php
    $CLD_CON->OpenRs("SELECT id , question FROM SAT_questions ORDER BY question");
    while($CLD_CON->FetchArray()){
        $id = $CLD_CON->GetArrayField("id");
        $question = stripcslashes($CLD_CON->GetArrayField("question"));
        echo "<li value='$id' style='border-bottom:1px solid grey;text-align:center;cursor:pointer;color:darkcyan;'>";
        echo wordwrap("Q".$ID." - ".$question, 30, "<br>", 1);
        
        echo"</li >";
    }
    
    ?>
</div>
