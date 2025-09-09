<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loogin.html");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $method = $_POST['method'];

    // Save fund request
    $stmt = $pdo->prepare("INSERT INTO fund_requests (user_id, amount, method, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $amount, $method, 'Pending']);

    header("Location: dashboard.php?fund_request=1");
}
?>

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Funds - Apex Growth</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex justify-center items-center">
  <div class="bg-white shadow-lg p-8 rounded w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6">💰 Add Funds</h1>

    <?php if (!empty($message)): ?>
      <p class="mb-4 text-center text-blue-600"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block font-semibold">Amount ($)</label>
        <input type="number" name="amount" class="w-full border p-2 rounded" min="1" step="0.01" required>
      </div>
      <div>
        <label class="block font-semibold">Payment Method</label>
        <select name="method" class="w-full border p-2 rounded" required>
          <option value="Bank Transfer">🏦 Bank Transfer</option>
          <option value="Crypto">💎 Crypto</option>
          <option value="PayPal">💳 PayPal</option>
        </select>
      </div>
      <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-500">Submit Request</button>
    </form>

    <div class="mt-6">
      <a href="dashboard.php" class="text-blue-600">⬅ Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
