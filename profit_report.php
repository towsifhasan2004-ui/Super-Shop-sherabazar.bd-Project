<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// Total Sales
$salesQuery = $conn->prepare("
SELECT IFNULL(SUM(total_amount),0) AS total_sales
FROM sales
WHERE DATE(sale_date) BETWEEN ? AND ?
");
$salesQuery->bind_param("ss", $from, $to);
$salesQuery->execute();
$totalSales = $salesQuery->get_result()->fetch_assoc()['total_sales'];

// Cost of Goods Sold
$cogsQuery = $conn->prepare("
SELECT IFNULL(SUM(si.quantity * p.buying_price),0) AS cogs
FROM sale_items si
INNER JOIN products p ON p.id = si.product_id
INNER JOIN sales s ON s.id = si.sale_id
WHERE DATE(s.sale_date) BETWEEN ? AND ?
");
$cogsQuery->bind_param("ss", $from, $to);
$cogsQuery->execute();
$cogs = $cogsQuery->get_result()->fetch_assoc()['cogs'];

// Expenses
$expenseQuery = $conn->prepare("
SELECT IFNULL(SUM(amount),0) AS total_expense
FROM expenses
WHERE expense_date BETWEEN ? AND ?
");
$expenseQuery->bind_param("ss", $from, $to);
$expenseQuery->execute();
$totalExpense = $expenseQuery->get_result()->fetch_assoc()['total_expense'];

$profit = $totalSales - $cogs - $totalExpense;
?>

<!DOCTYPE html>
<html>
<head>

<title>Profit Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Profit Report</h2>

<form method="GET">

<div class="row">

<div class="col-md-3">
<label>From</label>
<input type="date" name="from" value="<?= $from ?>" class="form-control">
</div>

<div class="col-md-3">
<label>To</label>
<input type="date" name="to" value="<?= $to ?>" class="form-control">
</div>

<div class="col-md-2 d-grid">
<label>&nbsp;</label>
<button class="btn btn-primary">
Generate
</button>
</div>

<div class="col-md-2 d-grid">
<label>&nbsp;</label>
<button type="button" onclick="window.print()" class="btn btn-success">
Print
</button>
</div>

</div>

</form>

<hr>

<table class="table table-bordered">

<tr class="table-primary">
<th>Total Sales</th>
<td>৳ <?= number_format($totalSales,2) ?></td>
</tr>

<tr class="table-warning">
<th>Cost of Goods Sold</th>
<td>৳ <?= number_format($cogs,2) ?></td>
</tr>

<tr class="table-danger">
<th>Total Expenses</th>
<td>৳ <?= number_format($totalExpense,2) ?></td>
</tr>

<tr class="<?= $profit>=0 ? 'table-success':'table-danger' ?>">

<th>Net Profit</th>

<td>

<strong>

৳ <?= number_format($profit,2) ?>

</strong>

</td>

</tr>

</table>

</div>

</body>
</html>