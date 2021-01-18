
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
        echo "<div class=\"profilepic col-1 img-fluid\" >";
        echo "<img src=".$post->getUser()->getPicture()." width=25px height=25px >";
        echo "</div>";
        echo "<div class=col-2>";
        echo $post->getUser()->getUsername();
        echo "</div>";
        echo "<div class=\" col-2  offset-3\" >";
        echo "restriction";
        echo "</div>";
        echo "<div class=\"col-2 offset-2\" >" ;
        echo "<button>Lösch dich </button>";
        echo "</div></div>";


        echo "<div class=row>";
        echo "<a class=\" col-12\" href=" . $post->getFullPath() . " data-lightbox=" . $post->getName() .
            " data-title=" . $post->getName() . ">";
        echo "<img class=img-fluid src=" . $post->getDashPath() . ">";
        echo "</a>";
        echo "</div>";

        echo "<div class=row>";
        echo '<div class="col-1">';
        echo "<img  src=\"res/images/thumb-up.svg\" />";
        echo "<p>5</p>";
        echo '</div>';
        echo '<div class="col-1">';
        echo "<img   src=\"res/images/thumb-down.svg\"/>";
        echo "<p>4</p>";
        echo "</div>";
        echo "<div class=col-4>";
        echo "TAGS";
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
