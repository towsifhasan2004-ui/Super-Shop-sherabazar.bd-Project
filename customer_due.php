<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$result = mysqli_query($conn, "
SELECT *
FROM customer_dues
ORDER BY due_date ASC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Customer Due Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card{
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}

</style>

</head>

<body>

<div class="container mt-4">

<div class="card">

<div class="card-header bg-danger text-white">

<h3>Customer Due Management</h3>

</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Customer</th>
<th>Total Due</th>
<th>Paid</th>
<th>Remaining</th>
<th>Due Date</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['customer_name']); ?></td>

<td>৳ <?php echo number_format($row['total_due'],2); ?></td>

<td>৳ <?php echo number_format($row['paid_amount'],2); ?></td>

<td>৳ <?php echo number_format($row['remaining_due'],2); ?></td>

<td><?php echo $row['due_date']; ?></td>

<td>

<?php

if($row['status']=="Paid"){

echo "<span class='badge bg-success'>Paid</span>";

}else{

echo "<span class='badge bg-danger'>Pending</span>";

}

?>

</td>

<td>

<?php if($row['status']=="Pending"){ ?>

<form action="receive_payment.php" method="POST">

<input
type="hidden"
name="id"
value="<?php echo $row['id']; ?>">

<input
type="number"
step="0.01"
name="payment"
class="form-control mb-2"
placeholder="Amount"
required>

<button
class="btn btn-success btn-sm w-100">

Receive Payment

</button>

</form>

<?php } ?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center">

No Due Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</body>

</html>