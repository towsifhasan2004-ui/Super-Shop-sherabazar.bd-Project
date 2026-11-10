<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
}

header("Location: products.php");
exit();
?>