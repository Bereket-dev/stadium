<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';

$product_id = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $product_id = $_GET['id'];
} else {
    header("Location: product-management.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "product not found!";
    exit();
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$conn->close();
header("Location: product-management.php");
exit();
