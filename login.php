<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "sanko");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password, name FROM admin WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword, $name);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header("Location: database_dashboard.php");
            exit();
        } else {
            $error = "Invalid ID or password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="login.css" />
  <script src="login.js" defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form action="login.php" method="POST" class="login-form bg-white p-6 rounded shadow-md w-96">
    <h2 class="text-2xl font-bold mb-4 text-center">Texchem Admin Login</h2>
    
    <?php if (isset($error)): ?>
      <div class="text-red-500 mb-3 text-sm text-center"><?= $error ?></div>
    <?php endif; ?>

    <input type="text" name="id" placeholder="User ID" required
      class="w-full px-3 py-2 border border-gray-300 rounded mb-3 focus:outline-none focus:ring focus:border-blue-500" />
    <input type="password" name="password" placeholder="Password" required
      class="w-full px-3 py-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring focus:border-blue-500" />
    <button type="submit"
      class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">Login</button>
  </form>
</body>
</html>
