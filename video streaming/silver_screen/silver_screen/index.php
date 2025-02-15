<?php
error_reporting(E_ERROR | E_PARSE); // Suppress warnings but log errors.
include "./include/conn-db.php";
include "./include/session.php";

$sess_Username = $_SESSION["username"];
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}


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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="main.css">
    <style>
        * {
            background-color:#000000;
            color: white;
            margin: 0;
            padding: 0;
        }

        /* Flexbox container for layout */
        .container {
            display: flex;               /* Use Flexbox to position children side by side */
            justify-content: space-between; /* Optional: space between the two sections */
            width: 100%;                  /* Make sure the container spans the full width of the screen */
        }

        /* Main movie section */
        .main_movie {
            flex: 1;                      /* Main movie section takes the remaining space */
            padding-right: 20px;          /* Optional: add some padding on the right */
        }

        /* Recommendation section */
        .main_recomm_high_rating {

            width: 25%;                   /* Takes up 20% of the container's width */
            padding-left: 20px;           /* Optional: add some padding on the left */
            background-color: 00000;    /* Optional: background color for visibility */
        }
        

        /* Movie wrapper (for individual movie elements) */
        .movie_wrapper {
            display: inline-block;
            background-color: #000000;
            padding: 10px;
            margin: 20px;
            border-radius: 8px;
            color: white;
        }
            .movie_wrapper img {
            width: 150px;  /* Smaller image size (adjusted) */
            height: 150px; /* Fixed height to maintain aspect ratio */
            margin-bottom: 10px;
            border-radius: 8px;
        }

        /* Make images responsive within the movie wrapper */
        .movie_wrapper img {
            width: 200px;
            height: 200px;
        }

        /* Text styling for the "Recommendation" section */
        .main_recomm_high_rating .text {
            font-size: 2.5rem;                /* 40px / 16px = 2.5rem */
            margin-bottom: 1.25rem;           /* 20px / 16px = 1.25rem */
        }

        /* Recommendation movie images styling */
            .main_recomm_high_rating img {
            width: 12.5rem;                  /* 200px / 16px = 12.5rem */
            height: 9.375rem;                /* 150px / 16px = 9.375rem */
            margin: 1.25rem;                 /* 20px / 16px = 1.25rem */
            border-radius: 0.5rem;           /* 8px / 16px = 0.5rem */
        }


        /* Button styling */
        .btn_upload {
            background-color: green;
            color: white;
            padding: 15px 30px;
            width: 120px;
            border-radius: 8px;
            text-decoration: none;
            margin-left: 20px;
        }
        .movie_wrapper h3 {
            font-size: 20px;            /* Set font size for movie title */
            font-weight: bold;          /* Make the movie title bold */
            color: #fff;                /* White text color */
            margin: 0;                  /* Remove any default margin */
            line-height: 1.2;           /* Line height for better readability */
            white-space: nowrap;        /* Prevent the text from wrapping */
            text-overflow: ellipsis;    /* Adds "..." when text overflows */
            overflow: hidden;           /* Hide overflow text */
        }

        /* Movie Date Styling */
        .movie_wrapper .date {
            font-size: 14px;            /* Smaller font size for the date */
            color: #bbb;                /* Lighter color for the date */
            margin-left: 10px;          /* Space between title and date */
            font-style: italic;         /* Italicize the date */
        }
        /* Media query for smaller screens (responsive design) */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;  /* Stack the sections vertically on small screens */
            }

            .main_recomm_high_rating {
                width: 100%;  /* Make the recommendation section full width on smaller screens */
                margin-top: 20px;  /* Add some margin on top */
            }

            .main_movie {
                flex: none;  /* Allow the movie section to take up full width on smaller screens */
            }
        }
    </style>
</head>
<body>

<?php include "./include/header.php" ?>
<br><br>

<?php
if ($admin_flag == "1") {
    echo "<a href='./admin_upload.php' class='btn_upload'>Upload</a>";
    echo "<a href='./user_payment.php' class='btn_upload'>User Payment</a>";
    echo "<br><br>";
}
?>

<div class="container">

    <!-- Main Movie -->
    <div class="main_movie">
        <?php
        $movie_query = $mysql->query("SELECT * FROM movies $subscription_query ORDER BY movie_id DESC LIMIT 31;");
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
        }
        ?>
    </div>

    <!-- High Rating Recommendation -->
    <div class="main_recomm_high_rating">
        <span class="text">Recommendation</span><br>

        <?php
        $recom_query = $mysql->query("SELECT review_movie_id, SUM(review_score) AS total_score FROM rating GROUP BY review_movie_id ORDER BY total_score DESC LIMIT 5");
        while ($row = $recom_query->fetch_assoc()) {
            $rating_movie_id = $row["review_movie_id"];
            
            // Fetching the highest rated movies
            $movie_query = $mysql->query("SELECT * FROM movies WHERE movie_id = $rating_movie_id");
            while ($movie = $movie_query->fetch_assoc()) {
                $movie_id = $movie["movie_id"];
                $movie_name = $movie["movie_name"];
                $movie_upload_image = $movie["movie_upload_image"];
                $release_date = $movie["release_date"];
        ?>
                <div class="movie_wrapper">
                    <a href="./view_movie.php?attribute_movie_id=<?php echo $movie_id ?>">
                        <img src="./thumnail/<?php echo $movie_upload_image; ?>" alt="Movie Thumbnail">
                    </a>
                    <?php echo $movie_name . "<br> " . $release_date; ?>
                </div>
        <?php
            }
        }
        ?>
    </div>

</div>

</body>
</html>
