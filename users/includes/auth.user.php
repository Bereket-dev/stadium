<?php

if (!isset($_SESSION['username']) || !isset($_SESSION["roles"])) {
    header("Location: ../auth/login.php");
    exit();
}
