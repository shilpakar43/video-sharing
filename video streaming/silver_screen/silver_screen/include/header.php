<?php
error_reporting(0);
error_reporting(E_ERROR | E_PARSE);
include "./conn-db.php";
include "./session.php";


include "./include/conn-db.php";
include "./include/session.php";

session_start();

$sess_Username = $_SESSION["username"];

$me_query1 = "select * from users where username = '" . $sess_Username . "'";

$selectQuery = $mysql->query($me_query1);
while($row = $selectQuery->fetch_assoc()){
    $me_username = $row["username"];
    $me_profile_image = $row["avatar"];
    $admin_flag = $row["flag_admin"];
    $me_uid = $row["user_id"];
    $flag_premium = $row["flag_premium"];
    // echo $us;
}

$_SESSION['id'] = $me_uid;
$sess_uid = $_SESSION['id'];

// search
/*if(isset($_GET["submit_movie"]) && $_GET["submit_movie"] = "search"){
    $search_movie = $_GET["search_movie"];

    //$movie_select = " and (movie_name like '%". $search_movie ."%') ";
    

}*/
// echo "<script>alert('$search_movie');</script>";


// if(isset($_POST["submit_movie"]) && $_POST["submit_movie"] == "search"){

//     $movie_select = " and (movie_name like %". $_POST["search_movie"] ."%) ";
    
// }
// else{
//     $movie_select = "";
// }

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        *{
            background-color: #01060A;
        }
        a{
            text-decoration: none;
        }
        .img_logo{
            width: 100px;
            border-radius: 8px;
            margin-left: 25px;
        }
        .profile_image{
            width: 100px;
            border-radius: 50%;
            height: 100px;
            border: 5px solid greenyellow;
        }
        input[name=search_movie]{
            padding: 12px 100px;
            border-radius: 8px;
            border: 2px solid white;
            margin-right: 20px;
            font-size: 20px;
            color: white;
        }input[name=submit_movie]{
            padding: 12px 30px;
            border-radius: 8px;
            margin-right: 20px;
            font-size: 20px;
            background-color: #C013E7;
            color: white;
            border: none;
        }
        .upgrade_to{
            color: red;
        }
        .premium{
            background-color: gold;
            color: white;
            padding: 10px;
            width: 120px;
            border-radius: 8px;
        }
        .premium a{

            background-color: gold;
            color: white;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td width="20%">
                <a href="./index.php">
                    <img src="./logo.jpg" alt="" srcset="" class="img_logo">
                </a>
            </td>

            <td width="60%">
                <center>

                <form method="get" action="search_result.php">
                    <input type="text" name="search_movie" placeholder="Search Movie">
                    <input type="submit" name="submit_movie" value="search">
                </form>
                </center>
            </td>

            <td width="20%">
                <center>

                <a href="./profile.php">
                    <img src="./avatar/<?php echo $me_profile_image; ?>" alt="" srcset="" class="profile_image">
                </a> 
                <br>
                <?php 
                    if($flag_admin == "1"){
                        echo "<div class='premium'>ADMIN USER</div>";
                    }
                    else{
                        if($flag_premium == "free"){
                            echo "<div class='upgrade_to'>UPGRADE TO</div>
                            <div class='premium'><a href='./premium.php'>PREMIUM</a></div>";
                        }
                        else{
                            echo "<div class='premium'>PREMIUM USER</div>";
                        }
                    }
                ?>
                
                </center>               
            </td>
        </tr>
    </table>
</body>
</html>