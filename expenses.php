<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if(isset($_POST['save'])){

    $expense_name=$_POST['expense_name'];
    $category=$_POST['category'];
    $amount=$_POST['amount'];
    $expense_date=$_POST['expense_date'];
    $remarks=$_POST['remarks'];

    $stmt=$conn->prepare("
        INSERT INTO expenses
        (expense_name,category,amount,expense_date,remarks)
        VALUES (?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssdss",
        $expense_name,
        $category,
        $amount,
        $expense_date,
        $remarks
    );

    $stmt->execute();

    header("Location: expenses.php");
    exit();
}

$result=$conn->query("SELECT * FROM expenses ORDER BY expense_date DESC,id DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Expense Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Expense Management</h2>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Expense Name</label>
<input type="text" name="expense_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Category</label>

<select name="category" class="form-control">

<option>Rent</option>
<option>Salary</option>
<option>Electricity</option>
<option>Transport</option>
<option>Internet</option>
<option>Maintenance</option>
<option>Other</option>

</select>

</div>

<div class="col-md-4 mb-3">
<label>Amount</label>
<input type="number" step="0.01" name="amount" class="form-control" required>
</div>

<div class="col-md-4 mb-3">
<label>Date</label>
<input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
</div>

<div class="col-md-4 mb-3">
<label>Remarks</label>
<input type="text" name="remarks" class="form-control">
</div>

</div>

<button class="btn btn-success" name="save">
Save Expense
</button>

</form>

<hr>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Amount</th>
<th>Date</th>
<th>Remarks</th>
</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<td><?= $row['id']; ?></td>
<td><?= htmlspecialchars($row['expense_name']); ?></td>
<td><?= htmlspecialchars($row['category']); ?></td>
<td>৳ <?= number_format($row['amount'],2); ?></td>
<td><?= $row['expense_date']; ?></td>
<td><?= htmlspecialchars($row['remarks']); ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>