<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

// Save employee
if(isset($_POST['save'])){

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $joining_date = $_POST['joining_date'];

    $sql = "INSERT INTO employees(name,phone,email,position,salary,joining_date)
            VALUES('$name','$phone','$email','$position','$salary','$joining_date')";

    mysqli_query($conn,$sql);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Employee Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="container mt-5">

<h2>Employee Registration</h2>

<form method="POST">

<div class="row">

<div class="col-md-6">
<label>Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="col-md-6">
<label>Phone</label>
<input type="text" name="phone" class="form-control" required>
</div>

<div class="col-md-6 mt-3">
<label>Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-6 mt-3">
<label>Position</label>
<input type="text" name="position" class="form-control">
</div>

<div class="col-md-6 mt-3">
<label>Salary</label>
<input type="number" name="salary" class="form-control">
</div>

<div class="col-md-6 mt-3">
<label>Joining Date</label>
<input type="date" name="joining_date" class="form-control">
</div>

</div>

<br>

<button class="btn btn-success" name="save">
Add Employee
</button>

</form>

<hr>

<h3>Employee List</h3>

<table class="table table-bordered table-striped">

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Position</th>
    <th>Salary</th>
    <th>Joining Date</th>
    <th>Action</th>
</tr>

<?php

$result = mysqli_query($conn,"SELECT * FROM employees");

while($row = mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['phone']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['position']; ?></td>
<td><?php echo $row['salary']; ?></td>
<td><?php echo $row['joining_date']; ?></td>

<td>
    <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>

    <a href="delete_employee.php?id=<?php echo $row['id']; ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Are you sure?')">
       Delete
    </a>
</td>

</tr>

<?php } ?>

</table>

</body>
</html>