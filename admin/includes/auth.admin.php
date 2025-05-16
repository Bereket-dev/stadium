<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION["role"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}
