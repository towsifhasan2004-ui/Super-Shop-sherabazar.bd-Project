<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year  = isset($_GET['year']) ? $_GET['year'] : date('Y');

$stmt = $conn->prepare("
SELECT *
FROM sales
WHERE MONTH(sale_date)=?
AND YEAR(sale_date)=?
ORDER BY sale_date DESC
");

$stmt->bind_param("ii", $month, $year);
$stmt->execute();

$result = $stmt->get_result();

$totalSales = 0;
?>

<!DOCTYPE html>
<html>

<head>

<title>Monthly Sales Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Monthly Sales Report</h2>

<form method="GET">

<div class="row">

<div class="col-md-3">

<select name="month" class="form-control">

<?php
for($i=1;$i<=12;$i++){
?>
<option value="<?= $i ?>" <?= ($month==$i)?'selected':'' ?>>
<?= date("F", mktime(0,0,0,$i,1)); ?>
</option>
<?php } ?>

</select>

</div>

<div class="col-md-3">

<input
type="number"
name="year"
class="form-control"
value="<?= $year ?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary">
Search
</button>

</div>

<div class="col-md-2">

<button
type="button"
class="btn btn-success"
onclick="window.print()">

Print

</button>

</div>

</div>

</form>

<hr>

<table class="table table-bordered table-striped">

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

<?php
$totalSales += $row['net_total'];
?>

<tr>

<td><?= htmlspecialchars($row['invoice_no']) ?></td>

<td><?= htmlspecialchars($row['customer_name']) ?></td>

<td><?= htmlspecialchars($row['payment_method']) ?></td>

<td>৳ <?= number_format($row['total_amount'],2) ?></td>

<td>৳ <?= number_format($row['discount'],2) ?></td>

<td><?= number_format($row['vat'],2) ?>%</td>

<td>৳ <?= number_format($row['net_total'],2) ?></td>

<td><?= htmlspecialchars($row['created_by']) ?></td>

<td><?= $row['sale_date'] ?></td>

</tr>

<?php } ?>

</tbody>

<tfoot>

<tr class="table-success">

<th colspan="6">

Monthly Total Sales

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