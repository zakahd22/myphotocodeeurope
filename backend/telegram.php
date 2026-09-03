<?php 

require 'vendor/autoload.php';
use Telegram\Bot\Api;
echo "telegram";
$telegram = new Api('563225064:AAFKtxhim_0kfCbymRLjpaUrOYWVJozn6k0');
$token = '563225064:AAFKtxhim_0kfCbymRLjpaUrOYWVJozn6k0';
$chat_id = '@photobooth_provesbot';
$text = 'Hello World';

$response = file_get_contents("https://api.telegram.org/bot".$token."/sendMessage?chat_id=".$chat_id."&text=".$text);
//https://api.telegram.org/bot563225064:AAFKtxhim_0kfCbymRLjpaUrOYWVJozn6k0/sendMessage?chat_id=@photobooth_provesbot&text='Hello World'
