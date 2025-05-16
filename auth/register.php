<?php
include "../database/db.php";

$log = "login";

$username = "";
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["first_name"])) {
        $first_name = trim($_POST["first_name"]);
    }
    if (isset($_POST["last_name"])) {
        $last_name = trim($_POST["last_name"]);
    }
    if (isset($_POST["email"])) {
        $email = trim($_POST["email"]);
    }
    if (isset($_POST["password"])) {
        $password = trim($_POST["password"]);
    }

    if (empty($first_name) || empty($last_name) ||  empty($email) || empty($password)) {
        echo "Data field needed!";
        goto jump_here;
    }




    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This email has registered first!";
        $stmt->close();
    } else {
        $stmt->close();

        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

        if (!preg_match($passwordPattern, $password)) {
            echo "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
            goto jump_here;
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user(first_name, last_name, email, password_hash) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);
        $result = $stmt->execute();
        $user_id = $stmt->insert_id;
        if ($result) {
            session_start();
            $_SESSION["user_id"] = $user_id;
            $stmt->close();
            $conn->close();
            header("Location: ../index.php");
            exit();
        }
    }
}

jump_here:
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
        <!--- username -->
        <div class="mb-3">
            <label for="usernameInput" class="form-label">First Name</label>
            <input type="text" class="form-control" id="usernameInput" name="first_name" aria-describedby="" required>
        </div>

        <div class="mb-3">
            <label for="usernameInput" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="usernameInput" name="last_name" aria-describedby="" required>
        </div>

        <div class="mb-3">
            <label for="emailInput" class="form-label">Email</label>
            <input type="email" class="form-control" id="emailInput" name="email" aria-describedby="" required>
        </div>

        <div class="mb-3">
            <label for="passwordInput" class="form-label">Password</label>
            <input type="password" class="form-control" id="passwordInput" name="password" aria-describedby="" required>
        </div>



        <div class="">Already have an acount? <a href="./login.php">login</a></div>
        <br>
        <button type="submit" class="btn btn-primary">Signup</button>
    </form>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>