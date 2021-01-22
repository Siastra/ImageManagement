<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/utility/DB.php";
$db = new DB();
$comment = $db->postComment($_POST["id"], $_POST["comment"], new DateTime('now'));
if ($comment != null) {
    echo json_encode(array('success' => '1', 'commentUser' => $comment->getUser()->getUsername(),
        'commentText' => $comment->getText(), 'date' => $comment->getDate()));
} else {
    echo json_encode(array('success' => '0'));
}
