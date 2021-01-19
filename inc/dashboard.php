
<div class="container">
<?php
    include_once "utility/DB.php";
    $db = new DB();
    if(isset($_SESSION["username"])){
        $posts = $db->showDashboardPublic();
    }else{
        $posts = $db->showDashboardPrivate();
    }   
    $posts = array_reverse($posts);

    foreach($posts as $post) {

        echo "<div class=\"post\">";
        echo "<div class=row>";
        echo "<div class=\"col-1 img-fluid\" >";
        echo "<img class=profilepic src=".$post->getUser()->getPicture()." >";
        echo "</div>";
        echo "<div class=col>";
        echo $post->getUser()->getUsername();
        echo "</div>";
        echo "<div class=\" col \" >";
        echo "restriction";
        echo "</div>";
        echo "<div class=\"col\" >" ;
        echo "<button>Lösch dich </button>";
        echo "</div></div>";


        echo "<div class=\"row picBackground\">";
        echo "<a class=\" col-12\" href=" . $post->getFullPath() . " data-lightbox=" . $post->getName() .
            " data-title=" . $post->getName() . ">";
        echo "<img class=img-fluid src=" . $post->getDashPath() . " alt=" . $post->getName() . ">";
        echo "</a>";
        echo "</div>";

        echo "<div class=row>";
        echo '<div class="col-1">';
        echo "<img alt=\"Like Button\" src=\"res/images/thumb-up.svg\" />";
        echo "<p>5</p>";
        echo '</div>';
        echo '<div class="col-1">';
        echo "<img alt=\"Dislike Button\" src=\"res/images/thumb-down.svg\"/>";
        echo "<p>4</p>";
        echo "</div>";
        echo "<div class=col-4>";
        echo "TAGS:";
        $tags=$db->readTags($post->getId());
        $size=count($tags);
        for($i=0;$i<$size;$i++){
            echo "<span class=\"tags\" >".$tags[$i]["tag_name"]."</span>";
        }
        echo "</div>";
        echo "<input class= \"col-3\" type=\"text\"  placeholder=\"Comments\" name=\"comment\"  />";
        echo "<input class= \"col-2\"  type=\"submit\" name=\"sendcomment\"  />";
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

        function downVote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data:{action:'downvote', path:x},
            });
        }
    </script>
</div>
