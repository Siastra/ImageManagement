<div class="container">
    <?php
    include_once "utility/DB.php";
    $db = new DB();
    $tags = $db->listAllTags();
    echo '<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter"> Filter </button>';
    echo '<div class="collapse" id="collapseFilter">';
    echo '<form class="form-inline" method="get" action="">';
    //echo '<ul class="dropdown-menu checkbox-menu allow-focus">';
    echo '<div>';
    foreach($tags as $tag){

        echo '<div class="form-check">
                    <input type="checkbox" class="form-check-input" name="tag[]" id="'.$tag.'" value="'.$tag.'">
                    <label for="'.$tag.'" class="form-check-label">'.$tag.'</label>
              </div>';
    }
    echo '</div>';
    echo '<div>';
    echo '<div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="timespan" id="timespan1" value="1d">
            <label class="form-check-label" for="timespan1"><1d</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="timespan" id="timespan2" value="1w">
            <label class="form-check-label" for="timespan2"><1w</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="timespan" id="timespan3" value="1m">
            <label class="form-check-label" for="timespan3">>1w</label>
          </div>';
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Speichern</button>';
    echo '</div>';
    echo '</form>';
    //echo '</div>';
    if(isset($_SESSION["username"])){
        $posts = $db->showDashboardPublic();
        if(isset($_GET["tag"])) {
            if(gettype($_GET["tag"]) == 'string'){
                $_GET["tag"] = array($_GET["tag"]);
            }
            $posts = $db->checkTags($posts, $_GET['tag']);
        }
        if(isset($_GET["timespan"])){
            $posts = $db->filterDate($posts, $_GET["timespan"]);
        }
    }else{
        $posts = $db->showDashboardPrivate();
        if(isset($_GET["tag"])) {
            if(gettype($_GET["tag"]) == 'string'){
                $_GET["tag"] = array($_GET["tag"]);
            }
            $posts = $db->checkTags($posts, $_GET['tag']);
        }
        if(isset($_GET["timespan"])){
            $posts = $db->filterDate($posts, $_GET["timespan"]);
        }
    }
    $posts = array_reverse($posts);

    foreach($posts as $post) {
        $post_id = $post->getId();
        $restriction=$post->getRestricted();

        echo "<div class=\"post\" id='post" . $post_id . "'>
                    <div class=row>
                        <div class=\"headerBox\" >
                            <img class=profilepic src=" . $post->getUser()->getPicture() . " >
                            <span class=\"username\" >" . $post->getUser()->getUsername() . "</span>
                        </div>
                        <div class=\" headerBox headerBox2 \" >" .
                        "<img  class=img-fluid src=\"res/images/" .
                        (($restriction == "0") ? "public" : "private") . ".svg\" width=25px height=25px ><span class=\"username\">".
            (($restriction == "0") ? "Public" : "Private") .

                        "</span></div>
                        <div class=\"headerBox3 headerBox\">
                            Created at: " . $post->getDate() . "
                        </div>
                    </div>
                    
                    <div class=\"row picBackground\">
                        <a class=\" col-12\" href=" . $post->getFullPath() . " data-lightbox=" . $post->getName() .
            " data-title=" . $post->getName() . ">
                            <img class=img-fluid src=" . $post->getDashPath() . " alt=" . $post->getName() . ">
                        </a>
                    </div>

                    <div class=row>
                        <div class=\"ratingBox footerBox\">
                            <img class=\"ratingPic\" alt=\"Like Button\" src=\"res/images/thumb-up.svg\" 
                                 onclick=\"upVote(" . $post_id . ")\" />
                            <span id=likeCounter" . $post_id . ">" . $db->showRatings($post_id, 1) . "</span>
                        </div>

                        <div class=\"ratingBox footerBox\">
                            <img class=\"ratingPic\" alt=\"Dislike Button\" src=\"res/images/thumb-down.svg\" 
                                 onclick=\"downVote(" . $post_id . ")\"/>
                            <span id=dislikeCounter" . $post_id . ">" . $db->showRatings($post_id, 0) . "</span>
                        </div>

                        <div class=\"tagBox footerBox\" >
                            TAGS:";
        $tags = $db->readTags($post_id);
        $size = count($tags);
        for ($i = 0; $i < $size; $i++) {
            echo "<span class=\"tags \" >" . $tags[$i]["tag_name"] . "</span>";
        }
        echo "      </div>
                        <div class=\" footerBox textBox\" >" .
            (($post->getText() != "") ? $post->getText() : "This Picture has no Caption!") .
            "       </div>
                        <input class=\"commentTextBox footerBox\" type=\"text\"  placeholder=\"Comments\" 
                        name=\"comment\" id=\"commentBox" . $post_id . "\"/>
                        <button class=\"commentSendBox footerBox btn btn-success\"  type=\"submit\" name=\"sendcomment\"
                                onclick=\"postComment(" . $post_id . ")\"> Send!</button>
                    </div>";
        $comments = array_reverse($db->getAllCommentsByPost($post_id));
        echo "      <div class=\"row commSection\" id=\"commentSection" . $post_id . "\" >";
        foreach ($comments as $comment) {
            echo '<div class="comment">' . $comment->getUser()->getUsername() . '(' .
                $comment->getDate() . '): ' . $comment->getText() . '</div>';
        }
        echo "       </div>";
        echo "</div>"; //End of post
    }

      ?>

    <script>

        function upVote(post_id) {
            $.ajax({
                type: "POST",
                url: 'ajax/upvote.php',
                data: {id: post_id}
            }).then(
                // resolve/success callback
                function (rating) {
                    let jsonData = JSON.parse(rating);
                    $('#likeCounter' + post_id).html(jsonData.like);
                    $('#dislikeCounter' + post_id).html(jsonData.dislike);
                },
                // reject/failure callback
                function () {
                    alert('There was some error!');
                }
            );

        }

        function downVote(post_id) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data: {id: post_id}
            }).then(
                // resolve/success callback
                function (rating) {
                    let jsonData = JSON.parse(rating);
                    $('#likeCounter' + post_id).html(jsonData.like);
                    $('#dislikeCounter' + post_id).html(jsonData.dislike);
                },
                // reject/failure callback
                function () {
                    alert('There was some error!');
                }
            );
        }

        function postComment(post_id) {
            let comment = $('#commentBox' + post_id).val();
            if (comment !== "") {
                $.ajax({
                    type: "POST",
                    url: 'ajax/postComment.php',
                    data: {
                        id: post_id,
                        comment: comment
                    },
                    success: function (response) {
                        let jsonData = JSON.parse(response);

                        // user is logged in successfully in the back-end
                        // let's redirect
                        if (jsonData.success === '1') {
                            let old = $('#commentSection' + post_id).html();
                            $('#commentSection' + post_id).html("<div class='comment'>" + jsonData.commentUser + "(" +
                                jsonData.date + "): " +
                                jsonData.commentText + "</div>" + old);
                            $('#commentBox' + post_id).val("");
                        } else {
                            alert('Comment not posted!');
                        }
                    }
                });
            }
        }
    </script>
</div>
