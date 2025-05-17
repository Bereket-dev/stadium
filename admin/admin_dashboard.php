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

    <!-- side bar and header-->
    <?php
    include './includes/sidebar.php';
    include './includes/header.admin.php';

    ?>


    <div class="content">
        <div class="row justify-content-center gap-3">
            <?php
            $event_result = $conn->query("SELECT * FROM `event`");

            while ($event = $event_result->fetch_assoc()) {
                echo '<div class="col-auto box-shadow" style="width: 500px;">';
                echo '<div class="d-flex justify-content-between p-3">';
                echo '<div class="text-primary mt-5" style="font-size: 20px;">';
                echo $event["event_name"];
                echo '</div>';
                echo '<div class="pie">';
                echo '<canvas class="myPieChart" data-event-id="' . $event["id"] . '" style="width: 255px; height: 255px;"></canvas>'; // Fixed inline styles
                echo '</div></div></div>';
            }
            ?>
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
    const eventElements = document.querySelectorAll(".myPieChart");
    eventElements.forEach(eventElement => {
        const eventId = eventElement.getAttribute("data-event-id"); // Get the event ID
        fetch(`events_data.php?event_id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                const ctx = eventElement.getContext('2d'); // Use the current element's context
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Available', 'Booked', 'Selected'],
                        datasets: [{
                            label: 'Seats Status',
                            data: [data.available, data.booked, data.selected],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.6)', // Available - Green
                                'rgba(255, 99, 132, 0.6)', // Booked - Red
                                'rgba(255, 206, 86, 0.6)' // Selected - Yellow
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching seat data:', error));
    });
</script>

</script>

</html>