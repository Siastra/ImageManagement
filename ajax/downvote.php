<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/utility/DB.php";
$db = new DB;
if($_POST['action'] == 'downvote'){
    $db->addRating($_POST["path"], 0);
}
