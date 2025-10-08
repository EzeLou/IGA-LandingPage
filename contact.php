<?php

// get the post records
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$consulta = $_POST['consulta'];

// validate recaptcha
$recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
if (!$recaptchaResponse) {
    http_response_code(400);
    exit('Por favor completa el reCAPTCHA.');
}

// reCAPTCHA secret key (v2 Checkbox)
$recaptchaSecret = '6Lc2quArAAAAAEkb9e8gqPhmODA0zZH949dO7_CJ';


// Verificar reCAPTCHA con cURL (POST)
$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ],
    CURLOPT_TIMEOUT => 10,
]);
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
$email_to = 'deftflamink@gmail.com'.','.'igautopartesok@gmail.com'.','.'administracion@ig-sa.com.ar';
$email_subject = "Nueva Consulta WEB";
$email_body = '<table><tr><th>Nombre: </th><th>'.$name.'</th></tr><tr><th>Telefono: </th><th>'.$phone.'</th></tr><tr><th>Email: </th><th>'.$email.'</th></tr><tr><th>Consulta: </th><th>'.$consulta.'</th></tr></table>';
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'From: Contacto-Pagina@deftflamink.com' . "\r\n" . 'Reply-To: info@deftflamink.com.ar' . "\r\n";

mail($email_to,$email_subject,$email_body,$headers);
header( 'Location: http://ig-sa.com.ar/');

//header( 'Location: https://deftflamink.com/');

?>