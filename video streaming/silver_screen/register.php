<?php
include "./include/conn-db.php";

if (isset($_POST["submit"]) && $_POST["submit"] == "Register") {
    $errors = [];
    $profile_upload_flag = 0;
    $pname = null;

    // File upload validation
    if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
        if ($_FILES['image']['size'] < 4000000) { // Max size 4MB
            $file_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['image']['type'], $file_types)) {
                $pname = uniqid() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], 'avatar/' . $pname)) {
                    $profile_upload_flag = 1;
                } else {
                    $errors[] = 'Image upload failed';
                }
            } else {
                $errors[] = 'Invalid image type. Only JPEG, PNG, and JPG are allowed.';
            }
        } else {
            $errors[] = 'Image size exceeds 4MB.';
        }
    } else {
        $errors[] = 'Please upload an image.';
    }

    // Password confirmation
    if ($_POST["password"] !== $_POST["psw-confirm"]) {
        $errors[] = 'Passwords do not match.';
    }

    // Data duplication check
    $checkQuery = $mysql->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR phone_no = ?");
    $checkQuery->bind_param("sss", $_POST["username"], $_POST["email"], $_POST["phone"]);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    if ($result->num_rows > 0) {
        $errors[] = 'Username, email, or phone number already exists.';
    }

    // Data validation
    if (!preg_match("/^[A-Za-z0-9_]+$/", $_POST["username"])) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }
    if (strlen($_POST["password"]) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address format.';
    }
    if (!preg_match("/^98\d{8}$/", $_POST["phone"])) {
        $errors[] = 'Phone number must be in the format 98XXXXXXXX.';
    }

    // Registration if no errors
    if (empty($errors) && $profile_upload_flag === 1) {
        $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $flag_premium = "free";
        $flag_admin = 0;

        $registerQuery = $mysql->prepare("INSERT INTO users (username, password, email, phone_no, avatar, flag_admin, flag_premium, expire_premium) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $registerQuery->bind_param("ssssssss", $_POST["username"], $hashed_password, $_POST["email"], $_POST["phone"], $pname, $flag_admin, $flag_premium, date("Y-m-d"));

        if ($registerQuery->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }

    // Display errors
    foreach ($errors as $error) {
        echo "<script>alert('$error');</script>";
    }
    if (!empty($errors)) {
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    }
    
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Form</title>
    <style>
        /* CSS styles here */
        form {
    border: 3px solid #f1f1f1;
    width: 300px;
    margin: 0 auto;
}

input[type=text], input[type=password], input[type=email] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

.register_button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

.btn_register:hover {
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
input[type=file]::file-selector-button {
    margin-right: 20px;
    border: none;
    background: #F23A0C;
    padding: 10px 20px;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
    transition: background .2s ease-in-out;
}

input[type=file]::file-selector-button:hover {
    background: #0d45a5;
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
.error-messages {
    color: red;
    margin: 20px 0;
}
.error-messages p {
    padding: 5px;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
}

}
    </style>
</head>
<body>

<div class="container">
    <center><h1>Register</h1></center>
    <form method="post" enctype="multipart/form-data">
        <div class="container">
            <input type="file" name="image" required>
            <br><br>
            <label for="username">Username</label>
            <input type="text" placeholder="Enter Username" name="username" required>
            <br><br>
            <label for="password">Password</label>
            <input type="password" placeholder="Enter Password" name="password" required>
            <br><br>
            <label for="psw-confirm">Confirm Password</label>
            <input type="password" placeholder="Confirm Password" name="psw-confirm" required>
            <br><br>
            <label for="email">Email Address</label>
            <input type="email" placeholder="Enter Email Address" name="email" required>
            <br><br>
            <label for="contact">Contact Number</label>
            <input type="text" placeholder="Enter Contact" name="phone" required>
            <br><br>
            <input type="submit" name="submit" value="Register" class="register_button">
        </div>
        <div class="container" style="background-color:#f1f1f1">
            <a href="./login.php" class="cancelbtn">Login</a>
        </div>
    </form>
</div>

</body>
</html>
