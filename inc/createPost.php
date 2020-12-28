

<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

</head>
<body>

<form method="post" action="createPost.php" enctype="multipart/form-data">
	<input type="file" name="picture">
    </br>
    <input type="text" placeholder="Tags " name="tags">
    </br>
	<input type="submit" name="upload" value="Upload" />

</form>
<?php
include_once "../utility/DB.php";
include_once "../model/User.php";


$db = new DB();
if(isset($_FILES["picture"])){
    $z=$_FILES["picture"]["name"];
    $pathway="D:\\Pathway\\to\\img\\".$z.".jpg";
    move_uploaded_file($_FILES["picture"]["tmp_name"],$pathway);
    $path=$pathway;
    $restricted = 0;
    $db->createPost($path,$restricted);
    }

?>
</body>
</html>