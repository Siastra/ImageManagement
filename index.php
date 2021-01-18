<?php
    session_start();
    include_once "utility/DB.php";
    include_once "model/User.php";
    include_once "utility/MsgFactory.php";
    include_once "utility/Email.php";
    include_once "utility/Upload.php";
    $db = new DB();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="Author: Marcel Glavanits,
    Sebastian Schramm, Lukas Koller | A basic social network">
    <link rel="stylesheet" href="res/css/myCss.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="res/css/bootstrap.min.css">
    <link href="res/css/lightbox.css" rel="stylesheet">

    <title>Usermanagement</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php?section=dash"><img src="res/images/dashboard.svg" alt="Dashboard icon" width="25px">
        Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <?php
            if(isset($_SESSION["username"])) {
                $user = $db->getUser($_SESSION["username"]);
                echo '<li><a class="nav-link" href="index.php?section=create">
                       <img src="res/images/upload.svg" alt="Upload icon" width="25px">Upload Post</a></li>';
            }
            if(isset($_SESSION["username"]) && $user->isAdmin()) {
                echo '<li class="nav-item active">
                    <a class="nav-link" href="index.php?section=view">
                    <img src="res/images/administrator.svg" alt="Administration icon" width="25px">User Administration</a>
                </li>';
            }

            ?>
        </ul>
        <ul class="navbar-nav navbar-right">
            <?php
            if (isset($_SESSION["username"]) && (isset($_GET["section"]) && ($_GET["section"] == "userPage"))) {
                echo '<li><a class="nav-link" href="index.php?section=register&edit=true">
                        <img src="res/images/edit.svg" alt="Edit icon" width="25px">Edit profile</a></li>';
            }else if (isset($_SESSION["username"])) {
                echo '<li><a class="nav-link" href="index.php?section=userPage"><img src="' . $user->getPicture() . '" 
                        alt="User icon" width="25px" height="25px" id="profilePic"> ' .
                    (($user->isAdmin()) ? '<b>[admin]</b>' : '') .
                    $_SESSION["username"] . '\'s Profile</a></li>';
            }
            if(isset($_SESSION["username"])) {
                echo '<li class="nav-item">
                            <a class="nav-link" href="inc/backend.php?type=logout"><img src="res/images/logout.svg" 
                            alt="Logout" width="25px"> Logout</a>
                      </li>';
            }else{
                echo '<li class="nav-item">
                            <a class="nav-link" href="index.php?section=login"><img src="res/images/login.svg" 
                            alt="Login" width="25px"> Login</a>
                          </li>';
            }

            ?>
        </ul>
    </div>
</nav>
<header>
<?php
    //Message Banner
    if(isset($_GET["action"]) && ($_GET["action"] == "success")) {
        echo MsgFactory::getSuccess("Action performed successfully!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "fail1") && (isset($_GET["section"]) && ($_GET["section"] == "login"))) {
        echo MsgFactory::getWarning("Login failed! Username or password incorrect!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "fail2") && (isset($_GET["section"]) && ($_GET["section"] == "login"))) {
        echo MsgFactory::getWarning("Login failed! User account deactivated!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "fail") && (isset($_GET["section"]) && ($_GET["section"] == "register"))) {
        echo MsgFactory::getWarning("Registration failed! Username not valid or exists!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "fail") && ($_GET["type"] == "edit")) {
        echo MsgFactory::getWarning("Update failed! Password not valid!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "UploadFail") && ($_GET["type"] == "edit")) {
        echo MsgFactory::getWarning("Update failed! Image upload failed!");
    }
    ?>
</header>
<main>
<?php
    //Section- Management
    if (isset($_GET["section"])) {
        switch ($_GET["section"]) {
            case "register":
                include "inc/registerForm.php";
                break;
            case 'view':
                include "inc/userAdministration.php";
                break;
            case 'login':
                include "inc/login.php";
                break;
            case 'forgotPw':
                include "inc/forgotPassword.php";
                break;
            case 'create':
                include "inc/createPost.php";
                break;
            case 'dash':
                include "inc/dashboard.php";
                break;
            case 'userPage':
                include "inc/userPage.php";
                break;
            default :
                include "inc/dashboard.php";
                break;
        }
    }
?>
</main>
<script src="res/js/lightbox-plus-jquery.js"></script>
<<<<<<< HEAD
<script src="res/js/bootstrap.bundle.min.js" ></script>

=======
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
>>>>>>> 4945386075ce4a3df854c93515c7d3b06c2ce3f7
</body>
</html>