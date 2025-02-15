<?php
include "./include/conn-db.php";
include "./include/session.php";

session_start();

// Ensure user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$sess_Username = $_SESSION["username"];

// Prepare and execute the query safely
$me_query1 = "SELECT * FROM users WHERE username = ?";
$selectQuery = $mysql->prepare($me_query1);
$selectQuery->bind_param("s", $sess_Username);
$selectQuery->execute();
$result = $selectQuery->get_result();

// Fetch user data
if ($row = $result->fetch_assoc()) {
    $me_username = $row["username"];
    $me_profile_image = $row["avatar"];
    $admin_flag = $row["flag_admin"];
    $me_uid = $row["user_id"];
    $flag_admin = $row["flag_admin"];
    $flag_premium = $row["flag_premium"];
    $expire_premium = $row["expire_premium"];
} else {
    // Handle error if user is not found or query fails
    echo "User not found or an error occurred.";
    exit();
}

$_SESSION['id'] = $me_uid;
$sess_uid = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="main.css">
    <style>
        * {
            color: white;
        }
        a {
            text-decoration: none;
        }
        .welcome_username {
            font-size: 40px;
        }
        .btn_edit {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            margin-right: 20px;
        }
        .btn_logout {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
        }
        .profile-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .profile-img {
            width: 30%;
            text-align: center;
        }
        .profile-details {
            width: 60%;
        }
        .profile-details p {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                align-items: center;
            }
            .profile-img, .profile-details {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    
<?php include "./include/header.php"; ?>
<br><br>

<div class="profile-container">
    <div class="profile-img">
        <img src="./avatar/<?php echo htmlspecialchars($me_profile_image); ?>" width="200px" alt="Profile Picture">
        <br>
        <span class="welcome_username">Welcome <?php echo htmlspecialchars($me_username); ?></span>
    </div>

    <div class="profile-details">
        <p>Username: <?php echo htmlspecialchars($me_username); ?></p>
        <p>Email: <?php echo htmlspecialchars($row["email"]); ?></p>
        <p>Contact: <?php echo htmlspecialchars($row["phone_no"]); ?></p>
        <p>Subscription: <?php echo $flag_premium != "free" ? "Premium" : "Free"; ?></p>
        
        <?php if ($flag_premium != "free"): ?>
            <p>Expire Date: <?php echo date("F j, Y", strtotime($expire_premium)); ?></p>
        <?php endif; ?>

        <br>
        <a href="./edit.php" class="btn_edit">Profile Setting</a>
        <a href="./logout.php" class="btn_logout">Logout</a>
    </div>
</div>

</body>
</html>
