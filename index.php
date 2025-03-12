<a?php
    session_start(); //to check the user was logged in
    include './database/db.php' ; //include database connection
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
        <div class="container frames">
            <div class="container-title text-center my-5">
                <h1>UPCOMING EVENTS</h1>
            </div>

            <!-- cards -->
            <div class="container" style="width: max-content;">
                <?php include './users/upcoming.events.php' ?>
                <div class="text-end"><a href="../users/users.event.calendar.php">See All</a></div>
            </div>



        </div>

        <!-- call to action -->
        <div class="call-to-action frames">
            <div class="container">
                <div class="calling-text" style="color: white;width: 50%;">
                    <h1>YOUR&nbsp;ENTERTAINMENT IS OUR GOAL</h1>
                </div>
                <a href="./users/users.event.calendar.php" class="btn btn-primary">Book An Event</a>
            </div>
        </div>

        <!-- event information -->
        <div class="event-info frames mb-5">
            <div class="container-title text-center my-5">
                <h1>EVENT INFORMATION</h1>
            </div>

            <div class="enfo-container d-flex border border-primary" style="text-align: end;">
                <img src="./assets/Images/banner/info_image.png" alt="priemer league" class="image-fluid" style="max-width: 50vw;">
                <div class="container">
                    <div class="info-text">
                        <h1>ETHIOPIA'S&nbsp;HIGEST PROFESSIONAL <br> LEAGUE</h1>
                    </div>
                    <div style="margin-right: 5vw;"> <a href="./users/users.event.calendar.php" class="btn btn-primary">Book An Event</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="food-drinks frames container">
            <div class="container-title text-center my-5">
                <h1>FOOD & DRINKS</h1>
            </div>

            <!-- cards -->
            <div class="container" style="width: max-content;">
                <?php include './users/upcoming.events.php' ?>
                <div class="text-end"><a href="../users/users.event.calendar.php">See All</a></div>
            </div>
        </div>

        <?php include './includes/footer.php'; ?>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
            <script src="./assets/js/main.js"></script>
    </body>

    </html>