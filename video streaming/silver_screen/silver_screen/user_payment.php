<?php
include "./include/conn-db.php";
include "./include/session.php";

session_start();

$sess_Username = $_SESSION["username"];

// Sanitize input for security
$sess_Username = mysqli_real_escape_string($mysql, $sess_Username);

// Fetch user details
$me_query1 = "SELECT * FROM users WHERE username = '$sess_Username'";

$selectQuery = $mysql->query($me_query1);
while($row = $selectQuery->fetch_assoc()){
    $me_username = $row["username"];
    $me_profile_image = $row["avatar"];
    $admin_flag = $row["flag_admin"];
    $me_uid = $row["user_id"];
    $flag_premium = $row["flag_premium"];
}

// Check if the user is an admin
if($admin_flag != "1"){
    header("location: index.php");
    exit();
}

date_default_timezone_set('Asia/Kathmandu');

// Check if we have the user ID to activate
$get_activation_user_id = isset($_GET["activate_user_id"]) ? $_GET["activate_user_id"] : null;

if($get_activation_user_id != null){
    // Set to 'premium' when activating
    $form_flag_premium = "premium";

    // Get current date and calculate expiry date for premium (1 year ahead)
    $current_date = date("Y-m-d");
    $premium_date = strtotime("+1 year", strtotime($current_date));

    // Update the user's premium status in the database
    $query_premium_activate = $mysql->prepare("UPDATE users SET flag_premium = ?, expire_premium = ? WHERE user_id = ?");
    $query_premium_activate->bind_param("sss", $form_flag_premium, date("Y-m-d", $premium_date), $get_activation_user_id);
    $query_premium_activate->execute();

    // Redirect to the user payment page (optional)
    // header("location: user_payment.php");
}

$_SESSION['id'] = $me_uid;
$sess_uid = $_SESSION['id'];

// Query for users with pending premium activation
$paymentQuery = $mysql->query("SELECT * FROM users WHERE flag_premium = 'free' AND premium_transaction != ''");


/*if ($query_premium_activate->execute()) {
    // Success logic
} else {
    // Error logic
}*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Payment</title>
    <style>
        *{
            background-color:#000;
            color:white;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size:1rem;
        }

        table{
            width:75%;
            position: relative;
            margin: auto;
            margin-top:7%;
        }
        tr,td{
            border-bottom:1px solid;
            border-color: rgba(255,255,255,.2);
            text-align: center;
        }

        th{ 
            border:1px solid;
            border-color: rgba(255,255,255,.2);
            width: calc(100%/4);

        }
        
        tr td{
            padding:5px 15px 5px 15px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'?>
    
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Transaction Key</th>
            <th>Approve</th>
        </tr>
    <?php
    while($row = $paymentQuery->fetch_assoc()){
        $payment_user_id = $row["user_id"];
        $payment_username = $row["username"];
        $payment_transaction_key = $row["premium_transaction"];
        
        echo "<tr>";
        echo "<td>$payment_user_id</td>";
        echo "<td>$payment_username</td>";
        echo "<td>$payment_transaction_key</td>";
        echo "<td><a href='./user_payment.php?activate_user_id=$payment_user_id'>Activate</a></td>";
        echo "</tr>";
    }
    ?>
    </table>
</body>
</html>
