<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: customer_due.php");
    exit();
}

$id = (int)$_POST['id'];
$payment = (float)$_POST['payment'];

// Get current due information
$stmt = $conn->prepare("
    SELECT paid_amount, remaining_due
    FROM customer_dues
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Due record not found.");
}

$row = $result->fetch_assoc();

$currentPaid = $row['paid_amount'];
$currentRemaining = $row['remaining_due'];

// Prevent invalid payment
if ($payment <= 0) {
    die("Payment amount must be greater than 0.");
}

// Prevent over payment
if ($payment > $currentRemaining) {
    die("Payment cannot be greater than remaining due.");
}

$newPaid = $currentPaid + $payment;
$newRemaining = $currentRemaining - $payment;

$status = "Pending";

if ($newRemaining <= 0) {
    $newRemaining = 0;
    $status = "Paid";
}

$stmt = $conn->prepare("
    UPDATE customer_dues
    SET
        paid_amount = ?,
        remaining_due = ?,
        status = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ddsi",
    $newPaid,
    $newRemaining,
    $status,
    $id
);

$stmt->execute();

header("Location: customer_due.php?success=1");
exit();
?>