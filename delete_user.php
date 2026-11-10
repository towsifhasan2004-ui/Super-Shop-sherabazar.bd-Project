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

// Get the selected user
$stmt = $conn->prepare("SELECT username FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Prevent admin from deleting their own account
if ($user['username'] == $_SESSION['username']) {
    echo "<script>
            alert('You cannot delete your own account!');
            window.location='user_management.php';
          </script>";
    exit();
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: user_management.php?msg=deleted");
    exit();

} else {

    echo "Delete failed.";

}
?>