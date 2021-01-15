
<div class="container">
<?php
    include_once "utility/DB.php";
    $db = new DB();
    if($_SESSION["username"]!=""){
        $post = $db->showDashboardAll();
    }else{
        $post = $db->showDashboardPublic();
    }   
$post2 = array_reverse($post);
echo "<div class=container-fluid>";
foreach($post2 as $dash) {
    echo "<img class=\"images-fluid\" src=" .$dash["path"]."   /> ";
    echo "<img class=ldl src=\"res/images/thumb-up.svg\" />";
    echo "<img class=ldl src=\"res/images/thumb-down.svg\" />";

}
echo "</div>"
      ?>    
       
      
</div>
