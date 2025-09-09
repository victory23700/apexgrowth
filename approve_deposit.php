<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: admin.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        // Get deposit info
        $stmt = $pdo->prepare("SELECT user_id, amount FROM fund_requests WHERE id = ? AND status = 'Pending'");
        $stmt->execute([$id]);
        $deposit = $stmt->fetch();
        
        if ($deposit) {
            // Update deposit status
            $pdo->prepare("UPDATE fund_requests SET status = 'Approved' WHERE id = ?")->execute([$id]);
            
            // Add balance to user
            $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")
                ->execute([$deposit['amount'], $deposit['user_id']]);
        }
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE fund_requests SET status = 'Rejected' WHERE id = ?")->execute([$id]);
    }
    
    header("Location: admin.php#deposits");
    exit;
}
?>