<?php
header("Content-Type: application/json");

$api_url = "https://reallysimplesocial.com/api/v2";
$api_key = "2bf4bdd4ed5921e0444cd6e0c907a4fd";

$postData = [
    "key" => $api_key,
    "action" => "services"
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
