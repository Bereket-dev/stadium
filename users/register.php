<?php
include("../database/db.php");

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

    if (empty($username) || empty($email) || empty($password)) {
        echo "Data field needed!";
        goto jump_here;
    }




    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Use unique email and username!";
        $stmt->close();
    } else {
        $stmt->close();

        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

        if (!preg_match($passwordPattern, $password)) {
            echo "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
            goto jump_here;
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

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

jump_here:
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