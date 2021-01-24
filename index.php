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

    <script src="res/js/lightbox-plus-jquery.js"></script>
    <!--<script src="res/js/bootstrap.bundle.min.js" ></script>-->
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <title>Usermanagement</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand" href="index.php?section=dash"><img src="res/images/dashboard.svg" alt="Dashboard icon" class="navbar-icon">
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
                       <img src="res/images/upload.svg" alt="Upload icon" class="navbar-icon"> Upload Post</a></li>';
            }
            if(isset($_SESSION["username"]) && $user->isAdmin()) {
                echo '<li class="nav-item">
                    <a class="nav-link" href="index.php?section=view">
                    <img src="res/images/administrator.svg" alt="Administration icon" class="navbar-icon">User Administration</a>
                </li>';
            }

            ?>
            <li><a class="nav-link" href="index.php?section=about">
             <img src="res/images/about.svg" alt="About icon" class="navbar-icon"> About</a></li>
        </ul>

        <?php
        echo '<div class="nav-center">
            <form class="form-inline my-2 my-lg-0" method="GET" action="">
                <input class="form-control navbar-center" type="search" name="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>';
        if(isset($_GET["tag"])){
            foreach($_GET["tag"] as $tag){
                echo '<input type="hidden" name=tag[] value="'.$tag.'">';
            }
        }
        if(isset($_GET["timespan"])){
            echo '<input type="hidden" name=timespan value="'.$_GET["timespan"].'">';
        }
        if(isset($_GET["userid"])){
            echo '<input type="hidden" name=userid value="'.$_GET["userid"].'">';
        }
            echo '</form>
        </div>'
        ?>

        <ul class="navbar-nav navbar-right">
            <?php
            if (isset($_SESSION["username"]) && (isset($_GET["section"]) && ($_GET["section"] == "userPage"))) {
                echo '<li><a class="nav-link" href="index.php?section=register&edit=true">
                        <img src="res/images/edit.svg" alt="Edit icon" class="navbar-icon">Edit profile</a></li>';
            }else if (isset($_SESSION["username"])) {
                echo '<li><a class="nav-link" href="index.php?section=userPage"><img src="' . $user->getPicture() . '" 
                        alt="User icon" class="navbar-icon" id="profilePic"> ' .
                    (($user->isAdmin()) ? '<b>[admin]</b>' : '') .
                    $_SESSION["username"] . '\'s Profile</a></li>';
            }
            if(isset($_SESSION["username"])) {
                echo '<li class="nav-item">
                            <a class="nav-link" href="inc/backend.php?type=logout"><img src="res/images/logout.svg" 
                            alt="Logout" class="navbar-icon"> Logout</a>
                      </li>';
            }else{
                echo '<li class="nav-item">
                            <a class="nav-link" href="index.php?section=login"><img src="res/images/login.svg" 
                            alt="Login" class="navbar-icon"> Login</a>
                          </li>';
            }

            ?>
        </ul>
    </div>
</nav>
<header>
<?php
    //Message Banner - Fails
    if (isset($_GET["fail"]) && ($_GET["fail"] == "updatePasswordFailed")) {
        echo MsgFactory::getWarning("<b>Password-Update failed!</b>");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "updatePasswordFailedWrong")) {
        echo MsgFactory::getWarning("<b>Password-Update failed!</b> Wrong password given!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "userNotFound")) {
        echo MsgFactory::getWarning("<b>Password-Update failed!</b> User not found!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "loginUserNotFound")) {
        echo MsgFactory::getWarning("<b>Login failed!</b> User account does not exist!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "accountDeactivated")) {
        echo MsgFactory::getWarning("<b>Login failed!</b> User account deactivated!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "wrongPassword")) {
        echo MsgFactory::getWarning("<b>Login failed!</b> Password incorrect!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "UploadFail")) {
        echo MsgFactory::getWarning("<b>Update failed!</b> Image upload failed!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "UpdateFail")) {
        echo MsgFactory::getWarning("<b>Update failed!</b>");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "registerFail")) {
        echo MsgFactory::getWarning("<b>Registration failed!</b> Username not valid or exists!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "RatingUserNotLoggedIn")) {
        echo MsgFactory::getWarning("<b>Failed to give rating!</b> User not logged in!");
    }elseif (isset($_GET["fail"]) && ($_GET["fail"] == "CommentUserNotLoggedIn")) {
        echo MsgFactory::getWarning("<b>Comment could not be posted!</b> User not logged in!");
    }

    //Message Banner - Successes
    if(isset($_GET["success"]) && ($_GET["success"] == "update")) {
        echo MsgFactory::getSuccess("Update on user data performed successfully!");
    }elseif(isset($_GET["success"]) && ($_GET["success"] == "login")) {
        echo MsgFactory::getSuccess("Login performed successfully!");
    }elseif(isset($_GET["success"]) && ($_GET["success"] == "logout")) {
        echo MsgFactory::getSuccess("Logout performed successfully!");
    }elseif(isset($_GET["success"]) && ($_GET["success"] == "updatePassword")) {
        echo MsgFactory::getSuccess("Update on user password performed successfully!");
    }elseif(isset($_GET["success"]) && ($_GET["success"] == "uploadIcon")) {
        echo MsgFactory::getSuccess("Upload of user picture performed successfully!");
    }elseif(isset($_GET["success"]) && ($_GET["success"] == "registerSuccess")) {
        echo MsgFactory::getSuccess("Registration performed successfully!");
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
            case 'userPage':
                include "inc/userPage.php";
                break;
            case 'about':
                include "inc/about.php";
                break;
            default:
                include "inc/dashboard.php";
                break;
        }
    }else {
        include "inc/dashboard.php";
    }
?>
<script src="res/js/lightbox-plus-jquery.js"></script>
<script src="res/js/bootstrap.bundle.min.js" ></script>
</main>
<script>
    $(document).ready(function () {
        var url = window.location;
        $('ul.nav a[href="'+ url +'"]').parent().addClass('active');
        $('ul.nav a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');
    });
</script>
</body>
</html>