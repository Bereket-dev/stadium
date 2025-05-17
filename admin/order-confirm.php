<?php include '../database/db.php';
$order_id = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $order_id = $_GET['id'];
} else {
    header("Location: product-management.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM `order` WHERE  id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$product_id = $row["product_id"];
$quantity = $row["quantity"];

$order_status = $row["status"];
if ($order_status == 'pending') {
    $stmt = $conn->prepare("UPDATE `order` SET `status` = 'processing' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
} else if ($order_status == 'processing') {
    $stmt = $conn->prepare("UPDATE `order` SET `status` = 'delivered' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM `product` WHERE  id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$product_amount = $row["product_amount"];
$new_product_amount = $product_amount - $quantity;

$stmt = $conn->prepare("UPDATE `product` SET `product_amount` = ? WHERE id = ?");
$stmt->bind_param("ii", $new_product_amount, $product_id);
$stmt->execute();
$stmt->close();

$_SESSION["message"] = '';
header("Location: ./product-management.php");
exit();
