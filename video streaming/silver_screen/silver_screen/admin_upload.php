<?php

include "./include/conn-db.php";
include "./include/session.php";

session_start();

// Ensure the session has a username
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$sess_Username = $_SESSION["username"];

// Prepared statement to prevent SQL injection
$me_query1 = $mysql->prepare("SELECT * FROM users WHERE username = ?");
$me_query1->bind_param("s", $sess_Username);
$me_query1->execute();
$selectQuery = $me_query1->get_result();

// Fetch user details
if ($row = $selectQuery->fetch_assoc()) {
    $admin_flag = $row["flag_admin"];
    if ($admin_flag != "1") {
        header("location: index.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

// File upload and validation
$errors = [];  // Store error messages
$successMessage = ""; // Store success message

if (isset($_POST['upload'])) {
    $maxFileSize = 5 * 1073741824; // 5 GB in bytes

    // Thumbnail upload validation
    if (isset($_FILES['thumnail']) && $_FILES['thumnail']['error'] == 0) {
        if ($_FILES['thumnail']['size'] <= $maxFileSize) {
            $file_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['thumnail']['type'], $file_types)) {
                $thumbnail = uniqid() . '_' . basename($_FILES['thumnail']['name']);
                if (!move_uploaded_file($_FILES['thumnail']['tmp_name'], 'thumnail/' . $thumbnail)) {
                    $errors['thumbnail'] = 'Thumbnail upload failed.';
                }
            } else {
                $errors['thumbnail'] = 'Thumbnail type mismatch.';
            }
        } else {
            $errors['thumbnail'] = 'Thumbnail size exceeds limit.';
        }
    } else {
        $errors['thumbnail'] = 'Please upload a thumbnail.';
    }

    // Movie file upload validation
    if (isset($_FILES['movie']) && $_FILES['movie']['error'] == 0) {
        if ($_FILES['movie']['size'] <= $maxFileSize) {
            $video_types = ['video/mp4', 'video/mkv'];
            if (in_array($_FILES['movie']['type'], $video_types)) {
                $movie = uniqid() . '_' . basename($_FILES['movie']['name']);
                if (!move_uploaded_file($_FILES['movie']['tmp_name'], 'movie/' . $movie)) {
                    $errors['movie'] = 'Movie upload failed.';
                }
            } else {
                $errors['movie'] = 'Movie type mismatch.';
            }
        } else {
            $errors['movie'] = 'Movie size exceeds limit.';
        }
    } else {
        $errors['movie'] = 'Please upload a movie file.';
    }

    // Insert into database if no errors
    if (empty($errors)) {
        $registerQuery = $mysql->prepare(
            "INSERT INTO movies (movie_name, movie_upload_name, movie_upload_image, movie_description, movie_genre, movie_subscription, release_date, movie_language) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $registerQuery->bind_param(
            "ssssssss",
            $_POST["name"],
            $movie,
            $thumbnail,
            $_POST["description"],
            $_POST["genre"],
            $_POST["subscription"],
            $_POST["date"],
            $_POST["language"]
        );

        if ($registerQuery->execute()) {
            $successMessage = 'Movie uploaded successfully!';
        } else {
            $errors['database'] = 'Failed to upload movie to the database.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <style>
        /* Styling for errors and success messages */
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 18px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        form {
            background-color: #2e3b46;
            padding: 20px;
            border-radius: 10px;
            max-width: 800px;
            margin: 20px auto;
            color: white;
        }

        label {
            font-size: 16px;
        }

        input[type="text"], input[type="file"], select, input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: #333;
            color: white;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        input[type="submit"] {
            background-color: #5d78ff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
        }

        input[type="submit"]:hover {
            background-color: #4a67cc;
        }
    </style>
</head>
<body>
    <?php include "./include/header.php"; ?>

    <!-- Display errors or success message -->
    <div>
        <?php
        if (!empty($successMessage)) {
            echo "<div class='message success'>$successMessage</div>";
        }
        foreach ($errors as $error) {
            echo "<div class='message error'>$error</div>";
        }
        ?>
    </div>

    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="movie">Movie</label>
            <input type="file" name="movie" required>
        </div>

        <div>
            <label for="thumnail">Thumbnail</label>
            <input type="file" name="thumnail" required>
        </div>

        <div>
            <label for="name">Name</label>
            <input type="text" name="name" required>
        </div>

        <div>
            <label for="subscription">Subscription</label>
            <input type="radio" name="subscription" value="free" required> Free
            <input type="radio" name="subscription" value="premium" required> Premium
        </div>

        <div>
            <label for="description">Description</label>
            <input type="text" name="description" required>
        </div>

        <div>
            <label for="genre">Genre</label>
            <select name="genre" id="genre" required>
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

        <div>
            <label for="date">Date</label>
            <input type="date" name="date" required>
        </div>

        <div>
            <label for="language">Language</label>
            <input type="text" name="language" required>
        </div>

        <div>
            <input type="submit" name="upload" value="Upload">
        </div>
    </form>
</body>
</html>
