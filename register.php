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
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email has been used first!";
        $stmt->close();
    } else {
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO users(username, email, password_hash) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $result = $stmt->execute();

        if ($result) {
            session_start();
            $_SESSION["username"] = $username;
            $stmt->close();
            $conn->close();
            header("Location: dashboard.php");
            exit();
        }
    }
}


?>

<form method="post">
    <div>
        <label for="username">Username: </label>
        <input type="text" id="username" name="username" required>
    </div>
    <br>
    <div>
        <label for="email">Email: </label>
        <input type="email" id="email" name="email" required>
    </div>
    <br>
    <div>
        <label for="password">Password: </label>
        <input type="password" id="password" name="password" required>
    </div>
    <br>
    <input type="submit">
</form>