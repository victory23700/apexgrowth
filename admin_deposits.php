<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: adminlogin.php");
    exit;
}

// Approve/Reject action
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $stmt = $pdo->prepare("SELECT user_id, amount FROM deposits WHERE id=? AND status='Pending'");
    $stmt->execute([$id]);
    $deposit = $stmt->fetch();

    if ($deposit) {
        // Update deposit
        $pdo->prepare("UPDATE deposits SET status='Approved' WHERE id=?")->execute([$id]);

        // Add balance
        $pdo->prepare("UPDATE users SET balance=balance+? WHERE id=?")->execute([$deposit['amount'], $deposit['user_id']]);
    }
    header("Location: admin_deposits.php");
    exit;
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $pdo->prepare("UPDATE deposits SET status='Rejected' WHERE id=?")->execute([$id]);
    header("Location: admin_deposits.php");
    exit;
}

// Fetch deposits
$deposits = $pdo->query("SELECT d.id, u.username, d.amount, d.method, d.status, d.created_at 
                         FROM deposits d JOIN users u ON d.user_id=u.id 
                         ORDER BY d.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Deposits</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
  <h1 class="text-2xl font-bold mb-6">💰 Deposit Requests</h1>
  <div class="bg-white shadow rounded overflow-x-auto">
    <table class="w-full border-collapse text-left">
      <thead class="bg-gray-200">
        <tr>
          <th class="p-2">ID</th>
          <th class="p-2">User</th>
          <th class="p-2">Amount</th>
          <th class="p-2">Method</th>
          <th class="p-2">Status</th>
          <th class="p-2">Date</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($deposits as $d): ?>
        <tr class="border-b">
          <td class="p-2"><?= $d['id'] ?></td>
          <td class="p-2"><?= htmlspecialchars($d['username']) ?></td>
          <td class="p-2">$<?= number_format($d['amount'],2) ?></td>
          <td class="p-2"><?= $d['method'] ?></td>
          <td class="p-2"><?= $d['status'] ?></td>
          <td class="p-2"><?= $d['created_at'] ?></td>
          <td class="p-2 space-x-2">
            <?php if ($d['status'] === 'Pending'): ?>
              <a href="?approve=<?= $d['id'] ?>" class="bg-green-600 text-white px-3 py-1 rounded">Approve</a>
              <a href="?reject=<?= $d['id'] ?>" class="bg-red-600 text-white px-3 py-1 rounded">Reject</a>
            <?php else: ?>
              ✅ Processed
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
