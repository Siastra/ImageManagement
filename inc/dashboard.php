<div class="container">
    <?php
    include_once "utility/DB.php";
    $db = new DB();
    $tags = $db->listAllTags();
    $users  = $db->getUserList();
    echo '<button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#collapseFilter" <!--aria-expanded="false" aria-controls="collapseFilter-->"> Filter </button>';
    echo '<div class="collapse" id="collapseFilter">';
    echo '<form class="form-inline" method="get" action="">';
    //echo '<ul class="dropdown-menu checkbox-menu allow-focus">';
    echo '<div>';
    //echo '<div class="form-check">';
    foreach($tags as $tag){

        echo '
                    <input type="checkbox" class="form-check-input" name="tag[]" value="'.$tag.'">
                    <label for="'.$tag.'" class="form-check-label">'.$tag.'</label>
              ';
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
    echo '<div>';
    foreach($users as $user){
        $username = $user->getUsername();
        $userId = $user->getId();
        echo '
                    <input type="radio" class="form-radio-input" name="userid" value="'.$userId.'">
                    <label for="'.$username.'" class="form-radio-label">'.$username.'</label>
              ';
    }

    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Speichern</button>';
    echo '</div>';
    echo '<form method="post" action="">
        <div class="dropdown">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
        Sort by
        </button>
         <div class="dropdown-menu">
        <button class="dropdown-item" type="submit" value="likesAcending" name="sort">Likes ascending</button>
         <button class="dropdown-item" type="submit" value="likesDescending" name="sort">Likes descending</a>
         <button class="dropdown-item" type="submit" value="dislikesAcending" name="sort">Dislikes ascending</a>
         <button class="dropdown-item" type="submit" value="dislikesDecending" name="sort">Dislikes descending</a>
        <button class="dropdown-item" type="submit" value="CommentsAcending" name="sort">Comments ascending</a>
        <button class="dropdown-item" type="submit" value="CommentsDecending" name="sort">Comments descending</a>
         </div>
        </div>
        </form>';


    if(isset($_GET["sort"])){
        switch($_GET["sort"]){
            case 'likesAcending': $posts=$db->getDashboardByLikes();
            
            break;
            case 'likesDescending':$posts=array_reverse($db->getDashboardByLikes());
            break;
            case 'dislikesAcending':$posts=$db->getDashboardByDislikes();
            break;
            case 'dislikesDecending':$posts=array_reverse($db->getDashboardByDislikes());
            break;
            case 'CommentsAcending':$posts=$db->getDashboardByComments();
            break;
            case 'CommentsDecending':$posts=array_reverse($db->getDashboardByComments());
            break; 
        }
        $sorted =array();
        if(!isset($_SESSION["username"])){

            foreach ($posts as $post) {
                if($post->getRestricted()===0){
                    array_push($sorted,$post);
                }
            }
            
            $posts=$sorted;

        
        }  
        }else{
            if(!isset($_SESSION["username"])){
                $posts=$db->showDashboardPublic();
            }else{
                 $posts=$db->showDashboardAll();
            }
    }
    if(isset($_SESSION["username"])){
        if(isset($_GET["tag"])){
            if(gettype($_GET["tag"]) == 'string'){
                $_GET["tag"] = array($_GET["tag"]);
            }
            $posts = $db->checkTags($posts, $_GET['tag']);
        }
        if(isset($_GET["timespan"])){
            $posts = $db->filterDate($posts, $_GET["timespan"]);
        }
        if(isset($_GET["userid"])){
            if (isset($_GET["userid"])) {
                $temp = array();
                foreach ($posts as $post) {
                    $postUser = $post->getUser();
                    $postUserId = $postUser->getId();
                    foreach($_GET["userid"] as $checkId){
                        if(intval($checkId) == $postUserId){
                            array_push($temp, $post);
                        }
                    }
                }
                $posts = $temp;
            }
        }
        if(isset($_GET["search"])){
            $posts = $db->checkSearchRequest($posts, $_GET["search"]);
        }
    }else{
        if(!isset($posts))
        if(isset($_GET["tag"])){
            if (gettype($_GET["tag"]) == 'string') {
                $_GET["tag"] = array($_GET["tag"]);
            }
            $posts = $db->checkTags($posts, $_GET['tag']);
        }
        if (isset($_GET["timespan"])) {
            $posts = $db->filterDate($posts, $_GET["timespan"]);
        }
        if(isset($_GET["userid"])){
            if (isset($_GET["userid"])) {
                $temp = array();
                foreach ($posts as $post) {
                    $postUser = $post->getUser();
                    $postUserId = $postUser->getId();
                    if(intval($_GET["userid"]) == $postUserId){
                        array_push($temp, $post);
                    }
                }
                $posts = $temp;
            }
        }
    }
    $posts = array_reverse($posts);
    foreach($posts as $post) {
        $post_id = $post->getId();
        $restriction=$post->getRestricted();

        echo "<div class=\"post\" id='post" . $post_id . "'>
                    <div class=row>
                        <div class=\"headerBox\" >
                            <img alt=ProfilPic class=profilepic src=" . $post->getUser()->getPicture() . " >
                            <span class=\"username\" >" . $post->getUser()->getUsername() . "</span>
                        </div>
                        <div class=\" headerBox headerBox2 \" >" .
                        "<img  alt=Restriction class=img-fluid src=\"res/images/" .
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
                            <img alt=Post class=img-fluid src=" . $post->getDashPath() . " alt=" . $post->getName() . ">
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
        //, ".form-check input"
        var limit = 2;
        /*$('div.form-check').on('change', function(evt) {
            $input = $(this).find("input")
            if($input.siblings(':checked').length <= limit) {
                //$input.checked = false;
                //alert($(this).children(':checked').length);
                console.log($input.siblings());
            }

        });*/
        $('input.form-check-input').on('change', function(evt) {
            if($(this).siblings(':checked').length >= limit) {
                this.checked = false;
                //alert($(this).children(':checked').length);
                //console.log($(this).siblings());
            }
        });
    </script>
    <script>

        function upVote(post_id) {
            $.ajax({
                type: "POST",
                url: 'ajax/upvote.php',
                data:{id: post_id},
                success: function (response) {
                    try {
                        let jsonData = JSON.parse(response);
                        $('#likeCounter' + post_id).html(jsonData.like);
                        $('#dislikeCounter' + post_id).html(jsonData.dislike);
                    }catch (e) {
                        location.href = "index.php?fail=RatingUserNotLoggedIn";
                    }
                }
            });
        }

        function downVote(post_id) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data:{id: post_id},
                success: function (response) {
                    try {
                        let jsonData = JSON.parse(response);
                        $('#likeCounter' + post_id).html(jsonData.like);
                        $('#dislikeCounter' + post_id).html(jsonData.dislike);
                    }catch (e) {
                        location.href = "index.php?section=dash&fail=RatingUserNotLoggedIn";
                    }
                }
            });
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
                        try {
                            let jsonData = JSON.parse(response);
                            if (jsonData.success === '1') {
                                let old = $('#commentSection' + post_id).html();
                                $('#commentSection' + post_id).html("<div class='comment'>" + jsonData.commentUser + "(" +
                                    jsonData.date + "): " +
                                    jsonData.commentText + "</div>" + old);
                                $('#commentBox' + post_id).val("");
                            } else {
                                alert('Comment not posted!');
                            }
                        }catch (e) {
                            location.href = "index.php?section=dash&fail=CommentUserNotLoggedIn";
                        }
                    }
                });
            }
        }
    </script>
</div>
