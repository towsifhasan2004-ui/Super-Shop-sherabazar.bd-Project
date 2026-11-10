<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: user_management.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

$message = "";

if (isset($_POST['update'])) {

    $username = trim($_POST['username']);
    $role = trim($_POST['role']);

    // Check duplicate username
    $check = $conn->prepare("SELECT id FROM users WHERE username=? AND id!=?");
    $check->bind_param("si", $username, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {

        $message = "<div class='alert alert-danger'>Username already exists.</div>";

    } else {

        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);

        if ($stmt->execute()) {

            header("Location: user_management.php");
            exit();

        } else {

            $message = "<div class='alert alert-danger'>Update Failed.</div>";

        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Edit User</title>

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

<div class="card-header bg-warning">

<h3>Edit User</h3>

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
value="<?php echo htmlspecialchars($user['username']); ?>"
required>

</div>

<div class="mb-3">

<label>Role</label>

<select
name="role"
class="form-select">

<option value="admin"
<?php if($user['role']=="admin") echo "selected"; ?>>

Admin

</option>

<option value="employee"
<?php if($user['role']=="employee") echo "selected"; ?>>

Employee

</option>

</select>

</div>

<button
type="submit"
name="update"
class="btn btn-primary">

Update User

</button>

<a
href="user_management.php"
class="btn btn-secondary">

Back

</a>

</form>

<hr>

<h5>Change Password</h5>

<form action="change_password.php" method="POST">

<input
type="hidden"
name="id"
value="<?php echo $user['id']; ?>">

<div class="mb-3">

<label>New Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button
class="btn btn-danger">

Change Password

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>