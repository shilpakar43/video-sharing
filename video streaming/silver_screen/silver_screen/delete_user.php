<?php 

include "./include/conn-db.php";
include "./include/session.php";

// Start session to ensure we have access to session variables
session_start();

// Ensure the session contains the username
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$sess_Username = $_SESSION["username"];

// Secure query to fetch user details using prepared statement to prevent SQL injection
$me_query1 = $mysql->prepare("SELECT * FROM users WHERE username = ?");
$me_query1->bind_param("s", $sess_Username);
$me_query1->execute();
$selectQuery = $me_query1->get_result();

if ($row = $selectQuery->fetch_assoc()) {
    $me_username = $row["username"];
    $me_profile_image = $row["avatar"];
    $admin_flag = $row["flag_admin"];
    $me_uid = $row["user_id"];
    $flag_premium = $row["flag_premium"];
} else {
    // If user does not exist, redirect to login
    header("Location: login.php");
    exit();
}

// Prepare and execute the deletion queries with error checking
try {
    // Delete ratings associated with the user
    $deleteRatingQuery = $mysql->prepare("DELETE FROM rating WHERE review_user_id = ?");
    $deleteRatingQuery->bind_param("s", $me_uid);
    if (!$deleteRatingQuery->execute()) {
        throw new Exception("Failed to delete ratings.");
    }

    // Delete comments associated with the user
    $deleteCommentQuery = $mysql->prepare("DELETE FROM comment WHERE comment_user_id = ?");
    $deleteCommentQuery->bind_param("s", $me_uid);
    if (!$deleteCommentQuery->execute()) {
        throw new Exception("Failed to delete comments.");
    }

    // Delete user record
    $deleteUserQuery = $mysql->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteUserQuery->bind_param("s", $me_uid);
    if (!$deleteUserQuery->execute()) {
        throw new Exception("Failed to delete user.");
    }

    // Successfully deleted, redirect to logout
    header("Location: logout.php");
    exit();

} catch (Exception $e) {
    // If any query fails, show an error message
    echo "Error: " . $e->getMessage();
} finally {
    // Close the prepared statements and the MySQL connection
    if (isset($deleteRatingQuery)) $deleteRatingQuery->close();
    if (isset($deleteCommentQuery)) $deleteCommentQuery->close();
    if (isset($deleteUserQuery)) $deleteUserQuery->close();
    $mysql->close();
}

?>
