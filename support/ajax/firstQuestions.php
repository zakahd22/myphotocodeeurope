<?php

include '../sessio.php';
include '../conexio.php';
$i = 1;
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT fq.question_id , b.id as BID , b.name  FROM SAT_firstquestion fq RIGHT JOIN CLD_boothTypes b ON b.id = fq.boothType");

while ($CLD_CON->FetchArray()) {
    $questionID = $CLD_CON->GetArrayField("question_id");
    $model = $CLD_CON->GetArrayField("BID");
    $name = $CLD_CON->GetArrayField("name");
    if (empty($questionID)) {
        $questionID = 0;
    }
    echo "<div style='width:100%;float:left;display:block;'>";
    echo "<div style='display:inline;float:left;width:47%;height:100%;margin-left:3%;'>";
     echo "<p class='title1'>$name</p>";
    echo "<img src='https://myphotocode.com/support/images/pb/$model.png'>";
    echo "</div>";
    echo "<div id='booth$model'style='display:inline;float:left;width:49%;' >";
    echo "<p class='title2'>First Question</p>";
    $CLD_CON2->OpenRs("SELECT question FROM SAT_questions WHERE id=$questionID");
    if ($CLD_CON2->FetchArray()) {
        echo "<p class='text'>" . stripslashes($CLD_CON2->GetArrayField("question")) . "</p>";
        echo "<p class='title2'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(3 , \"$model\")'>Set first question</span><p>";
    } else {
        if ($questionID == 999999) {
            echo "<p class='text'>Unsolved Form</p>";
            echo "<p class='title2' ><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(3 , \"$model\")'>Set first question</span><p>";
        } else {
            echo "<p class='text'>No question Assigned</p>";
            echo "<p class='title2' ><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(3 , \"$model\")'>Assign First Question</span><p>";
        }
    }

    echo "</div>";
    echo "</div>";
    
    echo "<hr>";
    $i++;
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
