<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    header("Location: sales.php");
    exit();
}

include 'config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Shop Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body{
            background:#f4f6f9;
        }
        .sidebar{
            width:250px;
            height:100vh;
            position:fixed;
            background:#212529;
            color:white;
        }
        .sidebar h3{
            text-align:center;
            padding:20px;
        }
        .sidebar a{
            display:block;
            color:white;
            text-decoration:none;
            padding:15px 20px;
        }
        .sidebar a:hover{
            background:#0d6efd;
        }
        .content{
            margin-left:250px;
            padding:30px;
        }
        .card{
            border:none;
            border-radius:10px;
        }
    </style>
</head>

<body>

<div class="sidebar">

    <h3>Super Shop</h3>

    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="employees.php">👨‍💼 Employees</a>
    <a href="user_management.php">👥 User Management</a>
    <a href="products.php">📦 Products</a>
    <a href="stock.php">📦 Stock Management</a>
    <a href="sales.php">💰 Sales</a>
    <a href="customer_due.php">💳 Customer Due</a>
    <a href="reports.php">📈 Reports</a>
    <a href="logout.php">🚪 Logout</a>

</div>

<div class="content">

<?php
$productQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
$totalProducts = mysqli_fetch_assoc($productQuery)['total'];

$employeeQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees");
$totalEmployees = mysqli_fetch_assoc($employeeQuery)['total'];

$salesQuery = mysqli_query($conn, "
SELECT IFNULL(SUM(net_total),0) AS total
FROM sales
WHERE DATE(sale_date) = CURDATE()
");

$todaySales = mysqli_fetch_assoc($salesQuery)['total'];

$profitQuery = mysqli_query($conn, "
SELECT
IFNULL(SUM(net_total),0) -
(
    SELECT IFNULL(SUM(quantity * p.buying_price),0)
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    JOIN sales s2 ON si.sale_id = s2.id
    WHERE MONTH(s2.sale_date)=MONTH(CURDATE())
      AND YEAR(s2.sale_date)=YEAR(CURDATE())
)
-
(
    SELECT IFNULL(SUM(amount),0)
    FROM expenses
    WHERE MONTH(expense_date)=MONTH(CURDATE())
      AND YEAR(expense_date)=YEAR(CURDATE())
)
AS profit
FROM sales
WHERE MONTH(sale_date)=MONTH(CURDATE())
  AND YEAR(sale_date)=YEAR(CURDATE())
");

$monthlyProfit = mysqli_fetch_assoc($profitQuery)['profit'];

$sql = "
SELECT
MONTHNAME(sale_date) AS month,
SUM(net_total) AS total
FROM sales
GROUP BY MONTH(sale_date)
ORDER BY MONTH(sale_date)
";

$result = mysqli_query($conn,$sql);

$months = [];
$sales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $sales[] = $row['total'];
}

$categorySalesSql = "
SELECT
products.category,
SUM(sale_items.subtotal) AS total
FROM sale_items
JOIN products
ON sale_items.product_id=products.id
GROUP BY products.category
";

$categorySalesResult = mysqli_query($conn, $categorySalesSql);

$recentSalesSql = "
SELECT
DATE(sale_date) AS day,
SUM(net_total) AS total
FROM sales
WHERE sale_date>=DATE_SUB(CURDATE(),INTERVAL 7 DAY)
GROUP BY DATE(sale_date)
";

$recentSalesResult = mysqli_query($conn, $recentSalesSql);
?>

<h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

<hr>

<div class="row">

<div class="col-md-3">
<div class="card bg-primary text-white p-3">
<h4>Total Products</h4>
<h2><?= $totalProducts ?></h2>
</div>
</div>

<div class="col-md-3">
<div class="card bg-success text-white p-3">
<h4>Total Employees</h4>
<h2><?= $totalEmployees ?></h2>
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning text-dark p-3">
<h4>Today's Sales</h4>
<h2>৳<?= number_format($todaySales,2) ?></h2>
</div>
</div>

<div class="col-md-3">
<div class="card bg-danger text-white p-3">
<h4>Monthly Profit</h4>
<h2>৳<?= number_format($monthlyProfit,2) ?></h2>
</div>
</div>

</div>

<br>

<div class="row mt-4">

    <div class="col-md-12">
        <div class="card shadow p-3">
            <h4 class="text-center">📈 Monthly Sales</h4>
            <div style="height:300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="card p-4">

<h3>Admin Panel</h3>

<p>Welcome to the Shera Bazar.</p>

<div class="d-grid gap-3 mt-4">



<a href="purchase.php" class="btn btn-primary btn-lg w-100 mt-3">
    🛒 Purchase Management
</a>

<a href="suppliers.php" class="btn btn-info btn-lg w-100 mt-3">
    🏢 Supplier Management
</a>

<a href="expenses.php" class="btn btn-warning btn-lg w-100 mt-3">
    💸 Expense Management
</a>

<a href="user_management.php" class="btn btn-secondary btn-lg w-100 mt-3">
    👥 User Management
</a>

<a href="customers.php" class="btn btn-primary btn-lg w-100 mt-3">
    👥 Customer Management
</a>



</div>

</div>

</div>

<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months); ?>,
        datasets: [{
            label: 'Monthly Sales (৳)',
            data: <?= json_encode(array_map('floatval', $sales)); ?>,
            borderWidth: 2,
            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#dc3545',
                '#6610f2',
                '#20c997',
                '#fd7e14',
                '#6f42c1',
                '#0dcaf0',
                '#6c757d',
                '#198754',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>