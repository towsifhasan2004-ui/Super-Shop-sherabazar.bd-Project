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

require_once 'config/db.php';

if(!isset($_GET['id'])){

    die("Customer not found.");

}

$customer_id=(int)$_GET['id'];

/*=========================================
GET CUSTOMER INFORMATION
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM customer_dues

WHERE customer_id=?

LIMIT 1

");

$stmt->bind_param("i",$customer_id);

$stmt->execute();

$customer=$stmt->get_result()->fetch_assoc();

if(!$customer){

    die("Customer ledger not found.");

}

/*=========================================
GET CUSTOMER TRANSACTIONS
=========================================*/

$stmt=$conn->prepare("

SELECT

invoice_no,

due_date,

due_amount,

paid_amount,

remaining_amount,

status

FROM customer_due_details

WHERE customer_due_id=?

ORDER BY due_date ASC,id ASC

");

$stmt->bind_param("i",$customer['id']);

$stmt->execute();

$ledger=$stmt->get_result();

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>

Customer Ledger

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

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

<i class="fa-solid fa-book"></i>

Customer Ledger

</h2>

<div>

<a href="customer_due.php" class="btn btn-secondary">

Back

</a>

<button onclick="window.print()" class="btn btn-success">

Print Ledger

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

<b>Total Paid</b>

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

<div class="card-header bg-dark text-white">

Ledger History

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Date</th>

<th>Invoice</th>

<th>Debit (Due)</th>

<th>Credit (Paid)</th>

<th>Balance</th>

<th>Status</th>

</tr>

</thead>

<tbody><?php

$runningBalance = 0;

if($ledger->num_rows>0){

while($row = $ledger->fetch_assoc()){

    /*
    ---------------------------------------
    RUNNING BALANCE
    ---------------------------------------
    */

    $runningBalance += $row['due_amount'];

    $runningBalance -= $row['paid_amount'];

?>

<tr>

<td>

<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['invoice_no']); ?>

</strong>

</td>

<td class="text-danger fw-bold">

৳ <?php echo number_format($row['due_amount'],2); ?>

</td>

<td class="text-success fw-bold">

৳ <?php echo number_format($row['paid_amount'],2); ?>

</td>

<td class="fw-bold">

৳ <?php echo number_format($runningBalance,2); ?>

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

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-muted">

No Ledger Record Found

</td>

</tr>

<?php

}

?>

</tbody>

<tfoot class="table-light">

<tr>

<th colspan="2" class="text-end">

Grand Total

</th>

<th class="text-danger">

৳ <?php echo number_format($customer['total_due'],2); ?>

</th>

<th class="text-success">

৳ <?php echo number_format($customer['paid_amount'],2); ?>

</th>

<th class="text-primary">

৳ <?php echo number_format($customer['remaining_due'],2); ?>

</th>

<th>

<?php

if($customer['remaining_due']<=0){

echo "<span class='badge bg-success'>Settled</span>";

}else{

echo "<span class='badge bg-danger'>Outstanding</span>";

}

?>

</th>

</tr>

</tfoot>

</table>

</div>

</div>

</div>

<footer class="text-center mt-5 mb-4">

<hr>

<h5 class="text-secondary">

<i class="fa-solid fa-store"></i>

Shera Bazar POS System

</h5>

<p class="text-muted mb-0">

Customer Ledger Statement

</p>

<small class="text-muted">

Developed By Towsif Hasan

</small>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>