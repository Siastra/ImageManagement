<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/utility/DB.php";
$db = new DB;
if($_POST['action'] == 'upvote'){
    $db->addRating($_POST["path"], 1);
}
