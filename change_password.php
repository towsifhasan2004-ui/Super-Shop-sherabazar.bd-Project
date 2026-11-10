<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: user_management.php");
    exit();
}

$id = (int)$_POST['id'];
$password = trim($_POST['password']);

if ($password == "") {
    die("Password cannot be empty.");
}

// If you later use password_hash(), replace the next line with:
// $password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $password, $id);

if ($stmt->execute()) {

    header("Location: user_management.php?msg=password_changed");
    exit();

} else {

    echo "Failed to change password.";

}
?>