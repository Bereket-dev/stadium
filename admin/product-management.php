<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';
$message = "";
$message1 = "";
$message2 = "";

//check form date are setted or not
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["product_name"])) {
        $product_name = trim($_POST["product_name"]);
    }
    if (isset($_POST["product_price"])) {
        $product_price = trim($_POST["product_price"]);
    }
    if (isset($_POST["product_amount"])) {
        $product_amount = trim($_POST["product_amount"]);
    }
    if (empty($product_name) || empty($product_price) || empty($product_amount)) {
        $message2 = "Data field needed!";
        goto form;
    }


    $stmt = $conn->prepare("SELECT * FROM product WHERE product_name = ?");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();

    $result = $stmt->get_result();

    //if the product is new it will be registered
    if ($result->num_rows > 0) {
        $message2 = "This product has already registered!";
        goto form;
    }

    //to prevent uploading occured event image first
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image_name = $_FILES["image"]["name"];
        $tmp_name = $_FILES["image"]["tmp_name"];
        $folder = '../assets/Images/uploaded/' . $image_name;

        if (!move_uploaded_file($tmp_name, $folder)) {
            $message2 = 'file not uploaded!';
            goto form;
        }
    } else {
        $message2 = 'no file input';
        goto form;
    }



    if ($result->num_rows == 0) {
        $stmt->close();
        $result->close();

        $stmt = $conn->prepare("INSERT INTO product(product_name, product_price, product_amount, product_image) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("siis", $product_name, $product_price, $product_amount, $image_name);

        if ($stmt->execute()) {
            $message2 = 'This product has registered successfully!';
        } else {
            $message2 = "Please try again later!";
        }
        $stadium_id = $stmt->insert_id;
        $stmt->close();
    }
}
form:
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Orders</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <!-- side bar -->
    <?php
    include './includes/sidebar.php';  ?>


    <div class="content">
        <p><?php echo $message1; ?></p>
    </div>


    <div class="content">
        <div class="p-5 box-shadow">
            <div class="text-center"><?php echo $message; ?></div>
            <div class="text-center mb-4" style="font-size: 25px;">Orders</div>
            <div class="row   ">
                <div class="col-2">Product</div>
                <div class="col-2">Quantity</div>
                <div class="col-2">Seat Number</div>
                <div class="col-2">Total Price</div>
                <div class="col-2">Status</div>

                <div class="col-1"></div>
                <div class="col-1"></div>
            </div>
            <?php
            $sql = "SELECT * FROM `order`";
            $orderresult = $conn->query($sql);
            while ($order = $orderresult->fetch_assoc()) {
                $stmt2 = $conn->prepare("SELECT product_name FROM product WHERE id = ?");
                $stmt2->bind_param("i", $order["product_id"]);
                $stmt2->execute();
                $stmt2->bind_result($product);
                $stmt2->fetch();
                $stmt2->close();

                echo '<div class="row   border-top mt-2">';
                echo  '<div class="col">' . $product . '</div>';
                echo  '<div class="col">' . $order["quantity"] . '</div>';
                echo    '<div class="col-2">' . $order["seat_number"] . '</div>';
                echo   '<div class="col-2">' . $order["total_price"] . '</div>';
                echo   '<div class="col-2">' . $order["status"] . '</div>';
                echo  '<div class="col-1"><a href="./order-cancel.php?id=' . $order["id"] . '" id="" class="btn mt-1  btn-secondary">Cancel</a></div>';
                echo  '<div class="col-1"><a href="./order-confirm.php?id=' . $order["id"] . '" class="btn mt-1 btn-primary">Confirm</a></div>';
                echo '</div>';
            }
            ?>

        </div>
    </div>


    <div class="content">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class=" text-center">
                    <h2>Product List</h2>
                </div>
                <div class="row justify-content-between border border-primary p-3">
                    <div class="col-3">Product Name</div>
                    <div class="col-3">Price</div>
                    <div class="col-3">Amount</div>
                    <div class="col-3"></div>
                </div>

                <?php
                $sql = 'SELECT * FROM product';
                $result = $conn->query($sql);

                while ($product = $result->fetch_assoc()) {
                    echo '<div class="row justify-content-between border-bottom border-primary p-3 input-area" >';
                    echo '<div class="col-3 stattype-area" >' . $product["product_name"] . '</div>';
                    echo '<div class="col-3">' . $product["product_price"] . '</div>';
                    echo '<div class="col-3 quantity-area">' . $product["product_amount"] . '</div>';
                    echo  '<div class="col-1"><a href="./product-edit.php?id=' . $product["id"] . '" id="" class="btn mt-1  btn-secondary">Edit</a></div>';
                    echo  '<div class="col-1"><a href="./product-delete.php?id=' . $product["id"] . '" id="" class="btn mt-1  btn-danger">Remove</a></div>';
                    echo '</div>';
                };

                ?>
            </div>
        </div>
    </div>



    <div class="content">
        <div class="row justify-content-center mb-5">
            <div class="card product-form p-3">
                <div class=" text-center">
                    <p><?php echo $message2; ?></p>
                    <h2>New Product</h2>
                </div>
                <div class="container  p-3" style="max-width: 80vw;">
                    <form class="row g-3" method="post" enctype="multipart/form-data">
                        <div class="col-md-6">
                            <label for="inputEmail4" class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" id="inputProductName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="inputAddress" class="form-label">Price</label>
                            <input type="number" name="product_price" class="form-control" id="" placeholder="" required>
                        </div>
                        <div class="col-md-6">
                            <label for="inputCity" class="form-label">Amount</label>
                            <input type="number" class="form-control" name="product_amount" id="" required>
                        </div>

                        <div class="col-md-6">
                            <label for="formFile" class="form-label">Product image</label>
                            <input class="form-control" name="image" type="file" id="formFile" required>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>