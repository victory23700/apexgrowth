<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loogin.html");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $link = $_POST['link'];
    $quantity = $_POST['quantity'];

    // Call API
    $api_url = "https://reallysimplesocial.com/api/v2";
    $api_key = "2bf4bdd4ed5921e0444cd6e0c907a4fd";

    $post_fields = [
        'key' => $api_key,
        'action' => 'add',
        'service' => $service_id,
        'link' => $link,
        'quantity' => $quantity
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['order'])) {
        // Save order to DB
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, service_id, link, quantity, api_order_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $service_id, $link, $quantity, $result['order'], 'Pending']);

        header("Location: dashboard.php?success=1");
    } else {
        header("Location: dashboard.php?error=1");
    }
}
?>
