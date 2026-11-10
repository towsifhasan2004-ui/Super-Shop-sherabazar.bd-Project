<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if (!isset($_GET['id'])) {
    die("Invoice not found.");
}

$sale_id = (int)$_GET['id'];

// Get sale information
$stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();

if (!$sale) {
    die("Invoice not found.");
}

// Get invoice items
$stmt = $conn->prepare("
SELECT
sale_items.quantity,
sale_items.price,
sale_items.subtotal,
products.product_name
FROM sale_items
JOIN products ON sale_items.product_id = products.id
WHERE sale_items.sale_id = ?
");

$stmt->bind_param("i", $sale_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<title>Invoice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="card">

<div class="card-header bg-dark text-white">

<h3>SUPER SHOP</h3>

</div>

<div class="card-body">

<h5>Invoice No :
<?php echo $sale['invoice_no']; ?>
</h5>

<p>

Customer :
<strong>
<?php echo htmlspecialchars($sale['customer_name']); ?>
</strong>

</p>

<p>

Date :
<?php echo $sale['sale_date']; ?>

</p>

<table class="table table-bordered">

<thead>

<tr>

<th>Product</th>
<th>Price</th>
<th>Qty</th>
<th>Subtotal</th>

</tr>

</thead>

<tbody>

<?php

while($row = $items->fetch_assoc())
{

?>

<tr>

<td><?php echo htmlspecialchars($row['product_name']); ?></td>

<td><?php echo number_format($row['price'],2); ?></td>

<td><?php echo $row['quantity']; ?></td>

<td><?php echo number_format($row['subtotal'],2); ?></td>

</tr>

<?php

}

?>

</tbody>

</table>

<h3 class="text-end">
Grand Total :
৳ <?php echo number_format($sale['net_total'],2); ?>
</h3>

<hr>

<button
class="btn btn-primary"
onclick="window.print();">

Print Invoice

</button>

<a
href="sales.php"
class="btn btn-success">

New Sale

</a>

</div>

</div>

</div>

</body>
</html>