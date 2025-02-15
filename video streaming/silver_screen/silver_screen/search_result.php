<?php
    // old php logic for header part
    error_reporting(E_ERROR | E_PARSE); // Suppress warnings but log errors.
    include "./include/conn-db.php";
    include "./include/session.php";

    $sess_Username = $_SESSION["username"];

    // Fetch user data securely using prepared statements
    $me_query1 = $mysql->prepare("SELECT * FROM users WHERE username = ?");
    $me_query1->bind_param("s", $sess_Username);
    $me_query1->execute();
    $selectQuery = $me_query1->get_result();

    if ($row = $selectQuery->fetch_assoc()) {
        $me_username = $row["username"];
        $me_profile_image = $row["avatar"];
        $admin_flag = $row["flag_admin"];
        $me_uid = $row["user_id"];
        $user_subscription = $row["flag_premium"];
        $user_expire_date = $row["expire_premium"];
    }

    // Expire Premium Logic
    $current_date = date("Y-m-d");
    if ($current_date > $user_expire_date) {
        $form_flag_premium = "free";
        $expire_premium_transaction = "";
        
        // Update premium status to free if expired
        $query_premium_deactivate = $mysql->prepare("UPDATE users SET flag_premium = ?, premium_transaction = ? WHERE user_id = ?");
        $query_premium_deactivate->bind_param("sss", $form_flag_premium, $expire_premium_transaction, $me_uid);
        $query_premium_deactivate->execute();
    }

    // Subscription Logic
    $subscription_query = "WHERE (movie_subscription = 'free' OR movie_subscription = 'premium')";
    if ($user_subscription == "free") {
        $subscription_query = "WHERE (movie_subscription = 'free')";
    }

    $_SESSION['id'] = $me_uid;
    $sess_uid = $_SESSION['id'];

    // counting query number
    $user_search_keyword = $_GET['search_movie'];
    $count = 0;
    if(strlen($user_search_keyword) != 0 && strlen($user_search_keyword) > 2 )
    {
        $count_query = $mysql->query("SELECT COUNT(*) AS total FROM movies WHERE movie_name LIKE '%$user_search_keyword%'");    
        if ($count_query) {
            $row = $count_query->fetch_assoc();
            $count = $row['total'];  // Use the 'total' column alias for the count value
        }
    }
    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result</title>
    <link rel="stylesheet" href="main.css">

</head>
<body>

    <!-- header part -->
     <?php include 'include/header.php'; ?>

     <div style="padding-left:20px; font-size:1.2rem;"> <?php echo $count?> results found </div>

     <div class="container">
        
        <div class="main_movie">
        <!-- Retreving data from database by user given keyword -->
            <?php
            if(strlen($user_search_keyword) != 0 && strlen($user_search_keyword) > 2 )
            {
                $user_search_keyword = $_GET['search_movie'];
                $movie_query = $mysql->query("SELECT * FROM movies WHERE movie_name LIKE '%$user_search_keyword%' LIMIT 31;");

                while ($row = $movie_query->fetch_assoc()) {
                    $movie_id = $row["movie_id"];
                    $movie_name = $row["movie_name"];
                    $movie_upload_name = $row["movie_upload_name"];
                    $movie_upload_image = $row["movie_upload_image"];
                    $release_date = $row["release_date"];                
            ?>
            
            <div class="movie_wrapper">
                <a href="./view_movie.php?attribute_movie_id=<?php echo $movie_id ?>">
                    <img src="./thumnail/<?php echo $movie_upload_image; ?>" alt="Movie Thumbnail">
                </a>
                <br>
                <?php echo $movie_name . "<br>  " . $release_date; ?>
            </div>

            <?php 
                } }
            ?>
        </div>
    </div>



</body>
</html>