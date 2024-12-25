<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$stringBrut = "App_usbStock
App_usbStockMaterial
Appusr_user
Appusr_userNot
Appusr_userPhoto
Appusr_userVotes
CLD_Contactes
CLD_Distributors
CLD_EventsManegers
CLD_In_Aparallament
CLD_Inc_coments
CLD_Incidents
CLD_Login
CLD_Productions
CLD_Servers
CLD_banners
CLD_boothTypes
CLD_boothsComponents
CLD_components
CLD_emailsText
CLD_estadistiques_photos
CLD_estadistiques_upload
CLD_event_booths
CLD_forgot_pws
CLD_historyBooth
CLD_historyComponents
CLD_ownerConnections
CLD_productionBooth
CLD_questions
CLD_questions_emails
CLD_subDistributors
CLD_timesBanners
CLD_typeComponents
FacebookUsers
Fcode_dongle
Fcode_reg
InstagramPhotoViewed
InstagramReportedPhotos
InstagramSuggestions
InstagramUsers
Mtr_info
Mtr_orders
Pay_print_dongle
Pay_print_order
Pay_print_sessions
SAT_answers
SAT_firstquestion
SAT_media
SAT_problems
SAT_problemsquestions
SAT_questions
SAT_solutions
SHP_Comandes
SHP_Comandes_Products
SHP_Contacts
SHP_Shops
SHP_address
SHP_caracteristiques
SHP_ch_options
SHP_cm_pr_ch
SHP_currency
SHP_products
booth_types
booths
collages
config
event_backgrounds
event_frame
events
frames
ftp_folders
gestor
manuals
manualsBooths
manualsItems
nombre
 
photo_Files
photos
registre_emails
rentals
temporal
usbs
view_EventInfo
view_Photos";

$type = $array["type"];
//    $words = str_replace("#","",$array["words"]);
//    $words = str_replace("@","",$words);
$cadenalimpia = preg_replace("[\n|\r|\n\r]", "` ENGINE INNODB;  ALTER TABLE `dbs326856`.`", $stringBrut);

print $cadenalimpia;



exit;
//    $wordsArray = array_filter(explode(" ", $cadenalimpia));
//
//foreach($wordsArray as $word){
//    if(substr($word,0,1) == '@'){
//        print $word." ";
//    }
//}