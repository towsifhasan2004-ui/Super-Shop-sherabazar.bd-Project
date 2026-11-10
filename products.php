<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if(isset($_POST['save'])){

$product_name=$_POST['product_name'];
$category=$_POST['category'];
$buying_price=$_POST['buying_price'];
$selling_price=$_POST['selling_price'];
$stock=$_POST['stock'];
$unit=$_POST['unit'];

$sql="INSERT INTO products(product_name,category,buying_price,selling_price,stock,unit)
VALUES('$product_name','$category','$buying_price','$selling_price','$stock','$unit')";

mysqli_query($conn,$sql);

header("Location: products.php");
exit();

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h2 class="text-center mb-4">Product Management</h2>

<div class="card">

<div class="card-header bg-primary text-white">

<h4>Add New Product</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Product Name</label>

<input
type="text"
name="product_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Category</label>

<input
type="text"
name="category"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Buying Price</label>

<input
type="number"
step="0.01"
name="buying_price"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Selling Price</label>

<input
type="number"
step="0.01"
name="selling_price"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Stock</label>

<input
type="number"
name="stock"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Unit</label>

<select
name="unit"
class="form-control">

<option value="pcs">PCS</option>
<option value="kg">KG</option>
<option value="liter">Liter</option>
<option value="box">Box</option>
<option value="packet">Packet</option>

</select>

</div>

</div>

<button
class="btn btn-success"
name="save">

Save Product

</button>

</form>

</div>

</div>
<hr>

<h3>Product List</h3>

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>ID</th>
<th>Product Name</th>
<th>Category</th>
<th>Buying Price</th>
<th>Selling Price</th>
<th>Stock</th>
<th>Unit</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$result = mysqli_query($conn,"SELECT * FROM products ORDER BY id DESC");

while($row = mysqli_fetch_assoc($result))
{
?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['product_name']; ?></td>

<td><?php echo $row['category']; ?></td>

<td><?php echo $row['buying_price']; ?></td>

<td><?php echo $row['selling_price']; ?></td>

<td><?php echo $row['stock']; ?></td>

<td><?php echo $row['unit']; ?></td>

<td>
    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
        Edit
    </a>

    <a href="delete_product.php?id=<?php echo $row['id']; ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Are you sure you want to delete this product?');">
        Delete
    </a>
</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</body>
</html>