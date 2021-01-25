<script>
    $(document).ready(function () {
        var x = document.getElementsByTagName("TITLE")[0];
        x.innerHTML = "Dashboard";
    });

</script>

<div class="container">
    <?php
    include_once "utility/DB.php";
    $db = new DB();
    $tags = $db->listAllTags();
    $users  = $db->getUserList();

    echo '<div class="nav-center pt-5">
            <form class="form-inline row justify-content-md-center" method="GET" action=""> <!--searchbar-->
                <input class="form-control col col-lg-2" type="search" name="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0 col col-lg-2" type="submit">Search</button>';
    if (isset($_GET["tag"])) { //if any filter is set it gets coppied
        foreach ($_GET["tag"] as $tag) {
            echo '<input type="hidden" name=tag[] value="' . $tag . '">';
        }
    }
    if (isset($_GET["timespan"])) {
        echo '<input type="hidden" name=timespan value="' . $_GET["timespan"] . '">';
    }
    if (isset($_GET["userid"])) {
        echo '<input type="hidden" name=userid value="' . $_GET["userid"] . '">';
    }
    echo '</form>
        </div>';

    echo '<button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#collapseFilter" id="filterButton" <!--aria-expanded="false" aria-controls="collapseFilter-->"> Filter </button>';
    echo '<div class="collapse" id="collapseFilter">';
    echo '<form class="" method="get" action="" style="background-color: rgba(180, 230, 255,1)">';//filterform
    echo '<div class="row flex-fill px-5 py-2 mb-3">
            <label class="col-form-label col-md-12">Tags:</label>';

    foreach($tags as $tag){//for each tag in the database a checkbox

        echo '<div class="form-group ml-5 mx-4 mt-2"> <!--col-sm-14-->
                    <input type="checkbox" class="form-check-input" name="tag[]" value="'.$tag.'">                    
                    <label for="'.$tag.'" class="form-check-label">'.$tag.'</label>
             </div>';
    }
    echo '</div>';
    echo '<div class="py-2">';
    echo '<div class="flex-fill px-5 row">
             <div class="col-md-12">
                <div class="d-flex row">
                    <div class="col-md-5 flex-fill px-4">
                    <label class="col-form-label">Timespan:</label>
                        <div class="form-group ml-5">
                            <input class="form-check-input" type="radio" name="timespan" id="timespan1" value="1d">
                            <label class="form-check-label" for="timespan1"><1d</label>
                        </div>
                        <div class="form-group ml-5">
                            <input class="form-check-input" type="radio" name="timespan" id="timespan2" value="1w">
                            <label class="form-check-label" for="timespan2"><1w</label>
                        </div>
                        <div class="form-group ml-5">
                           <input class="form-check-input" type="radio" name="timespan" id="timespan3" value="1m">
                           <label class="form-check-label" for="timespan3">>1w</label>

                        </div>
                    </div>
                    <div class="d-inline-flex col-md-6 flex-fill">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Users:</label>
                            <select name=userid class="form-control ml-5" id="exampleFormControlSelect1">
                            <option value=""></option>';

    foreach($users as $user){//for each user a select option
        $username = $user->getUsername();
        $userId = $user->getId();
        echo '<option value="'.$username.'">'.$username.'</option>';
    }
    echo '</select>
                        </div> 
                    </div>
                    <div class="d-flex col-md-1">
                        <button type="submit" class="btn btn-primary align-self-end">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    </div>';
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
    //if(isset($_SESSION["username"])){
        if(isset($_GET["tag"])){
            if(gettype($_GET["tag"]) == 'string'){//checkTags expects an array so if $_GET["tag"] is a string put it in an array
                $_GET["tag"] = array($_GET["tag"]);
            }
            $posts = $db->checkTags($posts, $_GET['tag']);
        }
        if(isset($_GET["timespan"])){
            $posts = $db->filterDate($posts, $_GET["timespan"]);
        }
        if(isset($_GET["userid"])){
            if ($_GET["userid"] != "") {
                $user = $db->getUser($_GET["userid"]);
                $userId= $user->getId();
                $temp = array();
                foreach ($posts as $post) {//check if user id of each post is the same as the one filtered for
                    $postUser = $post->getUser();
                    $postUserId = $postUser->getId();
                    if($userId == $postUserId) {

                        array_push($temp, $post);
                    }
                }
                $posts = $temp;
            }
        }
        if(isset($_GET["search"])){
            $posts = $db->checkSearchRequest($posts, $_GET["search"]);
        }
    /*}else{
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
        if(isset($_GET["search"])){
            $posts = $db->checkSearchRequest($posts, $_GET["search"]);
        }*/

    $posts = array_reverse($posts);
    if(empty($posts)){
        echo MsgFactory::getWarning("<b>No posts with matching requirements</b>");

    }
    foreach($posts as $post) {
        $post_id = $post->getId();
        $restriction=$post->getRestricted();

        echo "<div class=\"post\" id='post" . $post_id . "'>
                    <div class=row>
                        <div class=\"headerBox\" >
                            <img alt=ProfilePic class=profilepic src=" . $post->getUser()->getPicture() . " >
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
        $('div.form-group').on('change', function(evt) {//if form-group is clicked on
            var $input = $(this).siblings(".form-group").children(".form-check-input:checked").length; //get number of the children of the siblings where checked = true
            if($input >= limit) {//if this amount is more or equal to limit set checked of the currently clicked on to false;
                $(this).children(".form-check-input").prop("checked", false);
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
