<?php
include "../database/db.php";
$message = "";

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
        $message = "Data field needed!";
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
            header("Location: ../admin/admin_dashboard.php");
            exit();
        } else if ($row["roles"] == "user") {
            header("Location: ../index.php");
            exit();
        }

        $message = "no role!";
    } else {
        $message =  "Invalid username or password!";
    }
    jump_here:
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/styles.css">

</head>

<body>

    <form method="post" class="mx-auto my-5 login-form" style="width: 500px;">
        <?php echo '<p class="text-center">' . $message . '</p>' ?>
        <!--- username -->
        <div class="mb-3">
            <label for="usernameInput" class="form-label">Username</label>
            <input type="text" class="form-control" id="usernameInput" name="username" aria-describedby="" required>
        </div>

        <div class="mb-3">
            <label for="passwordInput" class="form-label">Password</label>
            <input type="password" class="form-control" id="passwordInput" name="password" aria-describedby="" required>
        </div>



        <div class="">Create an acount? <a href="./register.php">signup</a></div>
        <br>
        <button type="submit" class="btn btn-primary">login</button>
    </form>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>