<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$stmt = $conn->prepare("
SELECT
    purchases.*,
    suppliers.supplier_name,
    products.product_name
FROM purchases
JOIN suppliers ON purchases.supplier_id = suppliers.id
JOIN products ON purchases.product_id = products.id
WHERE purchase_date BETWEEN ? AND ?
ORDER BY purchase_date DESC
");

$stmt->bind_param("ss", $from, $to);
$stmt->execute();

$result = $stmt->get_result();

$totalPurchase = 0;
$totalQuantity = 0;
?>

<!DOCTYPE html>
<html>
<head>

<title>Purchase Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Purchase Report</h2>

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
<button class="btn btn-primary">Search</button>
</div>

<div class="col-md-2 d-grid">
<label>&nbsp;</label>
<button type="button" class="btn btn-success" onclick="window.print()">
Print
</button>
</div>

</div>

</form>

<hr>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Date</th>
<th>Supplier</th>
<th>Product</th>
<th>Buying Price</th>
<th>Quantity</th>
<th>Total</th>
<th>Created By</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){

$totalPurchase += $row['total'];
$totalQuantity += $row['quantity'];

?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= $row['purchase_date']; ?></td>

<td><?= htmlspecialchars($row['supplier_name']); ?></td>

<td><?= htmlspecialchars($row['product_name']); ?></td>

<td>৳ <?= number_format($row['buying_price'],2); ?></td>

<td><?= $row['quantity']; ?></td>

<td>৳ <?= number_format($row['total'],2); ?></td>

<td><?= htmlspecialchars($row['created_by']); ?></td>

</tr>

<?php } ?>

</tbody>

<tfoot class="table-success">

<tr>

<th colspan="5">Grand Total</th>

<th><?= $totalQuantity; ?></th>

<th>৳ <?= number_format($totalPurchase,2); ?></th>

<th></th>

</tr>

</tfoot>

</table>

</div>

</body>
</html>