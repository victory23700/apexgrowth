<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetchColumn();

// Fetch services from API
$api_url = "https://reallysimplesocial.com/api/v2";
$api_key = "2bf4bdd4ed5921e0444cd6e0c907a4fd";

$post_fields = [
    'key' => $api_key,
    'action' => 'services'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
$response = curl_exec($ch);
curl_close($ch);

$services = json_decode($response, true);

// Fetch orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch fund requests
$stmt = $pdo->prepare("SELECT * FROM fund_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$funds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apex Growth - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3B82F6',
            secondary: '#1E40AF',
            dark: '#1F2937',
            light: '#F9FAFB'
          }
        }
      }
    }
  </script>
  <style>
    .gradient-bg {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card-hover {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .sidebar {
      background: linear-gradient(180deg, #1F2937 0%, #111827 100%);
    }
  </style>
</head>
<body class="bg-gray-50 flex h-screen">
  <!-- Sidebar -->
  <aside class="sidebar w-64 text-white flex flex-col">
    <div class="p-6">
      <h2 class="text-2xl font-bold text-white flex items-center">
        <i class="fas fa-rocket mr-2 text-blue-400"></i>
        Apex Growth
      </h2>
    </div>
    
    <nav class="flex-1 px-4 space-y-2">
      <a href="#balance" class="flex items-center px-4 py-3 text-gray-300 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
        <i class="fas fa-wallet mr-3"></i>
        Balance
      </a>
      <a href="#neworder" class="flex items-center px-4 py-3 text-gray-300 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
        <i class="fas fa-plus-circle mr-3"></i>
        New Order
      </a>
      <a href="#orders" class="flex items-center px-4 py-3 text-gray-300 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
        <i class="fas fa-list-alt mr-3"></i>
        Orders
      </a>
      <a href="#addfunds" class="flex items-center px-4 py-3 text-gray-300 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
        <i class="fas fa-credit-card mr-3"></i>
        Add Funds
      </a>
      <a href="#funds" class="flex items-center px-4 py-3 text-gray-300 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
        <i class="fas fa-history mr-3"></i>
        Fund History
      </a>
    </nav>

    <div class="p-4 border-t border-gray-700">
      <a href="logout.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-all">
        <i class="fas fa-sign-out-alt mr-3"></i>
        Logout
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 overflow-y-auto bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="flex justify-between items-center p-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <div class="flex items-center space-x-4">
          <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
            <span class="text-white font-bold"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></span>
          </div>
        </div>
      </div>
    </header>

    <!-- Balance Card -->
    <section id="balance" class="p-6">
      <div class="gradient-bg rounded-2xl p-6 text-white shadow-xl">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-lg font-semibold">Wallet Balance</h2>
            <p class="text-3xl font-bold mt-2">₦<?= number_format($balance, 2) ?></p>
            <p class="text-blue-100 mt-1">Available for orders</p>
          </div>
          <div class="text-4xl">
            <i class="fas fa-wallet"></i>
          </div>
        </div>
      </div>
    </section>

    <!-- New Order -->
    <section id="neworder" class="p-6">
      <div class="bg-white rounded-2xl shadow-sm p-6 card-hover">
        <h2 class="text-xl font-bold mb-6 flex items-center">
          <i class="fas fa-plus-circle mr-3 text-blue-500"></i>
          Place New Order
        </h2>
        <form action="place_order.php" method="POST" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-semibold mb-2">Select Service</label>
              <select name="service_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <?php foreach ($services as $srv): ?>
                  <option value="<?= $srv['service'] ?>">
                    <?= htmlspecialchars($srv['name']) ?> (₦<?= $srv['rate'] ?>/1000)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2">Quantity</label>
              <input type="number" name="quantity" min="10" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1000">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2">Link</label>
            <input type="url" name="link" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://example.com/post">
          </div>
          
          <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-all">
            <i class="fas fa-paper-plane mr-2"></i>
            Place Order
          </button>
        </form>
      </div>
    </section>

    <!-- Orders List -->
    <section id="orders" class="p-6">
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-bold mb-6 flex items-center">
          <i class="fas fa-list-alt mr-3 text-blue-500"></i>
          Recent Orders
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php foreach ($orders as $o): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">#<?= $o['api_order_id'] ?></td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($o['service_id']) ?></td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= number_format($o['quantity']) ?></td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    <?= $o['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
                       ($o['status'] === 'Processing' ? 'bg-yellow-100 text-yellow-800' : 
                       'bg-blue-100 text-blue-800') ?>">
                    <?= $o['status'] ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Add Funds -->
    <section id="addfunds" class="p-6">
      <div class="bg-white rounded-2xl shadow-sm p-6 card-hover">
        <h2 class="text-xl font-bold mb-6 flex items-center">
          <i class="fas fa-credit-card mr-3 text-blue-500"></i>
          Add Funds
        </h2>
        <form action="addfunds.php" method="POST" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-semibold mb-2">Amount (₦)</label>
              <input type="number" name="amount" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1000">
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2">Payment Method</label>
              <select name="method" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="Bank Transfer">🏦 Bank Transfer</option>
                <option value="BTC">₿ Bitcoin</option>
                <option value="USDT">💲 USDT (TRC20)</option>
                <option value="PayPal">💳 PayPal</option>
              </select>
            </div>
          </div>
          <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg transition-all">
            <i class="fas fa-plus-circle mr-2"></i>
            Request Deposit
          </button>
        </form>
      </div>
    </section>

    <!-- Funds History -->
    <section id="funds" class="p-6">
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-bold mb-6 flex items-center">
          <i class="fas fa-history mr-3 text-blue-500"></i>
          Fund Request History
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php foreach ($funds as $f): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₦<?= number_format($f['amount'], 2) ?></td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= $f['method'] ?></td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    <?= $f['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                       ($f['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 
                       'bg-yellow-100 text-yellow-800') ?>">
                    <?= $f['status'] ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900"><?= date('M j, Y', strtotime($f['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</body>
</html>