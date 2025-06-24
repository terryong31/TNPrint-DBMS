<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "sanko");
if ($conn->connect_error) die("DB error: " . $conn->connect_error);

// CREATE
if (isset($_POST['create'])) {
    $custcode = $_POST['Custcode'];
    $custname = $_POST['Custname'];
    $prdcode = $_POST['Prdcode'];
    $prddesc = $_POST['Prddesc'];
    $prduom = $_POST['Prduom'];
    $pum = $_POST['PUM'];
    
    $stmt = $conn->prepare("INSERT INTO information (Custcode, Custname, Prdcode, Prddesc, Prddesc, Prduom, PUM) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $custcode, $custname, $prdcode, $prddesc, $prduom, $pum);
    $stmt->execute();
    $stmt->close();
}

// UPDATE
if (isset($_POST['update'])) {
    $custcode = $_POST['Custcode'];
    $custname = $_POST['Custname'];
    $prdcode = $_POST['Prdcode'];
    $prddesc = $_POST['Prddesc'];
    $prduom = $_POST['Prduom'];
    $pum = $_POST['PUM'];

    $stmt = $conn->prepare("UPDATE products SET Custcode=?, Custname=?, Prdcode=?, Prddesc=?, Prddesc=?, Prduom=?, PUM=? WHERE Prdcode=?");
    $stmt->bind_param("ssssss", $custcode, $custname, $prdcode, $prddesc, $prduom, $pum);
    $stmt->execute();
    $stmt->close();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE Prdcode=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// READ
$products = $conn->query("SELECT * FROM information");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Management</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
  <h1 class="text-2xl font-bold mb-6">📦 Database CRUD Management</h1>

  <!-- Create Form -->
  <form method="POST" class="bg-white p-4 rounded shadow mb-6 max-w-md">
    <h2 class="font-semibold mb-2">Add New Product</h2>
    <input name="name" type="text" placeholder="Product Name" required class="w-full mb-2 p-2 border rounded">
    <input name="price" type="number" step="0.01" placeholder="Price" required class="w-full mb-2 p-2 border rounded">
    <input name="quantity" type="number" placeholder="Quantity" required class="w-full mb-2 p-2 border rounded">
    <button name="create" type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Add Product</button>
  </form>

  <!-- Product Table -->
  <table class="table-auto w-full bg-white rounded shadow">
    <thead>
      <tr class="bg-gray-200">
        <th class="p-2">ID</th>
        <th class="p-2">Name</th>
        <th class="p-2">Price</th>
        <th class="p-2">Quantity</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $products->fetch_assoc()): ?>
      <tr class="border-t">
        <td class="p-2"><?= $row['product_id'] ?></td>
        <td class="p-2"><?= htmlspecialchars($row['name']) ?></td>
        <td class="p-2">RM <?= number_format($row['price'], 2) ?></td>
        <td class="p-2"><?= $row['quantity'] ?></td>
        <td class="p-2">
          <form method="POST" class="inline">
            <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="p-1 border rounded w-24">
            <input type="number" step="0.01" name="price" value="<?= $row['price'] ?>" class="p-1 border rounded w-20">
            <input type="number" name="quantity" value="<?= $row['quantity'] ?>" class="p-1 border rounded w-20">
            <button name="update" type="submit" class="text-green-600 ml-1">Save</button>
          </form>
          <a href="?delete=<?= $row['product_id'] ?>" class="text-red-500 ml-2" onclick="return confirm('Delete product?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
