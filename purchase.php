<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

// Save Purchase
if (isset($_POST['purchase'])) {

    $supplier = $_POST['supplier'];
    $product = $_POST['product'];
    $quantity = (int)$_POST['quantity'];
    $buying_price = (float)$_POST['buying_price'];
    $purchase_date = $_POST['purchase_date'];

    $total = $quantity * $buying_price;
    $created_by = $_SESSION['username'];

    $conn->begin_transaction();

    try {

        // Save purchase
        $stmt = $conn->prepare("
            INSERT INTO purchases
            (supplier_id, product_id, quantity, buying_price, total, purchase_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiiddss",
            $supplier,
            $product,
            $quantity,
            $buying_price,
            $total,
            $purchase_date,
            $created_by
        );

        if (!$stmt->execute()) {
            die("Purchase Insert Error: " . $stmt->error);
        }

        echo "Purchase inserted successfully!";

        // Increase stock
        $stmt = $conn->prepare("
            UPDATE products
            SET stock = stock + ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ii",
            $quantity,
            $product
        );

        $stmt->execute();

        $conn->commit();

        header("Location: purchase.php?success=1");
        exit();

    } catch (Exception $e) {

        $conn->rollback();
        die($e->getMessage());

    }
}

// Load suppliers
$suppliers = $conn->query("
SELECT id, supplier_name
FROM suppliers
ORDER BY supplier_name
");

// Load products
$products = $conn->query("
SELECT id, product_name, buying_price
FROM products
ORDER BY product_name
");

// Purchase history
$history = $conn->query("
SELECT
purchases.*,
products.product_name,
suppliers.supplier_name
FROM purchases
JOIN products
ON purchases.product_id = products.id
JOIN suppliers
ON purchases.supplier_id = suppliers.id
ORDER BY purchases.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Purchase Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Purchase Management</h2>

<?php if(isset($_GET['success'])){ ?>

<div class="alert alert-success">
Purchase Added Successfully.
</div>

<?php } ?>

<form method="POST">

<div class="row">

<div class="col-md-4">

<label>Supplier</label>

<select name="supplier" class="form-control" required>

<option value="">Select Supplier</option>

<?php while($s=$suppliers->fetch_assoc()){ ?>

<option value="<?= $s['id']; ?>">
<?= htmlspecialchars($s['supplier_name']); ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-md-4">

<label>Product</label>

<select name="product" id="product" class="form-control" required>

<option value="">Select Product</option>

<?php while($p=$products->fetch_assoc()){ ?>

<option
value="<?= $p['id']; ?>"
data-price="<?= $p['buying_price']; ?>">

<?= htmlspecialchars($p['product_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-4">

<label>Buying Price</label>

<input
type="number"
step="0.01"
name="buying_price"
id="buying_price"
class="form-control"
required>

</div>

<div class="col-md-4 mt-3">

<label>Quantity</label>

<input
type="number"
name="quantity"
class="form-control"
required>

</div>

<div class="col-md-4 mt-3">

<label>Purchase Date</label>

<input
type="date"
name="purchase_date"
value="<?= date('Y-m-d'); ?>"
class="form-control"
required>

</div>

<div class="col-md-4 mt-3 d-grid">

<label>&nbsp;</label>

<button
name="purchase"
class="btn btn-primary">

Purchase

</button>

</div>

</div>

</form>

<hr>

<h4>Purchase History</h4>

<table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Supplier</th>
<th>Product</th>
<th>Qty</th>
<th>Buying Price</th>
<th>Total</th>
<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($row=$history->fetch_assoc()){ ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['supplier_name']); ?></td>

<td><?= htmlspecialchars($row['product_name']); ?></td>

<td><?= $row['quantity']; ?></td>

<td>৳ <?= number_format($row['buying_price'],2); ?></td>

<td>৳ <?= number_format($row['total'],2); ?></td>

<td><?= $row['purchase_date']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<script>
document.getElementById("product").addEventListener("change", function () {
    const option = this.options[this.selectedIndex];
    document.getElementById("buying_price").value = option.dataset.price || "";
});
</script>

</body>
</html>