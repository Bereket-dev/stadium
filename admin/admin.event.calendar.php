<?php
include("../database/db.php");
include("./includes/header.admin.html");

$result = $conn->query("SELECT * FROM events");

echo '<div class="container">';
echo '<div class="row">';

foreach ($result as $row) {
    echo '<div class="col-auto m-2">';
    echo '<div class="card" style="width: 18rem;">';
    echo '      <img src="" style="height: 100px; width: 100%;" class="card-img-top" alt="...">';
    echo '<div class="scard-body p-2">';
    echo '<h5 class="card-title">';

    echo "<br>" . $row["event_name"] . "<br>";
    echo '</h5>';
    echo '<p class="card-text">' . $row["event_description"] . '</p>';
    echo '<a href="./event.edit.php?id=' . $row["id"] . '" class="btn btn-primary me-2" style="font-size: 14px;">EDIT EVENT</a>';
    echo '<a href="./event.delete.php?id=' . $row["id"] . '" class="btn btn-danger" style="font-size: 14px;">REMOVE EVENT</a>';
    echo '</div></div></div>';
}

echo '</div></div>';
