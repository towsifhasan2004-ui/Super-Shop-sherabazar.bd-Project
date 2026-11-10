<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}

if (isset($_POST['update'])) {

    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $buying_price = $_POST['buying_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];
    $unit = $_POST['unit'];

    mysqli_query($conn, "UPDATE products SET
        product_name='$product_name',
        category='$category',
        buying_price='$buying_price',
        selling_price='$selling_price',
        stock='$stock',
        unit='$unit'
        WHERE id=$id");

    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2>Edit Product</h2>

<form method="POST">

<div class="mb-3">
<label>Product Name</label>
<input type="text" name="product_name" class="form-control"
value="<?php echo $product['product_name']; ?>" required>
</div>

<div class="mb-3">
<label>Category</label>
<input type="text" name="category" class="form-control"
value="<?php echo $product['category']; ?>" required>
</div>

<div class="mb-3">
<label>Buying Price</label>
<input type="number" step="0.01" name="buying_price" class="form-control"
value="<?php echo $product['buying_price']; ?>" required>
</div>

<div class="mb-3">
<label>Selling Price</label>
<input type="number" step="0.01" name="selling_price" class="form-control"
value="<?php echo $product['selling_price']; ?>" required>
</div>

<div class="mb-3">
<label>Stock</label>
<input type="number" name="stock" class="form-control"
value="<?php echo $product['stock']; ?>" required>
</div>

<div class="mb-3">
<label>Unit</label>
<input type="text" name="unit" class="form-control"
value="<?php echo $product['unit']; ?>" required>
</div>

<button type="submit" name="update" class="btn btn-primary">
Update Product
</button>

<a href="products.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>

</body>
</html>