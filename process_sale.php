<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: sales.php");
    exit();
}

$customer = trim($_POST['customer_name']);
$payment = trim($_POST['payment_method']);
$cash_received = isset($_POST['cash_received']) ? (float)$_POST['cash_received'] : 0;
$change_amount = isset($_POST['change_amount']) ? (float)$_POST['change_amount'] : 0;
$due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;
$cartJson = $_POST['cart'];

$cart = json_decode($cartJson, true);

if (!$cart || count($cart) == 0) {
    die("Cart is empty.");
}

$conn->begin_transaction();

try {

    $invoice = "INV" . date("YmdHis");

    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }

    $discount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0;
    $vat = isset($_POST['vat']) ? (float)$_POST['vat'] : 0;
    $created_by = $_SESSION['username'];
    $status = 'Completed';

    // VAT is treated as a percentage
    $vatAmount = ($total - $discount) * ($vat / 100);
    $net_total = ($total - $discount) + $vatAmount;

    $stmt = $conn->prepare("
        INSERT INTO sales
        (
            invoice_no,
            customer_name,
            payment_method,
            cash_received,
            change_amount,
            total_amount,
            discount,
            vat,
            net_total,
            created_by,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssddddddss",
        $invoice,
        $customer,
        $payment,
        $cash_received,
        $change_amount,
        $total,
        $discount,
        $vat,
        $net_total,
        $created_by,
        $status
    );

    $stmt->execute();

    $sale_id = $conn->insert_id;

    if ($payment == "Due") {

        $stmt = $conn->prepare("
            INSERT INTO customer_dues
            (
                sale_id,
                customer_name,
                total_due,
                paid_amount,
                remaining_due,
                due_date,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $paid = 0;
        $remaining = $net_total;
        $status_due = "Pending";

        $stmt->bind_param(
            "isdddss",
            $sale_id,
            $customer,
            $net_total,
            $paid,
            $remaining,
            $due_date,
            $status_due
        );

        $stmt->execute();
    }

    foreach ($cart as $item) {

        $subtotal = $item['price'] * $item['qty'];

        $stmt = $conn->prepare("
            INSERT INTO sale_items
            (sale_id, product_id, quantity, price, subtotal)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiidd",
            $sale_id,
            $item['id'],
            $item['qty'],
            $item['price'],
            $subtotal
        );

        $stmt->execute();

        $stmt = $conn->prepare("
            UPDATE products
            SET stock = stock - ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ii",
            $item['qty'],
            $item['id']
        );

        $stmt->execute();
    }

    $conn->commit();

    header("Location: invoice.php?id=".$sale_id);

} catch (Exception $e) {

    $conn->rollback();

    die("Error : ".$e->getMessage());

}
?>