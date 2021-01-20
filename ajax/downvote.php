<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/ImageManagement/utility/DB.php";
$db = new DB;
    $db->addRating($_POST["id"], 0);
    $like=$db->getRating($_POST["id"],1);
    $dislike=$db->getRating($_POST["id"],0);
    echo json_encode(array($like,$dislike));
