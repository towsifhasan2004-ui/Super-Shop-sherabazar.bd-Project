<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if(isset($_POST['update_stock'])){

$id=$_POST['id'];
$stock=$_POST['stock'];

mysqli_query($conn,"UPDATE products SET stock=stock+$stock WHERE id=$id");

header("Location: stock.php");
exit();

}

$result=mysqli_query($conn,"SELECT * FROM products ORDER BY product_name ASC");

?>

<!DOCTYPE html>
<html>
<head>

<title>Stock Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Stock Management</h2>

<table class="table table-bordered table-hover">

<tr>

<th>ID</th>
<th>Product</th>
<th>Current Stock</th>
<th>Add Stock</th>
<th>Action</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<form method="POST">

<td>

<?php echo $row['id']; ?>

<input
type="hidden"
name="id"
value="<?php echo $row['id']; ?>">

</td>

<td>

<?php echo $row['product_name']; ?>

</td>

<td>

<?php

if($row['stock']<10){

echo "<span class='badge bg-danger'>".$row['stock']."</span>";

}else{

echo "<span class='badge bg-success'>".$row['stock']."</span>";

}

?>

</td>

<td>

<input
type="number"
name="stock"
class="form-control"
required>

</td>

<td>

<button
class="btn btn-primary"
name="update_stock">

Update

</button>

</td>

</form>

</tr>

<?php

}

?>

</table>

</div>

</body>

</html>