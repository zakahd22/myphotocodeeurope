<?php

//$paypalurl = "https://www.paypal.com/cgi-bin/webscr";//20140313
$paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr"; //20140313
// $emailSeller = "2UUQDJ7DL2UTS";//20140314
$emailSeller = "joancorominaslozano@gmail.com";
$comanda_id = $_POST['com'];
$n2 = $_POST['n2'];
$currency_code_paypal= $_POST['curr'];
$tt= $_POST['at'];
$product_name = $_POST['productName'];
$shop = $_POST['s'];
echo "
  <form id='paypalform'name='paypalform' action=\"$paypalurl\" method=\"post\">
<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
<input type=\"hidden\" name=\"business\" value=\"$emailSeller\">
<input type=\"hidden\" name=\"notify_url\" value=\"https://www.myphotocode.com/shop/comanda_check_sandBox.php\">
<input type=\"hidden\" name=\"cancel_return\" value=\"https://www.myphotocode.com/shop/comandaKO.php?c=$comanda_id&s=$shop\">
<input type=\"hidden\" name=\"return\" value=\"https://www.myphotocode.com/shop/comandaOk.php?c=$comanda_id&s=$shop\">
<input type=\"hidden\" name=\"lc\" value=\"EN\">
<input type=\"hidden\" name=\"item_name\" value=\"$product_name\">
<input type=\"hidden\" name=\"item_number\" value=\"$comanda_id-$n2\">
<input type=\"hidden\" name=\"amount\" value=\"$tt\">
<input type=\"hidden\" name=\"currency_code\" value=\"$currency_code_paypal\">
<input type=\"hidden\" name=\"button_subtype\" value=\"services\">
<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest\">

<input type=\"hidden\" name=\"address_override\" value=\"1\">
<input type=\"hidden\" name=\"no_shipping\" value=\"1\">
</form>";
?>
