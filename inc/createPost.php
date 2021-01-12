
<form method="post" action="index.php?section=create" enctype="multipart/form-data">
	<input type="file" name="picture">
    <br/>
    <label for="tags">Tags:</label>
    <input type="text" placeholder="Tags " name="tags" id="tags" />
    <br/>
    <div class="slidecontainer">
        <label for="myRange">Restriction:</label>
        <input type="range" min="0" max="1" value="0" class="slider" id="myRange" name="restriction" />
        <p>Restriction:<span id="demo"></span></p>
    </div>
	<input type="submit" name="upload" value="Upload" />

</form>
<?php
$db = new DB();
include_once "utility/DB.php";
include_once "model/User.php";
$z=$_SESSION["username"];
$spath = "pictures\\full\\$z\\";


if(isset($_POST["upload"])){
    Upload::uploadPost($_FILES);
    $restricted = $_POST["restriction"];
    if(isset($_POST["tags"])){
        $tag=$_POST["tags"];
        $db = new DB();
        $db->checkTag($tag);
        $result= $db->createPost('pictures/dashboard/' . explode(".", $_FILES['picture']['name'])[0]
            . "." . (pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION)),$restricted);
        $db->setTag($result,$tag);


    }
        header("Location: index.php?section=dash");
    
}


function imageResizeThump($imageResourceId, $width, $height)
{


    $targetWidth = 500;
    $targetHeight = 250;


    $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);


    return $targetLayer;

}

function imageResizeDash($imageResourceId, $width, $height)
{


    $targetWidth = 1500;
    $targetHeight = 750;


    $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);


    return $targetLayer;

}
?>
<script>
let slider = document.getElementById("myRange");
let output = document.getElementById("demo");
output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>
