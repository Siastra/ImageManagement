
<div class="container">
<?php
    include_once "utility/DB.php";
    $db = new DB();
    if(isset($_SESSION["username"])){
        $post = $db->showDashboardAll();
    }else{
        $post = $db->showDashboardPublic();
    }   
$post2 = array_reverse($post);
$divide ="#/#";
$divide2 ="/[.]/";
foreach($post2 as $dash) {
    $picuserid=$db->getPicCreaterId($dash["path"]);
    $piccreatername=$db->getPostCreater($picuserid);
    $piccreaterpic=$db->getPostPic($picuserid);
   
    $name=preg_split($divide,$dash["path"]);
    $picnameunfinished=$name[2];
    $finishname=preg_split($divide2,$picnameunfinished);
    echo "<div class=\"post\">";
    echo "<div class=row>";
    echo "<div class=\"profilepic col-1 img-fluid\" >";
    echo "<img src=".$piccreaterpic." width=25px height=25px >";
    echo "</div>";
    echo "<div class=col-2>";
    echo $piccreatername;
    echo "</div>";
    echo "<div class=\" col-2  offset-3\" >";
    echo "restriction";
    echo "</div>";
    echo "<div class=\"col-2 offset-2\" >" ;
    echo "<button>Lösch dich </button>";
    echo "</div>";


    echo "<div class=row>";
    echo "<a class=\" col-12\" href=".$dash["path"]." data-lightbox=".$dash["path"]." data-title=".$finishname[0].">";
    echo "<img class=img-fluid src=".$dash["path"].">";
    echo "</a>";
    echo "</div>";



    echo "<div class=row>";
    echo "<img  src=\"res/images/thumb-up.svg\"  class=\" col-1 \" />";
    echo "<p>5</p>";
    echo "<img   src=\"res/images/thumb-down.svg\" class= \"col-1  \" />";
    echo "<p>4</p>";
    echo "<div class=col-3>";
    echo "TAGS";
    echo "</div>";
    echo "<input class= \"col-2 offset-1\" type=\"text\"  placeholder=\"Comments\" name=\"comment\"  />";
    echo "<input class= col-1 offset-2  type=\"submit\" name=\"sendcomment\"  />";
    echo "</div>";


    echo "</div>";
    

}

      ?>    
       
      
  
    <script>
        function upvote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/upvote.php',
                data:{action:'upvote', path:x},
            });
        }
    </script>
    <script>
        function downVote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data:{action:'downvote', path:x},
            });
        }
    </script>
</div>
