<?php
header("Content-Type: application/json");

$api_url = "https://reallysimplesocial.com/api/v2";
$api_key = "2bf4bdd4ed5921e0444cd6e0c907a4fd";

$service = $_POST['service'] ?? null;
$link = $_POST['link'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if (!$service || !$link || !$quantity) {
    echo json_encode(["error" => "Missing fields"]);
    exit;
}

$postData = [
    "key" => $api_key,
    "action" => "add",
    "service" => $service,
    "link" => $link,
    "quantity" => $quantity
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
    exit;
}

curl_close($ch);
echo $response;
