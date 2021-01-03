
<form method="post" action="index.php?section=create&upload=true" enctype="multipart/form-data">
	<input type="file" name="picture">
    </br>
    <input type="text" placeholder="Tags " name="tags" id="tags">
    </br>
	<input type="submit" name="upload" value="Upload" />

</form>
<?php
$db = new DB();
include_once "utility/DB.php";
include_once "model/User.php";
$z=$_SESSION["username"];
$spath = "pictures\\full\\$z\\";

if(!is_dir($spath)){
    mkdir($spath,0700);
}
if(isset($_GET["upload"])){
   
    $name=$_FILES["picture"]["name"];
    $pathway="pictures\\full\\$z\\$name";
        move_uploaded_file($_FILES["picture"]["tmp_name"],$pathway );
        $restricted = 0;
        if(isset($_POST["tags"])){
            $tag=$_POST["tags"];
                  $db->checkTag($tag);
                $result=$db->createPost($pathway,$restricted);
                $db->setTag($result,$tag);

            
        }

    }
    $post = $db->showDashboardself();

    $post2 = array_reverse($post);

    foreach($post2 as $dash) {
        echo "<img class=pic src=" .$dash["path"]."   />";
    }

?>
