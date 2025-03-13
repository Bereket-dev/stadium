<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <!-- side bar and header-->
    <?php
    include './includes/sidebar.php';  
    include './includes/header.admin.php';

    ?>

    <div class="content">
        <div class="p-5 box-shadow">
            <div class="text-center mb-4" style="font-size: 25px;">Book Confirmation</div>
            <div class="row   ">
                <div class="col">Email</div>
                <div class="col-2">Date</div>
                <div class="col-2">Status</div>
                <div class="col-2"></div>
                <div class="col-2"></div>
            </div>
            <?php
            $sql = "SELECT * FROM bookings";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo '<div class="row   border-top mt-2">';
                echo  '<div class="col">' . $row["email_address"] . '</div>';
                echo    '<div class="col-2">' . $row["booking_date"] . '</div>';
                echo   '<div class="col-2">' . $row["status"] . '</div>';
                echo  '<div class="col-2"><a href="./book-cancel.php?id=' . $row["id"] . '" id="" class="btn mt-1  btn-secondary">Cancel</a></div>';
                echo  '<div class="col-2"><a href="./book-confirm.php?id=' . $row["id"] . '" class="btn mt-1 btn-primary">Confirm</a></div>';
                echo '</div>';
            }
            ?>

        </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="../assets/js/main.js"></script>



</body>


</html>