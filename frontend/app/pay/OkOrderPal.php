<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Thank you for your payment</title>


</head>

<body>
<h1>Payment completed</h1>
<p>Thank you for your payment. You can close this page.</p>
        <?php
            $ara = new DateTime("now");
            $f = fopen('./logPal.txt', 'a+');
            fwrite ($f, "\r\n" . $ara->format("Ymd H:i:s") . "OkOrderPal\r\n");
            fclose ($f);
        ?>

</body>
</html>
