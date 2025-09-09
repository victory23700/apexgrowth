<?php
session_start();

// Hardcoded admin credentials (you can change these)
$admin_user = "admin";
$admin_pass = "apex123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>❌ Invalid login. <a href='login.html'>Try again</a></p>";
    }
}

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: adminlogin.html");
    exit;
}
