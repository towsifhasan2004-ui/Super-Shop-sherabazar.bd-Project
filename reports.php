<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">Reports Dashboard</h2>

<div class="row">

<div class="col-md-4 mb-3">
<a href="daily_sales_report.php" class="btn btn-primary w-100 p-4">
Daily Sales Report
</a>
</div>

<div class="col-md-4 mb-3">
<a href="monthly_sales_report.php" class="btn btn-success w-100 p-4">
Monthly Sales Report
</a>
</div>

<div class="col-md-4 mb-3">
<a href="purchase_report.php" class="btn btn-warning w-100 p-4">
Purchase Report
</a>
</div>

<div class="col-md-4 mb-3">
<a href="expense_report.php" class="btn btn-danger w-100 p-4">
Expense Report
</a>
</div>

<div class="col-md-4 mb-3">
<a href="profit_report.php" class="btn btn-info w-100 p-4">
Profit Report
</a>
</div>

<div class="col-md-4 mb-3">
<a href="low_stock_report.php" class="btn btn-dark w-100 p-4">
Low Stock Report
</a>
</div>

</div>

</div>

</body>
</html>