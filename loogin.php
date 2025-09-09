<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Apex Growth</title>
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
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
  </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md card-hover">
    <div class="text-center mb-8">
      <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-rocket text-white text-2xl"></i>
      </div>
      <h1 class="text-3xl font-bold text-gray-800">Welcome Back</h1>
      <p class="text-gray-600 mt-2">Sign in to your Apex Growth account</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
        <div class="relative">
          <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
          <input type="email" name="email" placeholder="Enter your email" 
                 class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                 required>
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
        <div class="relative">
          <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
          <input type="password" name="password" placeholder="Enter your password" 
                 class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                 required>
        </div>
      </div>

      <button type="submit" 
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-all">
        <i class="fas fa-sign-in-alt mr-2"></i>
        Sign In
      </button>
    </form>

    <div class="text-center mt-6 pt-6 border-t border-gray-200">
      <p class="text-gray-600">Don't have an account? 
        <a href="signup.php" class="text-blue-600 hover:text-blue-700 font-semibold">Sign up here</a>
      </p>
    </div>
  </div>
</body>
</html>