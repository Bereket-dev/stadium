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
    <!-- side bar -->
    <?php include './includes/sidebar.html'; ?>

    <div class="content">
        <div class="row text-primary gap-2">
            <div class="col text-center box-shadow p-3">
                <div class="">50</div>
                <div class=" ">Total&nbsp;users</div>
            </div>
            <div class="col text-center box-shadow p-3">
                <div class="">500</div>
                <div class="">Total&nbsp;bookings</div>
            </div>
            <div class="col text-center box-shadow p-3">
                <div class="">$2000</div>
                <div class="">Confirmed&nbsp;revenue</div>
            </div>
            <div class="col text-center box-shadow p-3">
                <div class="">$10000</div>
                <div class="">Projected&nbsp;revenue</div>
            </div>
        </div>
    </div>

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
            <div class="row   border-top mt-2">
                <div class="col">bereket23423@gmail.com</div>
                <div class="col-2">2025: 03: 24</div>
                <div class="col-2">Pending</div>
                <div class="col-2"><a href="#" id="" class="btn mt-1  btn-secondary">Cancel</a></div>
                <div class="col-2"><a href="" class="btn mt-1 btn-primary">Confirm</a></div>
            </div>
            <div class="row   border-top mt-2">
                <div class="col">bereket23423@gmail.com</div>
                <div class="col-2">2025: 03: 24</div>
                <div class="col-2">Pending</div>
                <div class="col-2"><a href="#" id="" class="btn mt-1  btn-secondary">Cancel</a></div>
                <div class="col-2"><a href="" class="btn mt-1 btn-primary">Confirm</a></div>
            </div>
            <div class="row   border-top mt-2">
                <div class="col">bereket23423@gmail.com</div>
                <div class="col-2">2025: 03: 24</div>
                <div class="col-2">Pending</div>
                <div class="col-2"><a href="#" id="" class="btn mt-1  btn-secondary">Cancel</a></div>
                <div class="col-2"><a href="" class="btn mt-1 btn-primary">Confirm</a></div>
            </div>
        </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="../assets/js/main.js"></script>



</body>


</html>