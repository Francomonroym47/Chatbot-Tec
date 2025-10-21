<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$url = "https://franciscomonroy.app.n8n.cloud/webhook-test/4b90adba-3085-4032-b656-46017b6defd4";
$input = file_get_contents("php://input");
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
http_response_code($httpcode);
echo $response;
?>
