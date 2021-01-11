
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
   

        
    
    $file = $_FILES['picture']['tmp_name'];
        $sourceProperties = getimagesize($file);
        $fileNewName = explode(".", $_FILES['picture']['name'])[0];
        $folderPathDash =  'pictures/dashboard/';
        $folderPathThumb = 'pictures/thumbnail/';
        $folderPathFull = 'pictures/full/';
        $fullpathDash = getcwd() . "/" . $folderPathDash;
        $fullpathThumb = getcwd() . "/".$folderPathThumb;
        $fullpath = getcwd() . "/" . $folderPathFull;
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $imageType = $sourceProperties[2];


        switch ($imageType) {


            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file);
                $targetLayer = imageResizeThump($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                imagepng($targetLayer, $fullpathThumb . $fileNewName . ".". $ext);
                $imageResourceId1 = imagecreatefrompng($file);
                $targetLayer1 = imageResizeDash($imageResourceId1, $sourceProperties[0], $sourceProperties[1]);
                imagepng($targetLayer1, $fullpathDash . $fileNewName . ".". $ext);
               
                break;


            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file);
                $targetLayer = imageResizeThump($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                imagejpeg($targetLayer, $fullpathThumb . $fileNewName .".". $ext);
                $imageResourceId1 = imagecreatefromjpeg($file);
                $targetLayer1 = imageResizeDash($imageResourceId1, $sourceProperties[0], $sourceProperties[1]);
                imagejpeg($targetLayer1, $fullpathDash . $fileNewName .".".  $ext);
                
                break;


            default:
                echo "Invalid Image type.";
                exit;
        }


        move_uploaded_file($file, $fullpath . $fileNewName . "." . $ext);
        $restricted = $_POST["restriction"];
        if(isset($_POST["tags"])){
            $tag=$_POST["tags"];
                  $db->checkTag($tag);
                $result= $db->createPost($folderPathDash . $fileNewName . "." . $ext,$restricted);
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
