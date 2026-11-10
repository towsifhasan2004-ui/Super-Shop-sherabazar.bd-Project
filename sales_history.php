<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

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

$result = $conn->query("
    SELECT *
    FROM sales
    ORDER BY sale_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Sales History</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Sales History</h2>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th>Invoice</th>
<th>Customer</th>
<th>Total</th>
<th>Date</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?= htmlspecialchars($row['invoice_no']); ?></td>

<td><?= htmlspecialchars($row['customer_name']); ?></td>

<td>৳ <?= number_format($row['total_amount'],2); ?></td>

<td><?= $row['sale_date']; ?></td>

<td>

<a href="invoice.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">
View
</a>

<a href="invoice.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-success btn-sm">
Print
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>