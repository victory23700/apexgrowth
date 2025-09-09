<?php
session_start();
include "db.php";

header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

if ($action === "signup") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "msg" => "Signup successful"]);
    } else {
        echo json_encode(["success" => false, "msg" => "Username already exists"]);
    }
}

if ($action === "login") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'];
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "msg" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "msg" => "User not found"]);
    }
}

if ($action === "logout") {
    session_destroy();
    echo json_encode(["success" => true]);
}
?>
