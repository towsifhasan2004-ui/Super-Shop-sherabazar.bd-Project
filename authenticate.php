<?php
session_start();
include 'config/db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {

    $user = mysqli_fetch_assoc($result);

    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: dashboard.php");
        exit();
    }

    if ($user['role'] == 'employee') {
        header("Location: sales.php");
        exit();
    }

} else {
    echo "Invalid Username or Password";
}
?>