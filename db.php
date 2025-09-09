<?php
$host = "127.0.0.1";   // or "localhost"
$dbname = "apex_growth"; // your database name
$user = "root";         // default for XAMPP
$pass = "";             // default for XAMPP (no password)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}
?>
