<?php
include "./include/conn-db.php";

session_start(); // Start the session at the beginning

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Sanitize and validate the table name based on user type
    $is_admin = isset($_POST["check_admin"]) && $_POST["check_admin"] == "check_admin";
    
    // Validate user type
    if ($is_admin) {
        // Ensure that only admin users are being authenticated for admin login
        $table = "admin";
    } else {
        $table = "users";
    }

    // Prepare the SQL statement securely
    $loginQuery = $mysql->prepare("SELECT password FROM $table WHERE username=?;");
    $loginQuery->bind_param("s", $username);
    $loginQuery->execute();
    $loginQuery->store_result();

    if ($loginQuery->num_rows > 0) {
        $loginQuery->bind_result($hashed_password);
        $loginQuery->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $username;
            $_SESSION["is_admin"] = $is_admin;
            
            // Redirect based on admin status
            $redirect_page = $is_admin ? "./admin_index.php" : "./index.php";
            header("Location: $redirect_page");
            exit();
        } else {
            echo "<script>alert('Wrong username and/or password');</script>";
        }
    } else {
        echo "<script>alert('Wrong username and/or password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <style>
        /* CSS Styling */
        form {
            border: 3px solid #f1f1f1;
            width: 300px;
            margin: 0 auto;
        }

        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .login_button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            opacity: 0.8;
        }

        .cancelbtn {
            width: auto;
            padding: 10px 18px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
        }

        .imgcontainer {
            text-align: center;
            margin: 24px 0 12px 0;
        }

        img.avatar {
            width: 40%;
            border-radius: 50%;
        }

        .container {
            padding: 16px;
        }

        span.psw {
            float: right;
            padding-top: 16px;
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.psw {
                display: block;
                float: none;
            }
            .cancelbtn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <br><br><br><br>
    <center><h1>Login</h1></center>
    <form method="post">
        <div class="container">
            <label for="username">Username</label>
            <input type="text" placeholder="Enter Username" name="username" required>
            <label for="psw">Password</label>
            <input type="password" placeholder="Enter Password" name="password" required>

            <input type="checkbox" name="check_admin" value="check_admin">
            <label for="check_admin">Admin</label>

            <input type="submit" name="submit" class="login_button" value="Login">
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <a href="./register.php" class="cancelbtn">Register</a>
        </div>
    </form>
</div>

</body>
</html>
