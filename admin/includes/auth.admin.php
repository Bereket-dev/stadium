<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION["roles"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["roles"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}
