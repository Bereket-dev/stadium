<?php include '../database/db.php';
$order_id = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $order_id = $_GET['id'];
} else {
    header("Location: product-management.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM orders WHERE  id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$order_status = $row["status"];
if ($order_status == 'pending' || $order_status == 'processing') {
    $stmt = $conn->prepare("UPDATE orders SET `status` = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
} else if ($order_status == 'cancelled' || $order_status == 'delivered') {
    $stmt = $conn->prepare("DELETE FROM orders  WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}
$_SESSION["message"] = '';
header("Location: ./product-management.php");
exit();
