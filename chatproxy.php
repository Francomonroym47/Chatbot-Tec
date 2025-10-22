<?php
header("Content-Type: application/json; charset=utf-8");

$url = "https://franciscomonroy.app.n8n.cloud/webhook/4b90adba-3085-4032-b656-46017b6defd4";

$input = json_decode(file_get_contents("php://input"), true);
$message = $input["message"] ?? "";

if (!$message) {
  echo json_encode(["reply" => "⚠️ Mensaje vacío."]);
  exit;
}

$data = json_encode(["message" => $message]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Content-Type: application/json",
  "Accept: application/json",
  "Accept-Encoding: identity" // 👈 fuerza texto plano
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

// Si por alguna razón sigue llegando gzip
if (function_exists('gzdecode') && substr($response, 0, 2) === "\x1f\x8b") {
  $response = gzdecode($response);
}

// Depuración opcional
file_put_contents("debug_response.txt", $response);

$json = json_decode($response, true);

if (is_array($json) && isset($json[0]["reply"])) {
  echo json_encode(["reply" => $json[0]["reply"]]);
} elseif (isset($json["reply"])) {
  echo json_encode(["reply" => $json["reply"]]);
} else {
  echo json_encode(["reply" => "❌ No proxy o JSON inválido"]);
}
?>
