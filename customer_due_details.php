<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if(!isset($_GET['id'])){

    die("Customer not found.");

}

$customer_due_id=(int)$_GET['id'];

/*=====================================
GET CUSTOMER SUMMARY
=====================================*/

$stmt=$conn->prepare("

SELECT *

FROM customer_dues

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$customer_due_id);

$stmt->execute();

$customer=$stmt->get_result()->fetch_assoc();

if(!$customer){

    die("Customer Due Not Found.");

}

/*=====================================
GET ALL DUE INVOICES
=====================================*/

$stmt=$conn->prepare("

SELECT *

FROM customer_due_details

WHERE customer_due_id=?

ORDER BY due_date DESC,id DESC

");

$stmt->bind_param("i",$customer_due_id);

$stmt->execute();

$invoices=$stmt->get_result();

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>

Customer Due Details

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#f5f6fa;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.08);

}

</style>

</head>

<body>

<div class="container mt-4">

<div class="d-flex justify-content-between">

<h2>

<i class="fa-solid fa-file-invoice"></i>

Customer Due Details

</h2>

<div>

<a
href="customer_due.php"
class="btn btn-secondary">

Back

</a>

<button
onclick="window.print()"
class="btn btn-success">

Print

</button>

</div>

</div>

<br>

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-3">

<b>Customer</b>

<br>

<?php echo htmlspecialchars($customer['customer_name']); ?>

</div>

<div class="col-md-3">

<b>Total Due</b>

<br>

<span class="text-danger">

৳ <?php echo number_format($customer['total_due'],2); ?>

</span>

</div>

<div class="col-md-3">

<b>Paid</b>

<br>

<span class="text-success">

৳ <?php echo number_format($customer['paid_amount'],2); ?>

</span>

</div>

<div class="col-md-3">

<b>Remaining</b>

<br>

<span class="text-primary">

৳ <?php echo number_format($customer['remaining_due'],2); ?>

</span>

</div>

</div>

</div>

</div>

<br>

<div class="card">

<div class="card-header bg-primary text-white">

Invoice List

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Invoice</th>

<th>Date</th>

<th>Due</th>

<th>Paid</th>

<th>Remaining</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead><?php

if($invoices->num_rows>0){

while($row=$invoices->fetch_assoc()){

?>

<tr>

<td>

<strong>

<?php echo htmlspecialchars($row['invoice_no']); ?>

</strong>

</td>

<td>

<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</td>

<td class="text-danger fw-bold">

৳ <?php echo number_format($row['due_amount'],2); ?>

</td>

<td class="text-success fw-bold">

৳ <?php echo number_format($row['paid_amount'],2); ?>

</td>

<td class="fw-bold">

৳ <?php echo number_format($row['remaining_amount'],2); ?>

</td>

<td>

<?php

if($row['status']=="Paid"){

?>

<span class="badge bg-success">

Paid

</span>

<?php

}elseif($row['status']=="Partial"){

?>

<span class="badge bg-warning text-dark">

Partial

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Pending

</span>

<?php

}

?>

</td>

<td>

<a
href="invoice.php?id=<?php echo $row['sale_id']; ?>"
target="_blank"
class="btn btn-primary btn-sm">

<i class="fa-solid fa-eye"></i>

View Invoice

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center text-muted">

No Due Invoice Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<tbody>