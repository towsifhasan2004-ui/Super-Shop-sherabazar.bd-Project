<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

// Save Supplier
if (isset($_POST['save'])) {

    $supplier_name = $_POST['supplier_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("
        INSERT INTO suppliers
        (supplier_name, phone, email, company_name, address)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssss",
        $supplier_name,
        $phone,
        $email,
        $company_name,
        $address
    );

    $stmt->execute();

    header("Location: suppliers.php");
    exit();
}

$result = $conn->query("SELECT * FROM suppliers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Supplier Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<h2>Supplier Management</h2>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Supplier Name</label>
<input type="text" name="supplier_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Company Name</label>
<input type="text" name="company_name" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Address</label>
<textarea name="address" class="form-control"></textarea>
</div>

</div>

<button class="btn btn-success" name="save">
Save Supplier
</button>

</form>

<hr>

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Supplier</th>
<th>Company</th>
<th>Phone</th>
<th>Email</th>
<th>Address</th>
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['supplier_name']); ?></td>

<td><?= htmlspecialchars($row['company_name']); ?></td>

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