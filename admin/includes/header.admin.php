<?php

$sql = "SELECT * FROM users ";
$result = $conn->query($sql)->fetch_assoc();
$totalUsers = count($result);

$sql = "SELECT * FROM bookings ";
$result = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$confirmedRevenue = 0;
foreach ($result as $row) {
  $confirmedRevenue += (int)$row["price"];
}


$sql = "SELECT * FROM bookings WHERE `status` = 'confirmed'";
$result = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
$totalBookings = 0;
foreach ($result as $row) {
  $totalBookings += (int)$row["quantity"];
}

$sql = "SELECT * FROM seattype ";
$result = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
$projectedRevenue = count($result);

$projectedRevenue = 0;
foreach ($result as $row) {
  $projectedRevenue += (int)$row["seat_price"];
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