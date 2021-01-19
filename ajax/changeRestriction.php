<?php

include_once $_SERVER["DOCUMENT_ROOT"]."/utility/DB.php";

$db = new DB;
$db->changeRestriction($_POST["id"]);
