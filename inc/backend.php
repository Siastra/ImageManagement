<?php
include_once "../utility/DB.php";
include_once "../utility/Email.php";
session_start();

function generateRandomString($length = 10): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$db = new DB();
if ($_REQUEST["type"] == "insert") {
    $newUser = new User(1, $_REQUEST["title"], $_REQUEST["fname"], $_REQUEST["lname"], $_REQUEST["email"],
        $_REQUEST["username"], $_REQUEST["pwRepeat"], 0, 1, $_REQUEST["picture"]);

    if ($db->registerUser($newUser)) {
        header("Location: ../index.php?success=registerSuccess");
    } else {
        header("Location: ../index.php?section=register&fail=registerFail");
    }
} elseif ($_REQUEST["type"] == "delete") {
    $db->deleteUser($_REQUEST["id"]);
    header("Location: ../index.php?section=view");
} elseif ($_REQUEST["type"] == "update") {
    $user = new User($_REQUEST["id"], $_REQUEST["title"], $_REQUEST["fname"], $_REQUEST["lname"],
        $_REQUEST["username"], '', $_REQUEST["email"], 0, 1, NULL);
    $_SESSION["username"] = $_REQUEST["username"]; //if username is updated
    if ($db->updateUser($user)) {
        header("Location: ../index.php?success=update");
    } else {
        header("Location: ../index.php?section=register&type=edit&fail=updateFail");
    }
} elseif ($_REQUEST["type"] == "login") {
    switch ($db->loginUser($_REQUEST["username"], $_REQUEST["pw"])) {
        case 0:
            header("Location: ../index.php?section=login&fail=wrongPassword");
            break;
        case 1:
            $user = $db->getUser($_REQUEST["username"]);
            $_SESSION["username"] = $_REQUEST["username"];
            header("Location: ../index.php?success=login");
            break;
        case -1:
            header("Location: ../index.php?section=login&fail=accountDeactivated");
            break;
        case -2:
            header("Location: ../index.php?section=login&fail=loginUserNotFound");
            break;
    }
} elseif ($_REQUEST["type"] == "logout") {
    $_SESSION = array();
    session_destroy();
    header("Location: ../index.php?success=logout");
} elseif ($_REQUEST["type"] == "forgotPassword") {
    $newPw = generateRandomString();
    $user = $db->getUser($_REQUEST["username"]);
    if ($user != NULL) {
        $user->setPassword($newPw);
        if ($db->updatePassword($user)) {
            Email::sendnewPw($user);
            header("Location: ../index.php?success=updatePassword");
        } else {
            header("Location: ../index.php?section=forgotPw&fail=updatePasswordFailed");
        }
    }else {
        header("Location: ../index.php?section=forgotPw&fail=userNotFound");
    }
} elseif ($_REQUEST["type"] == "changePassword") {
    if ($db->loginUser($_REQUEST["username"], $_REQUEST["oldPw"])) {
        $newPw = $_REQUEST["pw"];
        $user = $db->getUser($_REQUEST["username"]);
        $user->setPassword($newPw);
        if ($db->updatePassword($user)) {
            echo "Record updated successfully";
            header("Location: ../index.php?success=updatePassword");
        } else {
            header("Location: ../index.php?fail=updatePasswordFailed");
        }
    } else {
        header("Location: ../index.php?section=register&edit=true&fail=updatePasswordFailedWrong");
    }
} elseif ($_REQUEST["type"] == "changeStatus") {
    if ($db->changeStatus($_REQUEST["username"])) {
        header("Location: ../index.php?section=view");
    }
} elseif ($_REQUEST["type"] == "uploadIcon") {
    if ($db->uploadIcon($_FILES)) {
        header("Location: ../index.php?section=register&edit=true&success=uploadIcon");
    }else{
        header("Location: ../index.php?section=register&edit=true&fail=UploadFail");
    }
}

?>
