<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';

$message2 = "";
$product_id  = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $product_id = $_GET['id'];
} else {
    header("Location: admin.product.calendar.php");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: product-management.php");
    exit();
}
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
        header("Location: product-management.php");
        exit();
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


    $stmt->close();
    $result->close();

    $stmt = $conn->prepare("UPDATE product SET product_name = ?, product_price = ?, product_amount = ?, product_image = ? WHERE id = ? ");
    $stmt->bind_param("siisi", $product_name, $product_price, $product_amount, $image_name, $product_id);
    $stmt->execute();
    $stmt->close();

    header("Location: product-management.php");
    exit();
}
form:
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| edit product</title>
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
        <div class="row justify-content-center mb-5">
            <div class="card product-form p-3">
                <div class=" text-center">
                    <p><?php echo $message2; ?></p>
                    <h2>Edit Product</h2>
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