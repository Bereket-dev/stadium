<?php
session_start(); //to check the user was logged in
include '../database/db.php'; //include database connection
include './includes/auth.user.php';



$message1 = "";
$message2 = "";


$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("i", $_SESSION["username"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];
$stmt->close();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
    }
    if (isset($_POST['quantity'])) {
        $quantity = $_POST['quantity'];
    }
    if (isset($_POST['seat_number'])) {
        $seat_number = $_POST['seat_number'];
    }
    if (empty($product_id) || empty($quantity) || empty($seat_number)) {
        $message1 = "Data field needed";
        goto form;
    }
    // Fetch product price
    $stmt = $conn->prepare("SELECT product_price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product_price = $result->fetch_assoc()['product_price']; //it will fetch the price only
    $total_price = $product_price * $quantity;
    $stmt->close();


    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $myorder = $result->fetch_assoc();
    if ($result->num_rows == 0) {

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, seat_number) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $user_id, $product_id, $quantity, $total_price, $seat_number);


        if ($stmt->execute()) {
            $message1 = "Your order has registered!";
        } else {
            $message1 = "Failed to place order.";
        }
    } else {
        $message1 = "Please the product is on " . $myorder['status']  . " !";
    }
}
form:
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
    <link rel="stylesheet" href="../assets/CSS/styles.css">

</head>

<body>
    <?php include './includes/header.php'; ?>

    <div class="event-banner event-banner-calendar">
        <div class="container">
            <div class="calling-text" style="color: white;width: 50%;">
                <h1>YOUR&nbsp;ENTERTAINMENT IS OUR GOAL</h1>
            </div>
            <a href="./users.event.calendar.php" class="btn btn-primary">Book An Event</a>
        </div>
    </div>
    <p class="text-center mt-5"><?php echo $message1; ?></p>
    <div class="row  justify-content-center frames">
        <?php

        $sql = 'SELECT * FROM products';
        $result = $conn->query($sql);
        while ($product = $result->fetch_assoc()) {
            echo '<div id = " class="col-auto m-2" style="width: max-content">';
            echo '<div class="card"  style="height: max-content">';
            echo '<img src="../assets/Images/uploaded/' . $product["product_image"] . '" style="width: 100%;" class="card-img-top image-fluid " alt="...">';
            echo '<div class="card-body p-2">';
            echo '<h5 class="card-title">' . ucwords($product["product_name"]) . '</h5>';
            echo '<h5 class="card-title">' . $product["product_price"] . '</h5>';
            echo '<div class="horizontal-line"></div>';
            echo '<div class="justify-content-between align-items-end">';

            $stmt2 = $conn->prepare("SELECT `status` FROM orders WHERE user_id = ? AND product_id = ?");
            $stmt2->bind_param("ii", $user_id, $product["id"]);
            $stmt2->execute();

            $orderresult = $stmt2->get_result();
            if ($orderresult->num_rows > 0) {
                $order = $orderresult->fetch_assoc();
                if ($order["status"] == 'processing') {
                    echo '<button type="" value = "" class="btn btn-secondary me-2 mb-2" style="font-size: 14px;">Processing</button>';
                } else if ($order["status"] == 'pending') {
                    echo '<button type="" value = "" class="btn btn-secondary me-2 mb-2" style="font-size: 14px;">Pending</button>';
                } else if ($order["status"] == 'cancelled') {
                    echo '<button type="" value = "" class="btn btn-secondary me-2 mb-2" style="font-size: 14px;">Cancelled</button>';
                }
            } else  if ($orderresult->num_rows == 0) {
                echo '<button type="button" onClick="showHiddenForm(' . $product["id"] . ')" class="btn btn-primary order-add-' . $product["id"] . ' me-2 mb-2" style="font-size: 14px;">Order -></button>';
                //hidden form
                echo '<form method="post" id = "order-form-' . $product["id"] . '"class = "order-input-area" style="display: none">';
                echo '<div class="col-md-6 input">';
                echo '<label for="productQuantity" class="form-label">Amount</label>';
                echo '<input type="number" class="form-control" name="quantity" id="productQuantity"required >';
                echo '</div>';
                echo '<div class="col-md-6 input">';
                echo '<label for="seatNumber" class="form-label">Seat Number</label>';
                echo '<input type="number" class="form-control" name="seat_number" id="seatNumber" required>';
                echo '</div>';
                echo '<button type="submit" name="product_id"  value = "' . $product["id"] . '" class="btn btn-primary me-2 my-2" style="font-size: 14px;">Submit</button>';
                echo '</form>';
            }

            echo '</div></div></div></div>';
        };
        ?></div> <?php include '../includes/footer.php'; ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="../assets/js/main.js"></script>
</body>

</html>