<?php
error_reporting(E_ERROR | E_PARSE);
include "./include/conn-db.php";
include "./include/session.php";

// session_start();

$sess_Username = $_SESSION["username"];

// Sanitize user input to prevent SQL injection
$sess_Username = mysqli_real_escape_string($mysql, $sess_Username);

// Fetch user data
$me_query1 = "SELECT * FROM users WHERE username = '$sess_Username'";
$selectQuery = $mysql->query($me_query1);

while ($row = $selectQuery->fetch_assoc()) {
    $me_username = $row["username"];
    $me_profile_image = $row["avatar"];
    $admin_flag = $row["flag_admin"];
    $me_uid = $row["user_id"];
    $user_subscription = $row["flag_premium"];
}

$_SESSION['id'] = $me_uid;
$sess_uid = $_SESSION['id'];

// Set subscription query based on user type
$subscription_query = ($user_subscription == "free") ? "WHERE movie_subscription = 'free'" : "WHERE (movie_subscription = 'free' OR movie_subscription = 'premium')";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        * {
            background-color: #01060A;
            color: white;
        }

        .container {
            display: flex;
        }

        .btn_upload {
            background-color: green;
            color: white;
            padding: 15px 30px;
            width: 120px;
            border-radius: 8px;
            text-decoration: none;
            margin-left: 20px;
        }

        .movie_wrapper {
            display: inline-block;
            background-color: #262b31;
            padding: 10px;
            margin: 20px;
            border-radius: 8px;
            color: white;
        }

        .movie_wrapper img {
            width: 200px;
            height: 200px;
        }

        .main_recomm_high_rating .text {
            font-size: 40px;
        }

        .main_recomm_high_rating img {
            width: 200px;
            height: 200px;
            margin: 20px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <?php include "./include/header.php"; ?>
    <br><br>

    <?php
    if ($flag_admin == "1") {
        echo "<a href='./admin_index.php' class='btn_upload'>Upload</a>";
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
                $movie_upload_image = $row["movie_upload_image"];
                $release_date = $row["release_date"];
                ?>

                <div class="movie_wrapper">
                    <a href="./view_movie.php?attribute_movie_id=<?php echo $movie_id ?>">
                        <img src="./thumnail/<?php echo $movie_upload_image; ?>" alt="<?php echo $movie_name; ?>">
                    </a>
                    <br>
                    <?php echo $movie_name . "<br>" . $release_date; ?>
                </div>

                <?php
            }
            ?>
        </div>

        <!-- High Rating Recommendations -->
        <div class="main_recomm_high_rating">
            <span class="text">High Rating</span><br>
            <table class="recommendation_table">
                <?php
                // Get top-rated movies based on average rating
                $recom_query = $mysql->query("SELECT m.movie_id, m.movie_name, m.movie_upload_name, m.movie_upload_image, m.release_date, AVG(r.review_score) AS average_rating
                                              FROM movies m
                                              LEFT JOIN rating r ON m.movie_id = r.review_movie_id
                                              GROUP BY m.movie_id
                                              ORDER BY average_rating DESC
                                              LIMIT 5;");

                while ($row = $recom_query->fetch_assoc()) {
                    $movie_id = $row["movie_id"];
                    $movie_name = $row["movie_name"];
                    $movie_upload_image = $row["movie_upload_image"];
                    $release_date = $row["release_date"];
                    ?>

                    <tr>
                        <td><a href='./view_movie.php?attribute_movie_id=<?php echo $movie_id; ?>'>
                                <img src='./thumnail/<?php echo $movie_upload_image; ?>' alt="<?php echo $movie_name; ?>">
                            </a></td>
                        <td><?php echo $movie_name . "<br>" . $release_date; ?></td>
                    </tr>

                    <?php
                }
                ?>
            </table>
        </div>
    </div>

</body>

</html>
