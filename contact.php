<?php

// Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

// get the post records con validación
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$consulta = isset($_POST['consulta']) ? trim($_POST['consulta']) : '';

// Validación básica
if (empty($name) || empty($phone)) {
    http_response_code(400);
    exit('Los campos Nombre y Teléfono son obligatorios');
}

// validate recaptcha
$recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
if (!$recaptchaResponse) {
    http_response_code(400);
    exit('Por favor completa el reCAPTCHA.');
}

// reCAPTCHA secret key (v2 Checkbox)
$recaptchaSecret = '6Lc2quArAAAAAEkb9e8gqPhmODA0zZH949dO7_CJ';

// Verificar reCAPTCHA con cURL (POST) - Compatible con PHP 5.6
$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
    'secret' => $recaptchaSecret,
    'response' => $recaptchaResponse,
    'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$verifyResponse = curl_exec($ch);
$curlErr = curl_error($ch);
curl_close($ch);

if ($verifyResponse === false) {
    exit('No se pudo verificar el reCAPTCHA.');
}

$verifyData = json_decode($verifyResponse, true);
if (empty($verifyData['success'])) {
    exit('Falló la verificación de reCAPTCHA.');
}

//send email
$email_to = 'deftflamink@gmail.com,igautopartesok@gmail.com,administracion@ig-sa.com.ar';
$email_subject = "Nueva Consulta WEB";
$email_body = '<table><tr><th>Nombre: </th><th>'.htmlspecialchars($name).'</th></tr><tr><th>Telefono: </th><th>'.htmlspecialchars($phone).'</th></tr><tr><th>Email: </th><th>'.htmlspecialchars($email).'</th></tr><tr><th>Consulta: </th><th>'.htmlspecialchars($consulta).'</th></tr></table>';
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'From: Contacto-Pagina@deftflamink.com' . "\r\n" . 'Reply-To: info@deftflamink.com.ar' . "\r\n";

// Intentar enviar el email
if (mail($email_to, $email_subject, $email_body, $headers)) {
    header('Location: http://ig-sa.com.ar/');
    exit();
} else {
    http_response_code(500);
    exit('Error al enviar el email. Intenta nuevamente.');
}

?>