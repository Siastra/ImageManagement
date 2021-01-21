
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
        echo "<div class=\"headerBox\" >";
        echo "<img class=profilepic src=".$post->getUser()->getPicture()." >";
        echo "<span class=\"username\" >".$post->getUser()->getUsername()."</span>";
        echo "</div>";
        echo "<div class=\" headerBox headerBox2 \" >";
        $restriction=$post->getRestricted();
        if($restriction=="0"){
            echo "Public";
            echo "<img  class=img-fluid src=\"res/images/public.svg\" width=25px height=25px >";
            
        }else{
            echo "Private";
            echo "<img class=img-fluid src=\"res/images/private.svg\" width=25px height=25px >";
        }
        echo "</div>";
        echo "<div class=\"headerBox3 headerBox\" >" ;
        echo "Created at: ".$post->getDate();
        echo "</div></div>";


        echo "<div class=\"row picBackground\">";
        echo "<a class=\" col-12\" href=" . $post->getFullPath() . " data-lightbox=" . $post->getName() .
            " data-title=" . $post->getName() . ">";
        echo "<img class=img-fluid src=" . $post->getDashPath() . " alt=" . $post->getName() . ">";
        echo "</a>";
        echo "</div>";


        echo "<div class=row>";
        $currLike=$db->showRatings($post->getId(),1);
        echo '<div class="ratingBox footerBox">';
        echo "<img class=\"ratingPic\" alt=\"Like Button\" src=\"res/images/thumb-up.svg\" onclick=\"upVote(" . $post->getId()
            . ")\" />";
        echo "<span id=likeCounter>".$db->showRatings($post->getId(),1)."</span>";
        echo '</div>';

        echo '<div class="ratingBox footerBox">';
        $currDislike=$db->showRatings($post->getId(),0);
        echo "<img class=\"ratingPic\" alt=\"Dislike Button\" src=\"res/images/thumb-down.svg\" onclick=\"downVote(" . $post->getId()
            . ")\"/>";
        echo "<span id=dislikeCounter>".$db->showRatings($post->getId(),0)."</span>";
        echo "</div>";

        echo "<div class=\"tagBox footerBox\" >";
        echo "TAGS:";
        $tags=$db->readTags($post->getId());
        $size=count($tags);
        for($i=0;$i<$size;$i++){
            echo "<span class=\"tags \" >".$tags[$i]["tag_name"]."</span>";
        }
        echo "</div>";
        echo "<div class=\" footerBox textBox\" >";
        if($post->getText()!=""){
            echo $post->getText();
        }else{
            echo "This Picture has no Caption!";
        }
        echo "</div>";
        echo "<input class=\"commentTextBox footerBox\" type=\"text\"  placeholder=\"Comments\" name=\"comment\"  />";
        echo "<button class=\"commentSendBox footerBox btn btn-success\"  type=\"submit\" name=\"sendcomment\"  > Send!</button>";
        echo "</div>";

        echo "<div class=\"row commSection\" >";

        echo "</div>";
        echo "</div>";
    }

      ?>    
       
      
  
    <script>

        function upVote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/upvote.php',
                data:{id:x},
                success: function(rating){
                    var jsonData = JSON.parse(rating);
                    $("#likeCounter").html(jsonData[0])
                    $("#dislikeCounter").html(jsonData[1]);

                }
            });

        }

        function downVote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data:{id:x},
                success: function(rating){
                    var jsonData = JSON.parse(rating);
                    $("#likeCounter").html(jsonData[0]);
                    $("#dislikeCounter").html(jsonData[1]);

                }
            });
            

        }
    </script>
</div>
