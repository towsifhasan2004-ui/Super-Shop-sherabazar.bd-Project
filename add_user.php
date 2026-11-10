<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$message = "";

if (isset($_POST['save'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if ($username == "" || $password == "") {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {

        // Check duplicate username
        $check = $conn->prepare("SELECT id FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "<div class='alert alert-danger'>Username already exists.</div>";

        } else {

            // For your current login system
            $stmt = $conn->prepare("INSERT INTO users(username,password,role) VALUES(?,?,?)");
            $stmt->bind_param("sss", $username, $password, $role);

            if ($stmt->execute()) {

                header("Location: user_management.php");
                exit();

            } else {

                $message = "<div class='alert alert-danger'>Failed to add user.</div>";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Add User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card{
    margin-top:40px;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card">

<div class="card-header bg-success text-white">

<h3>Add New User</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label>Username</label>

<input
type="text"
name="username"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Role</label>

<select
name="role"
class="form-select">

<option value="employee">Employee</option>

<option value="admin">Admin</option>

</select>

</div>

<button
type="submit"
name="save"
class="btn btn-success">

Save User

</button>

<a
href="user_management.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>