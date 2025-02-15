<?php 

include "./include/conn-db.php";
include "./include/session.php";

// Check if attribute_movie_id is set in GET request
if (!isset($_GET["attribute_movie_id"])) {
    header("Location: index.php");
    exit();
}

$attribute_movie_id = $_GET["attribute_movie_id"];

// Begin transaction
$mysql->begin_transaction();

try {
    // Prepare and execute delete statement for rating
    $deleteRatingQuery = $mysql->prepare("DELETE FROM rating WHERE review_movie_id = ?");
    if (!$deleteRatingQuery) {
        throw new Exception("Failed to prepare delete statement for rating.");
    }
    $deleteRatingQuery->bind_param("s", $attribute_movie_id);
    if (!$deleteRatingQuery->execute()) {
        throw new Exception("Failed to execute delete statement for rating.");
    }

    // Prepare and execute delete statement for comment
    $deleteCommentQuery = $mysql->prepare("DELETE FROM comment WHERE comment_movie_id = ?");
    if (!$deleteCommentQuery) {
        throw new Exception("Failed to prepare delete statement for comment.");
    }
    $deleteCommentQuery->bind_param("s", $attribute_movie_id);
    if (!$deleteCommentQuery->execute()) {
        throw new Exception("Failed to execute delete statement for comment.");
    }

    // Prepare and execute delete statement for movie
    $deleteMovieQuery = $mysql->prepare("DELETE FROM movies WHERE movie_id = ?");
    if (!$deleteMovieQuery) {
        throw new Exception("Failed to prepare delete statement for movie.");
    }
    $deleteMovieQuery->bind_param("s", $attribute_movie_id);
    if (!$deleteMovieQuery->execute()) {
        throw new Exception("Failed to execute delete statement for movie.");
    }

    // Commit transaction
    $mysql->commit();
    header("Location: index.php");

} catch (Exception $e) {
    // Rollback transaction on error
    $mysql->rollback();
    echo "Error deleting movie and related data: " . $e->getMessage();
} finally {
    // Close prepared statements
    if (isset($deleteRatingQuery)) $deleteRatingQuery->close();
    if (isset($deleteCommentQuery)) $deleteCommentQuery->close();
    if (isset($deleteMovieQuery)) $deleteMovieQuery->close();
    // Close MySQL connection
    $mysql->close();
}
?>
