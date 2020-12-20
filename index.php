<?php
    session_start();
    include_once "utility/DB.php";
    include_once "model/User.php";
    include_once "utility/MsgFactory.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="res/css/myCss.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <title>Usermanagement</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <?php
            if(isset($_SESSION["username"]) && $_SESSION["username"] == "admin") {
                echo '<li class="nav-item active">
                    <a class="nav-link" href="index.php?section=view">User Administration</a>
                </li>';
            }

            ?>
        </ul>
        <ul class="navbar-nav navbar-right">
            <?php
            if(isset($_SESSION["username"])) {
                echo '<li><a class="nav-link" href="index.php">' . $_SESSION["username"] . '\'s Posts</a></li>
                    <li><a class="nav-link" href="index.php?section=register&edit=true&id=' . $_SESSION["username"] .
                    '">Edit profile</a></li>
                          <li class="nav-item">
                            <a class="nav-link" href="inc/backend.php?type=logout"><img src="res/img/logout.svg" 
                            alt="Logout" width="25px"> Logout</a>
                          </li>';
            }else{
                echo '<li class="nav-item">
                            <a class="nav-link" href="index.php?section=login"><img src="res/img/login.svg" 
                            alt="Login" width="25px"> Login</a>
                          </li>';
            }
            ?>
        </ul>
    </div>
</nav>

<?php
    //Message Banner
    if(isset($_GET["login"]) && ($_GET["action"] == "success")) {
        echo MsgFactory::getSuccess("Action performed successfully!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "fail") && ($_GET["section"] == "login")) {
        echo MsgFactory::getWarning("Login failed! Username or password incorrect!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "fail") && ($_GET["section"] == "register")) {
        echo MsgFactory::getWarning("Registration failed! Username not valid or exists!");
    }elseif (isset($_GET["action"]) && ($_GET["action"] == "success") && isset($_GET["newPw"])) {
        echo MsgFactory::getSuccess("<h4>Password changed successfully!</h4> <p>Your new password:\n" . $_GET["newPw"] . "</p><p>Best regards,<br>The Admin</p>");
    }

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
        }
    }
?>
</body>
</html>