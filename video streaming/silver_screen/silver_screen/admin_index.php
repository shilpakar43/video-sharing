<?php
include "./include/conn-db.php";
include "./include/session.php";

session_start();

$sess_Username = $_SESSION["username"] ?? null;
if (!$sess_Username) {
    header("location: index.php");
    exit();
}

// Secure query to fetch admin details using prepared statements
$me_query1 = $mysql->prepare("SELECT * FROM users WHERE username = ?");
$me_query1->bind_param("s", $sess_Username);
$me_query1->execute();
$selectQuery = $me_query1->get_result();
$userData = $selectQuery->fetch_assoc();

// Ensure that only admin can access this page
if ($userData['flag_admin'] != "1") {
    header("location: index.php");
    exit();
}

$errors = [];

if (isset($_POST['upload'])) {
    $maxFileSize = 5 * 1073741824; // 5 GB in bytes

    // Thumbnail Upload
    if ($_FILES['thumnail']['error'] == 0) {
        if ($_FILES['thumnail']['size'] <= $maxFileSize) {
            $file_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['thumnail']['type'], $file_types)) {
                $thumbnail = uniqid() . '_' . basename($_FILES['thumnail']['name']);
                if (!move_uploaded_file($_FILES['thumnail']['tmp_name'], 'thumnail/' . $thumbnail)) {
                    $errors['thumnail'] = 'Thumbnail upload failed';
                }
            } else {
                $errors['thumnail'] = 'Invalid thumbnail file type';
            }
        } else {
            $errors['thumnail'] = 'Thumbnail size exceeds the limit';
        }
    } else {
        $errors['thumnail'] = 'Please upload a thumbnail';
    }

    // Movie Upload
    if ($_FILES['movie']['error'] == 0) {
        if ($_FILES['movie']['size'] <= $maxFileSize) {
            $video_types = ['video/mp4', 'video/mkv'];
            if (in_array($_FILES['movie']['type'], $video_types)) {
                $movieName = uniqid() . '_' . basename($_FILES['movie']['name']);
                if (move_uploaded_file($_FILES['movie']['tmp_name'], 'movie/' . $movieName)) {
                    // Insert data into the database using prepared statement to prevent SQL injection
                    $movieNameSafe = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
                    $descriptionSafe = htmlspecialchars($_POST["description"], ENT_QUOTES, 'UTF-8');
                    $genreSafe = htmlspecialchars($_POST["genre"], ENT_QUOTES, 'UTF-8');
                    $languageSafe = htmlspecialchars($_POST["language"], ENT_QUOTES, 'UTF-8');
                    $releaseDateSafe = $_POST["date"];
                    $subscriptionSafe = $_POST["subscription"];

                    // Insert movie details into the database using prepared statements
                    $registerQuery = $mysql->prepare("INSERT INTO movies (movie_name, movie_upload_name, movie_upload_image, movie_description, movie_genre, movie_subscription, release_date, movie_language) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $registerQuery->bind_param("ssssssss", $movieNameSafe, $movieName, $thumbnail, $descriptionSafe, $genreSafe, $subscriptionSafe, $releaseDateSafe, $languageSafe);
                    $registerQuery->execute();
                    echo "Movie uploaded successfully!";
                } else {
                    $errors['movie'] = 'Movie upload failed';
                }
            } else {
                $errors['movie'] = 'Invalid movie file type';
            }
        } else {
            $errors['movie'] = 'Movie size exceeds the limit';
        }
    } else {
        $errors['movie'] = 'Please upload a movie file';
    }

    // Display errors if any
    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin_index.css">
</head>
<body>
    <?php include "./include/header.php"; ?>
    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="movie">Movie</label>
                <input type="file" name="movie" required>
            </div>

            <div class="form-group">
                <label for="thumnail">Thumbnail</label>
                <input type="file" name="thumnail" required>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="subscription">Subscription</label>
                <label><input type="radio" name="subscription" value="free" required>Free</label>
                <label><input type="radio" name="subscription" value="premium" required>Premium</label>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" required>
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <select name="genre" required>
                    <option value="Action">Action</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Drama">Drama</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Horror">Horror</option>
                    <option value="Romance">Romance</option>
                    <option value="Sci-Fi">Sci-Fi</option>
                    <option value="Thriller">Thriller</option>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="language">Language</label>
                <input type="text" name="language" required>
            </div>

            <div class="form-group">
                <input type="submit" name="upload" value="Upload">
            </div>
        </form>
    </div>
</body>
</html>
