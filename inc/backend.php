<?php
    include_once "../utility/DB.php";
    include_once "../utility/Email.php";
    session_start();

    function generateRandomString($length = 10) : string {
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
        $newUser = new User(1, $_REQUEST["title"], $_REQUEST["fname"], $_REQUEST["lname"], $_REQUEST["username"],
            $_REQUEST["pwRepeat"], $_REQUEST["email"], 0, 1);

        if ($db->registerUser($newUser)) {
            echo "New record created successfully";
            header("Location: ../index.php?action=success");
        } else {
            echo "Error: Something went wrong!";
            header("Location: ../index.php?section=register&action=fail");
        }
    }elseif ($_REQUEST["type"] == "delete") {
        $db->deleteUser($_REQUEST["id"]);
        header("Location: ../index.php?section=view");
    }elseif ($_REQUEST["type"] == "update") {
        $user = new User($_REQUEST["id"], $_REQUEST["title"], $_REQUEST["fname"], $_REQUEST["lname"],
            $_REQUEST["username"], '',  $_REQUEST["email"], 0, 1);
        $_SESSION["username"] = $_REQUEST["username"]; //if username is updated
        if ($db->updateUser($user)) {
            echo "Record updated successfully";
            header("Location: ../index.php?action=success");
        } else {
            echo "Error updating record";
            header("Location: ../index.php?type=edit&action=fail");
        }
    }elseif ($_REQUEST["type"] == "login") {
        if ($db->loginUser($_REQUEST["username"], $_REQUEST["pw"])) {
            $_SESSION["username"] = $_REQUEST["username"];
            header("Location: ../index.php?action=success");
        }else {
            header("Location: ../index.php?section=login&action=fail");
        }
    }elseif ($_REQUEST["type"] == "logout") {
        $_SESSION = array();
        session_destroy();
        header("Location: ../index.php");
    }elseif ($_REQUEST["type"] == "forgotPassword") {
        $newPw = generateRandomString();
        $user = $db->getUser($_REQUEST["username"]);
        $user->setPassword($newPw);
        if ($db->updatePassword($user)) {
            echo "Record updated successfully";
            Email::sendnewPw($user);
            header("Location: ../index.php?action=success");
        } else {
            echo "Error updating record";
        }
    }elseif ($_REQUEST["type"] == "changePassword") {
        if ($db->loginUser($_REQUEST["username"], $_REQUEST["oldPw"])) {
            $newPw = $_REQUEST["pw"];
            $user = $db->getUser($_REQUEST["username"]);
            $user->setPassword($newPw);
            if ($db->updatePassword($user)) {
                echo "Record updated successfully";
                header("Location: ../index.php?action=success");
            } else {
                echo "Error updating record";
            }
        }else {
            header("Location: ../index.php?type=edit&action=fail");
        }
    }

?>
