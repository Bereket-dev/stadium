<?php
include("db.php");

$username = "";
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["username"])) {
        $username = trim($_POST["username"]);
    }
    if (isset($_POST["email"])) {
        $email = trim($_POST["email"]);
    }
    if (isset($_POST["password"])) {
        $password = trim($_POST["password"]);
    }

    if (empty($username) || empty($password)) {
        echo "Data field needed!";
        goto jump_here;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? ");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (password_verify($password, $row["password_hash"])) {

        session_start();
        $_SESSION["username"] = $row["username"];
        $_SESSION["roles"] = $row["roles"];

        if ($row["roles"] == "admin") {
            header("Location: admin_dashboard.php");
            exit();
        } else if ($row["roles"] == "user") {
            header("Location: dashboard.php");
            exit();
        }

        echo "no role!";
    } else {
        echo "Invalid username or password!";
    }
    jump_here:
    $conn->close();
}
?>

<form method="post">
    <div>
        <label for="username">Username: </label>
        <input type="text" id="username" name="username" required>
    </div>
    <br>
    <div>
        <label for="password">Password: </label>
        <input type="password" id="password" name="password" required>
    </div>
    <br>
    <input type="submit">
</form>