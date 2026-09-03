<?php

$baseController->createModel('InstagramSuggestions');
//TODO: acabar de substituir financing per instagram
$suggestion = $baseController->InstagramSuggestionsModel->getInstagramSuggestionsByWordTypePais($word, $type, $pais);

if($suggestion){
//    $dateAct = $dongle[0]["dateAct"];
}

//if($dateAct){ include './forms/instagram/formSuperEditAct.php';}
//else{ include './forms/instagram/formSuperEditNoAct.php';}

