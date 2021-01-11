
<div class="container">
<?php
    include_once "utility/DB.php";
    $db = new DB();
    if($_SESSION["username"]!=""){

        $post = $db->showDashboardall();


    }else{
        $post = $db->showDashboardpublic();
    }   
$post2 = array_reverse($post);
echo "<div class=container-fluid>";
foreach($post2 as $dash) {
    $like="res\\img\\Like.jpg";
    echo "<img class=\"img-fluid\" src=" .$dash["path"]."   /> ";
    echo "<img class=ldl src=".$like." />";
    echo "<img class=ldl src=res/img/Dislike.png />";

}
echo "</div>"
      ?>    
       
      
</div>
