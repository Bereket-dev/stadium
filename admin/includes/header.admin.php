<?php
$totalUsers = 0;
$sql = "SELECT * FROM user ";
$result = $conn->query(query: $sql);
$totalUsers = $result->num_rows;

$sql = "SELECT * FROM booking ";
$result = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$confirmedRevenue = 0;
foreach ($result as $row) {
  $confirmedRevenue += (int)$row["price"];
}


$sql = "SELECT * FROM booking WHERE `status` = 'confirmed'";
$result = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
$totalBookings = 0;
foreach ($result as $row) {
  $totalBookings += (int)$row["quantity"];
}

// Initialize projected revenue
$projectedRevenue = 0;

// Calculate total projected revenue by summing (seat_amount * seat_price) for all seat types
$sql = "SELECT seat_amount, seat_price FROM seattype";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projectedRevenue += (int)$row['seat_amount'] * (float)$row['seat_price'];
    }
} else {
    // Handle error or no results
    $projectedRevenue = 0;
}




?><div class="content">
  <div class="row text-primary gap-2">
    <div class="col text-center box-shadow p-3">
      <div class=""><?php echo $totalUsers ?></div>
      <div class=" ">Total&nbsp;users</div>
    </div>
    <div class="col text-center box-shadow p-3">
      <div class=""><?php echo  $totalBookings ?></div>
      <div class="">Total&nbsp;bookings</div>
    </div>
    <div class="col text-center box-shadow p-3">
      <div class=""><?php echo '$' .  $confirmedRevenue ?></div>
      <div class="">Confirmed&nbsp;revenue</div>
    </div>
    <div class="col text-center box-shadow p-3">
      <div class=""><?php echo '$' .  $projectedRevenue ?></div>
      <div class="">Projected&nbsp;revenue</div>
    </div>
  </div>
</div>