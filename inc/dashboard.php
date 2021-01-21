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
    echo '<div class="row">
            <div class="col form-group">
                <label for="picture">Picture:</label>
                    <input type="checkbox" data-toggle="toggle" data-on="with" data-off="without"
                        data-onstyle="success" data-offstyle="danger" class="col-1" data-size="small"
                        id="picture" checked name="picture">
            </div>
          </div>';
    echo '<div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="timespan" id="timespan1" value="1d">
            <label class="form-check-label" for="timespan1">1</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="timespan" id="timespan2" value="1w">
            <label class="form-check-label" for="timespan2">2</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="timespan" id="timespan3" value="1m">
            <label class="form-check-label" for="timespan3">3</label>
          </div>';
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
    }else{
        $posts = $db->showDashboardPrivate();
        if(isset($_GET["tag"])) {
            if(gettype($_GET["tag"]) == 'string'){
                $_GET["tag"] = array($_GET["tag"]);
            }
            $posts = $db->checkTags($posts, $_GET['tag']);
        }
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
        echo '<div class="ratingBox footerBox">';
        echo "<img class=\"ratingPic\" alt=\"Like Button\" src=\"res/images/thumb-up.svg\" 
        onclick=\"upVote(" . $post->getId() . ")\" />";
        echo "<span id=likeCounter" . $post->getId() . ">".$db->showRatings($post->getId(),1)."</span>";
        echo '</div>';

        echo '<div class="ratingBox footerBox">';
        echo "<img class=\"ratingPic\" alt=\"Dislike Button\" src=\"res/images/thumb-down.svg\" 
        onclick=\"downVote(" . $post->getId() . ")\"/>";
        echo "<span id=dislikeCounter" . $post->getId() . ">".$db->showRatings($post->getId(),0)."</span>";
        echo "</div>";

        echo "<div class=\"tagBox footerBox\" >";
        echo "TAGS:";
        $tags=$db->readTags($post->getId());
        $size=count($tags);
        for($i=0;$i<$size;$i++){
            echo "<span class=\"tags \" >".$tags[$i]["tag_name"]."</span>";
        }
        echo "</div>";
        echo "<div class=\"textBox footerBox\" >";
        echo "askdondposakndpasndpüna";
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
        function upvote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/upvote.php',
                data:{id:x}
            }).then(
                // resolve/success callback
                function(rating)
                {
                    let jsonData = JSON.parse(rating);
                    $('#likeCounter'+x).html(jsonData.like);
                    $('#dislikeCounter'+x).html(jsonData.dislike);
                },
                // reject/failure callback
                function()
                {
                    alert('There was some error!');
                }
            );

        }

        function downVote(x) {
            $.ajax({
                type: "POST",
                url: 'ajax/downvote.php',
                data:{id:x}
            }).then(
                // resolve/success callback
                function(rating)
                {
                    let jsonData = JSON.parse(rating);
                    $('#likeCounter'+x).html(jsonData.like);
                    $('#dislikeCounter'+x).html(jsonData.dislike);
                },
                // reject/failure callback
                function()
                {
                    alert('There was some error!');
                }
            );


        }
    </script>
</div>
