<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sanko");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Pagination settings
$rowsPerPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $rowsPerPage;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build the query with search and pagination
$whereClause = $search ? "WHERE Custcode LIKE '%$search%' OR Custname LIKE '%$search%' OR Prdcode LIKE '%$search%' OR Prddesc LIKE '%$search%' OR Prduom LIKE '%$search%' OR PUM LIKE '%$search%'" : '';
$query = "SELECT * FROM information $whereClause LIMIT $offset, $rowsPerPage";
$totalQuery = "SELECT COUNT(*) as total FROM information $whereClause";
$products = $conn->query($query);
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// CREATE
if (isset($_POST['create'])) {
    $custcode = $_POST['Custcode'];
    $custname = $_POST['Custname'];
    $prdcode = $_POST['Prdcode'];
    $prddesc = $_POST['Prddesc'];
    $prduom = $_POST['Prduom'];
    $pum = $_POST['PUM'];

    $stmt = $conn->prepare("INSERT INTO information (Custcode, Custname, Prdcode, Prddesc, Prduom, PUM) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $custcode, $custname, $prdcode, $prddesc, $prduom, $pum);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page" . ($search ? "&search=$search" : ""));
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $custcode = $_POST['Custcode'];
    $custname = $_POST['Custname'];
    $prdcode = $_POST['Prdcode'];
    $prddesc = $_POST['Prddesc'];
    $prduom = $_POST['Prduom'];
    $pum = $_POST['PUM'];

    $stmt = $conn->prepare("UPDATE information SET Custcode=?, Custname=?, Prddesc=?, Prduom=?, PUM=? WHERE Prdcode=?");
    $stmt->bind_param("ssssss", $custcode, $custname, $prddesc, $prduom, $pum, $prdcode);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page" . ($search ? "&search=$search" : ""));
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $prdcode = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM information WHERE Prdcode=?");
    $stmt->bind_param("s", $prdcode);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page" . ($search ? "&search=$search" : ""));
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
            --accent-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-bg: #1e293b;
            --card-bg: #f8fafc;
            --table-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            margin: 20px;
            padding: 30px;
        }
        
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }
        
        .card-header.bg-secondary {
            background: linear-gradient(135deg, var(--dark-bg), #334155) !important;
        }
        
        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: var(--table-bg);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
            transform: translateY(-1px);
        }
        
        .form-control-sm {
            border-radius: 8px;
            padding: 8px 12px;
        }
        
        .btn {
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-sm {
            padding: 8px 16px;
            border-radius: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: var(--shadow);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .btn-outline-success {
            border: 2px solid var(--success-color);
            color: var(--success-color);
            background: rgba(16, 185, 129, 0.1);
        }
        
        .btn-outline-success:hover {
            background: var(--success-color);
            transform: translateY(-1px);
        }
        
        .btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            background: rgba(239, 68, 68, 0.1);
        }
        
        .btn-outline-danger:hover {
            background: var(--danger-color);
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--text-muted);
            color: var(--text-muted);
            background: rgba(100, 116, 139, 0.1);
        }
        
        .table {
            background: var(--table-bg);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table-light {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        }
        
        .table-hover tbody tr:hover {
            background: rgba(79, 70, 229, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        .page-link {
            border: 2px solid var(--border-color);
            color: var(--primary-color);
            border-radius: 8px;
            margin: 0 2px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
        }
        
        .badge {
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
        }
        
        .title-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }
        
        .floating-label {
            position: relative;
        }
        
        .floating-label .form-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .btn-group-vertical .btn {
            margin-bottom: 5px;
        }
        
        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }
        
        .card-footer {
            background: rgba(248, 250, 252, 0.8);
            border-top: 1px solid var(--border-color);
            border-radius: 0 0 15px 15px;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
                border-radius: 15px;
            }
            
            .card {
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="text-center mb-5">
            <h1 class="display-4 title-gradient mb-2">
                <i class="bi bi-box-seam me-3"></i>Transfer Note Database CRUD
            </h1>
            <p class="lead text-muted">Database Management</p>
        </div>
        <!-- Logout Button -->
        <div class="text-end mb-4">
            <a href="logout.php" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to logout?')">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>

                <!-- Create Form -->
                <div class="card mb-5 glass-effect">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-white">
                            <i class="bi bi-plus-circle-fill me-2"></i>Add New Product
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="row g-4">
                                <div class="col-md-6 floating-label">
                                    <label for="custcode" class="form-label">Customer Code</label>
                                    <input name="Custcode" id="custcode" type="text" class="form-control" placeholder="Enter customer code" required>
                                </div>
                                <div class="col-md-6 floating-label">
                                    <label for="custname" class="form-label">Customer Name</label>
                                    <input name="Custname" id="custname" type="text" class="form-control" placeholder="Enter customer name" required>
                                </div>
                                <div class="col-md-6 floating-label">
                                    <label for="prdcode" class="form-label">Product Code</label>
                                    <input name="Prdcode" id="prdcode" type="text" class="form-control" placeholder="Enter product code" required>
                                </div>
                                <div class="col-md-6 floating-label">
                                    <label for="prddesc" class="form-label">Product Description</label>
                                    <input name="Prddesc" id="prddesc" type="text" class="form-control" placeholder="Enter product description" required>
                                </div>
                                <div class="col-md-6 floating-label">
                                    <label for="prduom" class="form-label">Unit of Measure</label>
                                    <input name="Prduom" id="prduom" type="text" class="form-control" placeholder="Enter unit of measure" required>
                                </div>
                                <div class="col-md-6 floating-label">
                                    <label for="pum" class="form-label">Primary Unit Measure</label>
                                    <input name="PUM" id="pum" type="text" class="form-control" placeholder="Enter primary unit measure" required>
                                </div>
                                <div class="col-12 text-center">
                                    <button name="create" type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="bi bi-plus-lg me-2"></i>Add Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="card mb-4 glass-effect">
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-8 floating-label">
                                <label for="search" class="form-label">
                                    <i class="bi bi-search me-1"></i>Search Products
                                </label>
                                <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" 
                                       placeholder="Search by any field..." class="form-control">
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary flex-fill">
                                        <i class="bi bi-search me-1"></i>Search
                                    </button>
                                    <?php if ($search): ?>
                                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Product Table -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-table me-2"></i>Product List 
                            <span class="badge bg-light text-dark ms-2"><?= $totalRows ?> total records</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Customer Code</th>
                                        <th scope="col">Customer Name</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Unit of Measure</th>
                                        <th scope="col">Primary Unit</th>
                                        <th scope="col" width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($products->num_rows > 0): ?>
                                        <?php while ($row = $products->fetch_assoc()): ?>
                                        <tr>
                                            <form method="POST" class="inline-form">
                                                <td>
                                                    <input type="text" name="Custcode" value="<?= htmlspecialchars($row['Custcode']) ?>" 
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <input type="text" name="Custname" value="<?= htmlspecialchars($row['Custname']) ?>" 
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <input type="text" name="Prdcode" value="<?= htmlspecialchars($row['Prdcode']) ?>" 
                                                           class="form-control form-control-sm" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="Prddesc" value="<?= htmlspecialchars($row['Prddesc']) ?>" 
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <input type="text" name="Prduom" value="<?= htmlspecialchars($row['Prduom']) ?>" 
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <input type="text" name="PUM" value="<?= htmlspecialchars($row['PUM']) ?>" 
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                                        <button name="update" type="submit" class="btn btn-outline-success btn-sm">
                                                            <i class="bi bi-check-lg"></i> Save
                                                        </button>
                                                        <a href="?delete=<?= urlencode($row['Prdcode']) ?>&page=<?= $page ?><?= $search ? "&search=".urlencode($search) : "" ?>" 
                                                           class="btn btn-outline-danger btn-sm" 
                                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </form>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="empty-state">
                                                <i class="bi bi-inbox display-1"></i>
                                                <h4 class="text-muted mb-2">No products found</h4>
                                                <?php if ($search): ?>
                                                    <p class="text-muted">Try adjusting your search terms or <a href="<?= $_SERVER['PHP_SELF'] ?>" class="text-decoration-none">clear the search</a></p>
                                                <?php else: ?>
                                                    <p class="text-muted">Start by adding your first product above</p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing <?= min($offset + 1, $totalRows) ?> to <?= min($offset + $rowsPerPage, $totalRows) ?> of <?= $totalRows ?> entries
                            </div>
                            
                            <nav aria-label="Product pagination">
                                <ul class="pagination pagination-sm mb-0">
                                    <!-- First Page -->
                                    <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1<?= $search ? "&search=".urlencode($search) : "" ?>">
                                            <i class="bi bi-chevron-double-left"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <!-- Previous Page -->
                                    <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?><?= $search ? "&search=".urlencode($search) : "" ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <!-- Page Numbers -->
                                    <?php
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    // Show first page if not in range
                                    if ($startPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=1<?= $search ? "&search=".urlencode($search) : "" ?>">1</a>
                                        </li>
                                        <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $search ? "&search=".urlencode($search) : "" ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <!-- Show last page if not in range -->
                                    <?php if ($endPage < $totalPages): ?>
                                        <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $totalPages ?><?= $search ? "&search=".urlencode($search) : "" ?>"><?= $totalPages ?></a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <!-- Next Page -->
                                    <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?><?= $search ? "&search=".urlencode($search) : "" ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <!-- Last Page -->
                                    <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $totalPages ?><?= $search ? "&search=".urlencode($search) : "" ?>">
                                            <i class="bi bi-chevron-double-right"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>