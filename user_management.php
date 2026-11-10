<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:#212529;
}

.sidebar h3{
    color:white;
    text-align:center;
    padding:20px;
}

.sidebar a{
    display:block;
    color:white;
    padding:15px;
    text-decoration:none;
}

.sidebar a:hover{
    background:#0d6efd;
}

.content{
    margin-left:250px;
    padding:30px;
}

.card{
    border:none;
    border-radius:10px;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Super Shop</h3>

<a href="dashboard.php">🏠 Dashboard</a>
<a href="employees.php">👨 Employees</a>
<a href="products.php">📦 Products</a>
<a href="sales.php">💰 Sales</a>
<a href="reports.php">📈 Reports</a>
<a href="customer_due.php">💳 Customer Due</a>
<a href="logout.php">🚪 Logout</a>

</div>

<div class="content">

<?php
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'deleted') {
        echo '<div class="alert alert-success">User deleted successfully.</div>';
    }

    if ($_GET['msg'] == 'password_changed') {
        echo '<div class="alert alert-success">Password changed successfully.</div>';
    }
}
?>

<div class="card">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between">

<h3>User Management</h3>

<a href="add_user.php" class="btn btn-light">

+ Add User

</a>

</div>

</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Username</th>
<th>Role</th>
<th width="220">Action</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['username']); ?></td>

<td>

<?php

if($row['role']=="admin"){

echo "<span class='badge bg-danger'>Admin</span>";

}else{

echo "<span class='badge bg-success'>Employee</span>";

}

?>

</td>

<td>

<a
href="edit_user.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a
href="delete_user.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this user?')">

Delete

</a>

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

</body>
</html>