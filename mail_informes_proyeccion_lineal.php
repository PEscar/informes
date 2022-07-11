<?php

require 'vendor/autoload.php';
require 'vendor/phpmailer/phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpSpreadsheet\Reader\Html;

$base_url_download = !isset($argv[1]) ? die('no hay url de descarga') : $argv[1];
// generate_excel($base_url_download . '/excel_proyectado_lineal.php', [ 'g' => 1, 'mes' => date('m'), 'anio' => date('Y') ], 'proyectado_lineal_' . strtoupper(date('Y-m-d')) .'.pdf');
// generate_excel($base_url_download . '/excel_proyectado_lineal.php', [ 'g' => 2, 'mes' => date('m'), 'anio' => date('Y') ], 'proyectado_lineal_olivos_' . strtoupper(date('Y-m-d')) .'.pdf');
send_mails();

function generate_pdf($url, $params = [], $file_name)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);         //follow redirects

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    $response = curl_exec($ch);
    curl_close($ch);
    file_put_contents(
    $file_name,
    $response
    );
}

function send_mails()
{
    $mail = new PHPMailer(true);// Server settings
    $mail->isSMTP();
    $mail->SMTPDebug = 0; // 2 para detalles
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure= 'tls';
    $mail->Port = 587;
    $mail->Username = 'pedroscarselletta@gmail.com';
    $mail->Password = 'fsqgcailobrjynvb';// Sender &amp; Recipient
    $mail->FromName = 'Sistemas';
    // $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->SetFrom('no-reply@fscarg.com', 'Sistemas'); //Name is optional
    $mail->Subject   = 'Informes Proyección Lineal - ' . date('Y-M-d');
    $mail->Body      = 'Informes Proyección Lineal - ' . date('Y-M-d');

    $receptores = explode(',', file('receptores.txt')[1]);

    foreach ($receptores as $key => $receptor)
        $mail->AddAddress($receptor);

    // Todos - Olivos
    $file_to_attach = 'proyectado_lineal_' . strtoupper(date('Y-m-d')) .'.pdf';
    $mail->AddAttachment( $file_to_attach , $file_to_attach );

    // Olivos
    $file_to_attach = 'proyectado_lineal_olivos_' . strtoupper(date('Y-m-d')) .'.pdf';
    $mail->AddAttachment( $file_to_attach , $file_to_attach );

    if($mail->send()){
        echo 'Success Message';
        return true;
    }else{
        echo 'Error Message';
        return false;
    }
}

function calcular_porc ($new, $old)
{
  $old = (int)$old;
  $new = (int)$new;

  if ( $old == 0 || $new == 0 )
    return '';

  $dif = $new - $old;

  return ( $dif * 100 ) / $old;
}
?>