<?php
session_start();
require 'db.php';

// Hardcoded credentials
$admin_user = "admin";
$admin_pass = "apex123";

// Handle login
if (isset($_POST['username'], $_POST['password'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "❌ Invalid login details!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Show login form if not logged in
if (!isset($_SESSION['logged_in'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Apex Growth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md card-hover">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Admin Portal</h1>
            <p class="text-gray-600 mt-2">Apex Growth Control Panel</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="username" placeholder="Admin username" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                           required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                    <input type="password" name="password" placeholder="Admin password" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                           required>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition-all">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Admin Login
            </button>
        </form>
    </div>
</body>
</html>
<?php exit; endif; ?>

<?php
// Fetch stats - FIXED: Removed price column reference
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_deposits = $pdo->query("SELECT COUNT(*) FROM fund_requests WHERE status='Pending'")->fetchColumn();

// Fetch latest orders - FIXED: Removed price column
$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Fetch users
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Fetch deposits
$deposits = $pdo->query("SELECT f.*, u.username FROM fund_requests f JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Growth - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #1F2937 0%, #111827 100%);
        }
        .stats-card {
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="sidebar w-64 text-white flex flex-col">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <i class="fas fa-shield-alt mr-2 text-red-400"></i>
                Admin Panel
            </h2>
        </div>
        
        <nav class="flex-1 px-4 space-y-2">
            <a href="#overview" class="flex items-center px-4 py-3 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Overview
            </a>
            <a href="#orders" class="flex items-center px-4 py-3 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                <i class="fas fa-shopping-cart mr-3"></i>
                Orders
            </a>
            <a href="#deposits" class="flex items-center px-4 py-3 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                <i class="fas fa-credit-card mr-3"></i>
                Deposits
            </a>
            <a href="#users" class="flex items-center px-4 py-3 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                <i class="fas fa-users mr-3"></i>
                Users
            </a>
        </nav>

        <div class="p-4 border-t border-gray-700">
            <a href="admin.php?logout=1" class="flex items-center px-4 py-3 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                <i class="fas fa-sign-out-alt mr-3"></i>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, Admin</span>
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-white"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Overview -->
        <section id="overview" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Total Orders</p>
                        <p class="text-3xl font-bold"><?= $total_orders ?></p>
                    </div>
                    <i class="fas fa-shopping-cart text-3xl opacity-90"></i>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Total Users</p>
                        <p class="text-3xl font-bold"><?= $total_users ?></p>
                    </div>
                    <i class="fas fa-users text-3xl opacity-90"></i>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Pending Orders</p>
                        <p class="text-3xl font-bold"><?= $pending_orders ?></p>
                    </div>
                    <i class="fas fa-clock text-3xl opacity-90"></i>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Pending Deposits</p>
                        <p class="text-3xl font-bold"><?= $pending_deposits ?></p>
                    </div>
                    <i class="fas fa-money-bill-wave text-3xl opacity-90"></i>
                </div>
            </div>
        </section>

        <!-- Orders Table -->
        <section id="orders" class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-shopping-cart mr-3 text-blue-500"></i>
                Recent Orders
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach($orders as $o): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $o['id'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($o['username']) ?></td>
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
        </section>

        <!-- Deposits -->
        <section id="deposits" class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-green-500"></i>
                Deposit Requests
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach($deposits as $d): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $d['id'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($d['username']) ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₦<?= number_format($d['amount'], 2) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= $d['method'] ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    <?= $d['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                       ($d['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                       'bg-yellow-100 text-yellow-800') ?>">
                                    <?= $d['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($d['status'] == "Pending"): ?>
                                    <div class="flex space-x-2">
                                        <a href="approve_deposit.php?id=<?= $d['id'] ?>&action=approve" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-all">
                                            ✅ Approve
                                        </a>
                                        <a href="approve_deposit.php?id=<?= $d['id'] ?>&action=reject" 
                                           class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-all">
                                            ❌ Reject
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-500 text-sm">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Users -->
        <section id="users" class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-users mr-3 text-purple-500"></i>
                Recent Users
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach($users as $u): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $u['id'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₦<?= number_format($u['balance'], 2) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>