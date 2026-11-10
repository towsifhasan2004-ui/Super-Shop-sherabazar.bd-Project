<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if (isset($_POST['save'])) {

    $customer_name = trim($_POST['customer_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

 $stmt = $conn->prepare("
    INSERT INTO customers
    (customer_name, phone, email, address)
    VALUES (?, ?, ?, ?)
    ");

   $stmt->bind_param(
    "ssss",
    $customer_name,
    $phone,
    $email,
    $address
    );

$stmt->execute();
    header("Location: customers.php");
    exit();
}

$result = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Customers</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Customer Management</h2>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Customer Name</label>
<input type="text" name="customer_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Address</label>
<input type="text" name="address" class="form-control">
</div>

</div>

<button class="btn btn-success" name="save">
Save Customer
</button>

</form>

<hr>

<table class="table table-bordered table-striped">

<thead>

<tr>
<th>ID</th>
<th>Name</th>
<th>Phone</th>
<th>Email</th>
<th>Address</th>
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?= $row['id']; ?></td>
<td><?= htmlspecialchars($row['customer_name']); ?></td>
<td><?= htmlspecialchars($row['phone']); ?></td>
<td><?= htmlspecialchars($row['email']); ?></td>
<td><?= htmlspecialchars($row['address']); ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>