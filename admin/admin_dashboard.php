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
    <title>Admin Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <div class="side_bar position-fixed bg-primary text-white ps-4 py-5">
        <div class="myacount d-flex align-items-center gap-2">
            <div class="admin_image position-relative bg-secondary"><img src="" alt="" class="postion-absolut img-fluid"></div>username
        </div>
        <div class="mt-5 d-flex flex-column gap-3">
            <a href="./admin_dashboard.php" class="border-bottom text-decoration-none text-white">Dashboard</a>
            <a href="./book-management.php" class="border-bottom  text-decoration-none text-white">Booking Management</a>
            <a href="./event-management.php" class=" text-decoration-none text-white">Event Management</a>
        </div>
    </div>
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
        <div class="row">
            <div class="col box-shadow">
                <div class="d-flex justify-content-between p-3">
                    <div class="text-primary mt-5" style="font-size: 20px;">
                        St. Georgis Vs Fassil Kenema
                    </div>
                    <div class="pie">
                        <canvas id="myPieChart1" width="220" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="col box-shadow">
                <div class="d-flex justify-content-between p-3">
                    <div class="text-primary mt-5" style="font-size: 20px;">
                        St. Georgis Vs Fassil Kenema
                    </div>
                    <div class="pie">
                        <canvas id="myPieChart2" width="220" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col box-shadow">
                <div class="d-flex justify-content-between p-3">
                    <div class="text-primary mt-5" style="font-size: 20px;">
                        St. Georgis Vs Fassil Kenema
                    </div>
                    <div class="pie">
                        <canvas id="myPieChart3" width="220" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="col box-shadow">
                <div class="d-flex justify-content-between p-3">
                    <div class="text-primary mt-5" style="font-size: 20px;">
                        St. Georgis Vs Fassil Kenema
                    </div>
                    <div class="pie">
                        <canvas id="myPieChart4" width="220" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/main.js"></script>



</body>
<script>
    //piechart
    document.addEventListener("DOMContentLoaded", () => {
        const ctx1 = document.getElementById("myPieChart1").getContext('2d');
        const ctx2 = document.getElementById("myPieChart2").getContext('2d');
        const ctx3 = document.getElementById("myPieChart3").getContext('2d');
        const ctx4 = document.getElementById("myPieChart4").getContext('2d');

        // Define data correctly
        const data = {
            labels: ['Available', 'Pending', 'Booked'],
            datasets: [{
                label: 'My First Dataset',
                data: [300, 50, 100],
                backgroundColor: [
                    'rgb(112, 133, 163)',
                    'rgb(54, 162, 235)',
                    'rgb(10, 101, 236)'
                ],
                hoverOffset: 4
            }]
        };

        // Define config correctly
        const config = {
            type: 'doughnut',
            data: data, // Reference data object
            options: { // Fixed from "Options"
                plugins: { // Fixed from "Plugins"
                    legend: {
                        display: true,
                        position: 'bottom' // Fixed missing quotes
                    }
                }
            }
        };

        // Create chart correctly
        new Chart(ctx1, config);
        new Chart(ctx2, config);
        new Chart(ctx3, config);
        new Chart(ctx4, config);
    });
</script>

</html>