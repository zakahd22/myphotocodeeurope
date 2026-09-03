<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();

$baseController->createModel('InstagramSuggestions');


//$result = "Ko";
$result = "Ok"; //si en el foreach en falla avisem?


$json = json_decode($_POST["dades"], TRUE);

for($i=0; $i< count($json); $i++){
    if ($json[$i]['name']){
        $array[$json[$i]['name']] = $json[$i]['value'];
    }
}

if($array["words"]){  

    $type = $array["type"];
    $words = str_replace("#","",$array["words"]);
    $words = str_replace("@","",$words);
    $wordsArray = array_filter(explode(" ", $words));
    
//    print_r($wordsArray);exit;
    //TODO: aqui haurem de tallar i fer un foreach
    foreach($wordsArray as $word){
//        $baseController->entity->loadEntity('InstagramSuggestions');
//
//        $baseController->entity->setValue("word", $word);
//        $baseController->entity->setValue("type", $type);
//
//
//        $suggestion  = $baseController->InstagramSuggestionsModel->insertInstagramSuggestions();
        $CLD_CON->Execute("INSERT INTO InstagramSuggestions (word, type) VALUES('$word' , '$type')");
        
       // $mysqli->set_charset("utf8");

        
        
        
//        if(!$suggestion){
//        
//            $result = "Ko";
//        }

    }
    


//    if($suggestion){
//        
//        $result = "Ok";
//    }
}
echo $result;