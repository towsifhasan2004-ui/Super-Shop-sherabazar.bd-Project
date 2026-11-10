<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$stmt = $conn->prepare("
SELECT *
FROM expenses
WHERE expense_date BETWEEN ? AND ?
ORDER BY expense_date DESC
");

$stmt->bind_param("ss", $from, $to);
$stmt->execute();

$result = $stmt->get_result();

$totalExpense = 0;
?>

<!DOCTYPE html>
<html>
<head>

<title>Expense Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Expense Report</h2>

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
<button type="button" onclick="window.print()" class="btn btn-success">
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
<th>Expense Name</th>
<th>Category</th>
<th>Remarks</th>
<th>Amount</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<?php $totalExpense += $row['amount']; ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= $row['expense_date']; ?></td>

<td><?= htmlspecialchars($row['expense_name']); ?></td>

<td><?= htmlspecialchars($row['category']); ?></td>

<td><?= htmlspecialchars($row['remarks']); ?></td>

<td>৳ <?= number_format($row['amount'],2); ?></td>

</tr>

<?php } ?>

</tbody>

<tfoot>

<tr class="table-danger">

<th colspan="5">

Total Expense

</th>

<th>

৳ <?= number_format($totalExpense,2); ?>

</th>

</tr>

</tfoot>

</table>

</div>

</body>
</html>