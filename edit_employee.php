<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM employees WHERE id=$id");
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $joining_date = $_POST['joining_date'];

    mysqli_query($conn,
    "UPDATE employees
     SET
     name='$name',
     phone='$phone',
     email='$email',
     position='$position',
     salary='$salary',
     joining_date='$joining_date'
     WHERE id=$id");

    header("Location: employees.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Employee</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">

<h2>Edit Employee</h2>

<form method="POST">

<label>Name</label>
<input type="text" name="name" class="form-control"
value="<?php echo $row['name']; ?>">

<label class="mt-3">Phone</label>
<input type="text" name="phone" class="form-control"
value="<?php echo $row['phone']; ?>">

<label class="mt-3">Email</label>
<input type="email" name="email" class="form-control"
value="<?php echo $row['email']; ?>">

<label class="mt-3">Position</label>
<input type="text" name="position" class="form-control"
value="<?php echo $row['position']; ?>">

<label class="mt-3">Salary</label>
<input type="number" name="salary" class="form-control"
value="<?php echo $row['salary']; ?>">

<label class="mt-3">Joining Date</label>
<input type="date" name="joining_date" class="form-control"
value="<?php echo $row['joining_date']; ?>">

<br>

<button class="btn btn-primary" name="update">
Update Employee
</button>

<a href="employees.php" class="btn btn-secondary">
Cancel
</a>

</form>

</body>
</html>