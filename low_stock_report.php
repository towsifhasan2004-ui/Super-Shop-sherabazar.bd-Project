<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$result = $conn->query("
SELECT *
FROM products
WHERE stock <= minimum_stock
ORDER BY stock ASC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Low Stock Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>⚠ Low Stock Report</h2>

<div class="mb-3">

<button
class="btn btn-success"
onclick="window.print()">

Print Report

</button>

</div>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Barcode</th>
<th>Product</th>
<th>Category</th>
<th>Buying Price</th>
<th>Selling Price</th>
<th>Current Stock</th>
<th>Minimum Stock</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['barcode']); ?></td>

<td><?= htmlspecialchars($row['product_name']); ?></td>

<td><?= htmlspecialchars($row['category']); ?></td>

<td>৳ <?= number_format($row['buying_price'],2); ?></td>

<td>৳ <?= number_format($row['selling_price'],2); ?></td>

<td><?= $row['stock']; ?></td>

<td><?= $row['minimum_stock']; ?></td>

<td>

<?php

if($row['stock']==0){

echo "<span class='badge bg-danger'>Out of Stock</span>";

}elseif($row['stock'] <= $row['minimum_stock']){

echo "<span class='badge bg-warning text-dark'>Low Stock</span>";

}else{

echo "<span class='badge bg-success'>OK</span>";

}

?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>