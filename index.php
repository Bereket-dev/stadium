<?php
session_start(); //to check the user was logged in
include '../database/db.php'; //include database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium| HOME</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body>
    <!-- header -->
    <?php include './users/includes/header.php'; ?>
    <div class="container-fluid home-bg">
    </div>

    <!-- Upcoming events -->
    < class="container" style="margin-top:100px;">
        <div class="container-title text-center my-5">
            <h1>UPCOMING EVENTS</h1>
        </div>

        <!-- cards -->
        <div class="container">
            <?php include './users/upcoming.events.php' ?>
            <div class="text-end"><a href="../users/users.event.calendar.php">See All</a></div>
        </div>

        </div>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
</body>

</html>