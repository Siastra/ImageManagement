<?php
    $db = new DB();
    $user = $db->getUser($_SESSION["username"]);
    $posts = $db->getPostsByUserID($user->getId());
    echo '<script>
            $(document).ready(function () {
                var x = document.getElementsByTagName("TITLE")[0];
                x.innerHTML = "' . $user->getUsername() . '\'s Profile";
            });

        </script>';
?>

<section class="container">
    <!--User header-->
    <div class="row user-header">
        <div class="col-3">
            <img src="<?php echo $user->getPicture(); ?>" alt="Profile Picture" class="img-fluid user-picture">
        </div>
        <div class="col-9">
            <?php
                echo '<h1>' . $user->getUsername() . '</h1>';
                echo '<p>' . $user->getFname() . ' ' . $user->getLname() . '</p>';
                echo '<p>' . $user->getEmail() . '</p>';
                echo '<p>' . sizeof($posts) . ' Posts</p>';
            ?>
        </div>
    </div>
    <?php
        //Post thumbnails
        for ($i = 0; $i < sizeof($posts); $i++) {
            if ($i == 0) {
                echo '<div class="galleryRow row">';
            }

            echo '<div class="galleryItem">
                    <div class="row itemHeader">
                        <input type="checkbox" ' . (($posts[$i]->getRestricted()) ? '' : 'checked') . ' 
                        data-toggle="toggle" data-on="Public" data-off="Restricted" 
                            data-onstyle="success" data-offstyle="danger" class="col-1" data-size="medium" 
                            onchange="changeRes(' . ($posts[$i]->getId()) . ')">
                        <div class="col">
                            <button class="btn btn-danger float-right" onclick="deletePost(' .
                                ($posts[$i]->getId()) . ')">X</button>
                        </div>
                    </div>
                    <div class="row">
                        <a class="col" href="index.php?section=dash#post' . ($posts[$i]->getId()) . '"><img alt="' . $posts[$i]->getName() .
                '" src="' . $posts[$i]->getThumbnailPath() . '"></a>
                    </div>
                </div>';
            if (($i == (sizeof($posts)-1)) && (sizeof($posts) % 6) != 0) {
                echo '</div>';
            }
        }
    ?>
</section>

<script>
    function changeRes(id) {
        $.ajax({
            type: "POST",
            url: 'ajax/changeRestriction.php',
            data:{id: id},
        });
    }

    function deletePost(id) {
        $.ajax({
            type: "POST",
            url: 'ajax/deletePost.php',
            data:{id: id},
        }).then(
            function() {
                location.reload();
            }, function() {
            }
        );

    }
</script>
