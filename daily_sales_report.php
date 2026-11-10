<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$stmt = $conn->prepare("
SELECT *
FROM sales
WHERE DATE(sale_date)=?
ORDER BY sale_date DESC
");

$stmt->bind_param("s",$date);

$stmt->execute();

$result=$stmt->get_result();

$totalSales=0;
?>

<!DOCTYPE html>
<html>

<head>

<title>Daily Sales Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Daily Sales Report</h2>

<form method="GET">

<div class="row">

<div class="col-md-4">

<input
type="date"
name="date"
value="<?= $date ?>"
class="form-control">

</div>

<div class="col-md-2">

<button class="btn btn-primary">

Search

</button>

</div>

<div class="col-md-2">

<button
type="button"
onclick="window.print();"
class="btn btn-success">

Print

</button>

</div>

</div>

</form>

<hr>

<table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>Invoice</th>

<th>Customer</th>

<th>Payment</th>

<th>Total</th>

<th>Discount</th>

<th>VAT</th>

<th>Net Total</th>

<th>Cashier</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()){ ?>

<?php $totalSales += $row['net_total']; ?>

<tr>

<td><?= htmlspecialchars($row['invoice_no']) ?></td>

<td><?= htmlspecialchars($row['customer_name']) ?></td>

<td><?= htmlspecialchars($row['payment_method']) ?></td>

<td><?= number_format($row['total_amount'],2) ?></td>

<td><?= number_format($row['discount'],2) ?></td>

<td><?= number_format($row['vat'],2) ?></td>

<td><?= number_format($row['net_total'],2) ?></td>

<td><?= htmlspecialchars($row['created_by']) ?></td>

<td><?= $row['sale_date'] ?></td>

</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>

<th colspan="6">

Grand Total

</th>

<th>

৳ <?= number_format($totalSales,2) ?>

</th>

<th colspan="2"></th>

</tr>

</tfoot>

</table>

</div>

</body>

</html>