<?php include './database/db.php';
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("i", $_SESSION["username"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];
$stmt->close(); ?>

<div class="row  justify-content-center frames">

    <?php
    $sql = 'SELECT * FROM products LIMIT 3';
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
    }; ?> </div>