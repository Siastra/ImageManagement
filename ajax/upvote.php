<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/ImageManagement/utility/DB.php";
$db = new DB;
$file='test.txt';
$content=serialize(intval($_POST["id"]));
file_put_contents($file,$content);
$db->addRating(intval($_POST["id"]), 1);
$like=$db->showRatings(intval($_POST["id"]),1);
$dislike=$db->showRatings(intval($_POST["id"]),0);
$file='test.txt';
$content=serialize(array($like,$dislike));
file_put_contents($file,$content);
echo json_encode(array($like,$dislike));


?>