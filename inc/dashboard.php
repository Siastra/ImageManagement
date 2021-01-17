
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
    echo '<img class=ldl onclick=upvote("'.$dash["path"].'") src="res/images/thumb-up.svg" />';
    echo '<img class=ldl onclick=downVote("'.$dash["path"].'") src="res/images/thumb-down.svg" />';

}
echo "</div>"
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
