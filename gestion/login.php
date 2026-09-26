<?php
session_start();
include "db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uname']) && isset($_POST['password'])) {

        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $uname = validate($_POST['uname']);
        $pass = validate($_POST['password']);

        if (empty($uname)) {
            header("Location: login.blade.php?error=User Name is required");
            exit();
        } else if (empty($pass)) {
            header("Location: login.blade.php?error=Password is required");
            exit();
        } else {
            $sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                if ($row['user_name'] === $uname && $row['password'] === $pass) {
                    $_SESSION['user_name'] = $row['user_name'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['id'] = $row['id'];
                    header("Location: home.blade.php");
                    exit();
                } else {
                    header("Location: login.blade.php?error=Incorrect User name or password");
                    exit();
                }
            } else {
                header("Location: login.blade.php?error=Incorrect User name or password");
                exit();
            }
        }
    } else {
        header("Location: login.blade.php");
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" type="image/png" href="image/logo.png">
    <title>GoldGrasp | Login </title>

</head>

<body>

    <div class="wrapper">
      
        <form action="login.php" method="POST">
            <p class="form-login">Login</p>
            <div class="input-box">
                <input required="" placeholder="Username" type="text" name="uname" />
            </div>
            <div class="input-box">
                <input required="" placeholder="Password" type="password" name="password" />
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" />Remember Me</label>

            </div>
            <button class="btn" type="submit" onclick="login()" name="login">Login</button>
            <div class="register-link">
                <p>Don't remembre my password? <a href="#">Forgot Password</a></p>
            </div>
        </form>
    </div>

    <script>
    function login() {

        window.location.href = "chargeur.php";
    }
    </script>
</body>


</html>