<?php
// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from form
    $id = $_POST['id'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Connect to MySQL (update these as needed)
    $conn = new mysqli("localhost", "root", "", "sanko");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute insert query
    $stmt = $conn->prepare("INSERT INTO admin (id, password, name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $id, $hashedPassword, $name);

    if ($stmt->execute()) {
        echo "<script type='text/javascript'>alert('Profile created successfully');</script>";
    } else {
        echo "<p class='text-red-500'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML form for user input -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
  <form method="POST" action="register.php" class="bg-white p-6 rounded shadow-md w-96">
    <h2 class="text-2xl font-bold mb-4 text-center">Register User</h2>
    <input name="id" type="text" placeholder="User ID" required class="w-full mb-3 p-2 border rounded" />
    <input name="password" type="password" placeholder="Password" required class="w-full mb-3 p-2 border rounded" />
    <input name="name" type="text" placeholder="Name" required class="w-full mb-4 p-2 border rounded" />
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Register</button>
  </form>
</body>
</html>
