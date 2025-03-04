<?php
include("db.php");

$username = "";
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["username"])) {
        $username = $_POST["username"];
    }
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? ");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (password_verify($password, $row["password_hash"])) {
        session_start();
        $_SESSION["username"] = $row["username"];
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid username or password!";
    }

    $stmt->close();
}
$conn->close();

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