<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/utility/DB.php";
$db = new DB();
$user = $db->getUser($_SESSION["username"]);
$db->addRating(intval($_POST["id"]), $user->getId(), 1);
$like = $db->showRatings(intval($_POST["id"]), 1);
$dislike = $db->showRatings(intval($_POST["id"]), 0);
echo json_encode(array('like' => strval($like), 'dislike' => strval($dislike)));

