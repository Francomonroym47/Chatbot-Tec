<?php
header("Content-Type: application/json; charset=utf-8");

$url = "https://franciscomonroy.app.n8n.cloud/webhook/4b90adba-3085-4032-b656-46017b6defd4"; // ✅ URL de producción

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
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true);

if (isset($json[0]["reply"])) {
  echo json_encode(["reply" => $json[0]["reply"]]);
} elseif (isset($json["reply"])) {
  echo json_encode(["reply" => $json["reply"]]);
} else {
  echo json_encode(["reply" => "⚠️ Respuesta inválida del servidor."]);
}
?>

