<?php
// Script que informa del total d'arxius ocupats al hosting
// checkspace v1.00.00
// Robert Casanovas robert@dc-image.com

require_once '../mailer/PHPMailerAutoload.php';
require_once '../mailer/class.phpmailer.php';
require_once '../mailer/class.smtp.php';

// Definimos el comando a ejecutar
$comando = 'find /kunden/homepages/46/d399659235/htdocs/ | wc -l';

// Ejecutamos el comando y obtenemos la salida
$salida = exec($comando);

// Convertimos la salida a un número entero
$salida = intval($salida);

// Calculamos la diferencia
$diferencia = 262144 - $salida;

//
$fetxa = date('Y-m-d H:t:s');

// Configuramos el correo electrónico
$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.ionos.com'; // Cambiar por el servidor SMTP de tu proveedor
$mail->Port = 465; // Cambiar por el puerto de tu servidor SMTP
$mail->SMTPSecure = 'ssl'; // Cambiar por la seguridad de tu servidor SMTP
$mail->SMTPAuth = true;
// $mail->SMTPDebug = true;
$mail->Username = 'noreply@myphotocode.com'; // Cambiar por tu correo electrónico
$mail->Password = 'd1g1t4lc3ntr3&'; // Cambiar por tu contraseña
$mail->setFrom('noreply@myphotocode.com', 'DC Report Platform');
$mail->addAddress('robert@dc-image.com', 'Robert Casanovas');
$mail->addCC('jtarres@dc-image.com', 'Josep Tarres');
$mail->Subject = '[Alerta] MyPhotoCode: Manteniment d\'arxius necessari a ' . $fetxa;
$mail->Body = "Webspace IONOS\n\nComprovat a " . $fetxa . "\n\nArxius actuals " . $salida . " de 262144 disponibles.\n\nFalten " . $diferencia . " arxius per arribar al limit.";

// Enviamos el correo electrónico
if (!$mail->send()) {
  echo 'Error al enviar el correo electrónico: ' . $mail->ErrorInfo;
} else {
  echo 'Correo electrónico enviado correctamente.';
}

?>
