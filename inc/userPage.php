<?php
    $db = new DB();
    $user = $db->getUser($_SESSION["username"]);
    $posts = $db->getPostsByUserID($user->getId());
?>

<section class="container-fluid">
    <div class="row user-header">
        <div class="col-3">
            <img src="<?php echo $user->getPicture(); ?>" alt="Profile Picture" class="img-fluid user-picture">
        </div>
        <div class="col-9">
            <?php
                echo '<h3>' . $user->getUsername() . '</h3>';
                echo '<p>' . $user->getFname() . ' ' . $user->getLname() . '</p>';
                echo '<p>' . $user->getEmail() . '</p>';
                echo '<p>' . sizeof($posts) . ' Posts</p>';
            ?>
        </div>
    </div>

    <?php

        for ($i = 0; $i < sizeof($posts); $i++) {
            if (($i % 6) == 0) {
                echo '<div class="row galleryRow row-cols-auto">';
            }

            echo '<a href="' . $posts[$i]->getPath(). '" data-lightbox="' . $posts[$i]->getName() . '" 
            data-title="' . $posts[$i]->getName() . '"><img class="col" alt="' . $posts[$i]->getName() .
                '" src="' . $posts[$i]->getThumbnailPath() . '"></a>';
            if (($i % 6) == 3) {
                echo '</div>';
            }
        }
    ?>
</section>
