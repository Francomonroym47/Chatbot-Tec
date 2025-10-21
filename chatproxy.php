<?php
header("Content-Type: application/json; charset=utf-8");

// URL del webhook de PRODUCCIÓN
$url = "https://franciscomonroy.app.n8n.cloud/webhook/4b90adba-3085-4032-b656-46017b6defd4";

// Leer el mensaje enviado desde el frontend
$input = json_decode(file_get_contents("php://input"), true);
$message = $input["message"] ?? "";

if (!$message) {
  echo json_encode(["reply" => "⚠️ Mensaje vacío."]);
  exit;
}

// Preparar el cuerpo de la petición
$data = json_encode(["message" => $message]);

// Configurar CURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Accept-Encoding: gzip"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

// Si viene comprimido en gzip, descomprimir
if (curl_getinfo($ch, CURLINFO_CONTENT_ENCODING) === 'gzip') {
  $response = gzdecode($response);
}

curl_close($ch);

// Intentar decodificar JSON
$json = json_decode($response, true);

// Validar estructura
if (is_array($json) && isset($json[0]["reply"])) {
  echo json_encode(["reply" => $json[0]["reply"]]);
} elseif (isset($json["reply"])) {
  echo json_encode(["reply" => $json["reply"]]);
} else {
  // Guardar debug temporal para revisar si algo cambia
  file_put_contents("debug_response.txt", $response);
  echo json_encode(["reply" => "⚠️ Respuesta inválida del servidor."]);
}
?>
