<?php
error_reporting(0);
error_reporting(E_ERROR | E_PARSE);
include "./include/conn-db.php";
include "./include/session.php";

// session_start();

$sess_Username = $_SESSION["username"];

$me_query1 = "select * from users where username = '" . $sess_Username . "'";

$selectQuery = $mysql->query($me_query1);
while($row = $selectQuery->fetch_assoc()){
    $me_username = $row["username"];
    $me_profile_image = $row["avatar"];
    $admin_flag = $row["flag_admin"];
    $me_uid = $row["user_id"];
    $flag_admin = $row["flag_admin"];
    // $user_subscription = $row["flag_premium"];
    // echo $us;
}


$_SESSION['id'] = $me_uid;
$sess_uid = $_SESSION['id'];


if(isset($_POST["pay_submit"]) && $_POST["pay_submit"] == "pay"){
    $form_flag_premium = "premium";
    $query_comment_post = $mysql->prepare("update users set  premium_transaction = ? where user_id = ?;");
        
    $query_comment_post->bind_param("ss",$_POST["transaction_id"],$sess_uid);
    
    $query_comment_post->execute();
    
    echo "<script>alert('Please wait for your transaction confirmation');</script>";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
      
		*{
			background-color: #01060A;
            color: white;
		}
        form{
            margin-left: 20px;
            font-size: 25px;
        }
        input{
            color: white;
            border: solid white 1px;
            padding: 15px 30px;
            font-size: 20px;
        }
        input[name="pay_submit"]{
            background-color: green;
            color: white;
            padding: 15px 30px;
            width: 120px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
        }
    </style>
</head>
<body>

<?php include "./include/header.php" ?>
<br><br>


    <form action="" method="post">
        Transaction ID:
        <input type="text" name="transaction_id" placeholder="Enter Transaction ID">
        <br>
        <input type="submit" name="pay_submit" value="pay">
    </form>

 
</body>
</html>