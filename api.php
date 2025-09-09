<?php
header("Content-Type: application/json");

// Your API key
$api_key = "2bf4bdd4ed5921e0444cd6e0c907a4fd";
$api_url = "https://reallysimplesocial.com/api/v2";

// Collect POST data
$action   = $_POST['action']   ?? '';
$service  = $_POST['service']  ?? '';
$link     = $_POST['link']     ?? '';
$quantity = $_POST['quantity'] ?? '';
$order_id = $_POST['order']    ?? '';

$postData = ["key" => $api_key];

if ($action === "add") {
    $postData["action"]  = "add";
    $postData["service"] = $service;
    $postData["link"]    = $link;
    $postData["quantity"] = $quantity;
} elseif ($action === "balance") {
    $postData["action"] = "balance";
} elseif ($action === "status") {
    $postData["action"] = "status";
    $postData["order"]  = $order_id;
} else {
    echo json_encode(["error" => "Invalid action"]);
    exit;
}

// Send request
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// Try decoding JSON response
$decoded = json_decode($response, true);

if ($decoded === null) {
    // Debugging output
    echo json_encode([
        "error" => "Invalid JSON received from API",
        "raw_response" => $response,    // 👀 what the API actually sent
        "postData_sent" => $postData    // 👀 what we sent to API
    ]);
} else {
    echo json_encode($decoded);
}
?>