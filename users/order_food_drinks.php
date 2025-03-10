<?php
session_start(); //to check the user was logged in
include '../database/db.php'; //include database connection
include './includes/auth.user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Fetch item price
    $sql = "SELECT price FROM food_drinks WHERE item_id = $item_id";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc(); //it will fetch the price only
    $total_price = $item * $quantity;

    // Insert order
    $sql = "INSERT INTO orders (user_id, item_id, quantity, total_price) 
            VALUES ({$_SESSION['user_id']}, $item_id, $quantity, $total_price)";
    if ($conn->query($sql)) {
        header('Location: view_orders.php');
        exit();
    } else {
        $error = "Failed to place order.";
    }
}

// Fetch all food/drinks items
$sql = "SELECT * FROM food_drinks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stadium|food & drinks</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
</head>

<body>
    <?php include './includes/header.php'; ?>

    <h1>Order Food/Drinks</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <label for="item_id">Select Item:</label>
        <select name="item_id" required>
            <?php while ($item = $result->fetch_assoc()): ?>
                <option value="<?= $item['item_id'] ?>"><?= $item['name'] ?> - $<?= $item['price'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" min="1" required>
        <br><br>
        <button type="submit">Place Order</button>
    </form>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>