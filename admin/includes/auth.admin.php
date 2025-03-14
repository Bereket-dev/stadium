<?php
if (!isset($_SESSION['username']) || !isset($_SESSION["roles"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["roles"] !== "admin") {
    echo 'anauthorized acces!';
    exit();
}
