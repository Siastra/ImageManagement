
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
            $divide="/[-\s:]/";
            $tag2=preg_split($divide,$tag);
            $db->checkTag($tag2);
            $result= $db->createPost('pictures/dashboard/' . explode(".", $_FILES['picture']['name'])[0]
            . "." . (pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION)),$restricted);
            $db->setTag($result,$tag2);

            
        }
        header("Location: index.php?section=dash");
    
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
