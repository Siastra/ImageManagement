
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
        $post_id = $post->getId();
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
        echo '<div class="ratingBox footerBox">';
        echo "<img class=\"ratingPic\" alt=\"Like Button\" src=\"res/images/thumb-up.svg\" 
        onclick=\"upVote(" . $post_id . ")\" />";
        echo "<span id=likeCounter" . $post_id . ">".$db->showRatings($post_id,1)."</span>";
        echo '</div>';

        echo '<div class="ratingBox footerBox">';
        echo "<img class=\"ratingPic\" alt=\"Dislike Button\" src=\"res/images/thumb-down.svg\" 
        onclick=\"downVote(" . $post_id . ")\"/>";
        echo "<span id=dislikeCounter" . $post_id . ">".$db->showRatings($post_id,0)."</span>";
        echo "</div>";

        echo "<div class=\"tagBox footerBox\" >";
        echo "TAGS:";
        $tags=$db->readTags($post_id);
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
        echo "<input class=\"commentTextBox footerBox\" type=\"text\"  placeholder=\"Comments\" name=\"comment\"
                     id=\"commentBox" . $post_id . "\"/>";
        echo "<button class=\"commentSendBox footerBox btn btn-success\"  type=\"submit\" name=\"sendcomment\"
                      onclick=\"postComment(" . $post_id . ")\"> Send!</button>";
        echo "</div>";
        $comments = array_reverse($db->getAllCommentsByPost($post_id));
        echo "<div class=\"row commSection\" id=\"commentSection" . $post_id . "\" >";
        foreach ($comments as $comment) {
            echo '<div class="comment">' . $comment->getUser()->getUsername() . '(' . $comment->getDate() . '): ' .
                                $comment->getText() . '</div>';
        }
        echo "</div>";
        echo "</div>";
    }

      ?>    
       
      
  
    <script>

        function upVote(post_id) {
            $.ajax({
                type: "POST",
                url: 'ajax/upvote.php',
                data:{id:post_id}
            }).then(
                // resolve/success callback
                function(rating)
                {
                    let jsonData = JSON.parse(rating);
                    $('#likeCounter'+post_id).html(jsonData.like);
                    $('#dislikeCounter'+post_id).html(jsonData.dislike);
                },
                // reject/failure callback
                function()
                {
                    alert('There was some error!');
                }
            );

        }

        function downVote(post_id) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data:{id:post_id}
            }).then(
                // resolve/success callback
                function(rating)
                {
                    let jsonData = JSON.parse(rating);
                    $('#likeCounter'+post_id).html(jsonData.like);
                    $('#dislikeCounter'+post_id).html(jsonData.dislike);
                },
                // reject/failure callback
                function()
                {
                    alert('There was some error!');
                }
            );
        }

        function postComment(post_id) {
            let comment = $('#commentBox'+post_id).val();
            if (comment !== "") {
                $.ajax({
                    type: "POST",
                    url: 'ajax/postComment.php',
                    data:{
                        id:post_id,
                        comment: comment
                    },
                    success: function(response)
                    {
                        let jsonData = JSON.parse(response);

                        // user is logged in successfully in the back-end
                        // let's redirect
                        if (jsonData.success === '1')
                        {
                            let old =  $('#commentSection'+post_id).html();
                            $('#commentSection'+post_id).html("<div class='comment'>" + jsonData.commentUser + "(" +
                                jsonData.date + "): " +
                                jsonData.commentText + "</div>" + old);
                            $('#commentBox'+post_id).val("");
                        }
                        else
                        {
                            alert('Comment not posted!');
                        }
                    }
                });
            }
        }
    </script>
</div>
