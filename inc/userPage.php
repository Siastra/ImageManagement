<?php
    $db = new DB();
    $user = $db->getUser($_SESSION["username"]);
    $posts = $db->showDashboardSelf();
?>

<section class="container-fluid">
    <div class="row user-header">
        <div class="col-md-3">
            <img src="<?php echo $user->getPicture(); ?>" alt="Profile Picture" class="img-fluid user-picture">
        </div>
        <div class="col-md-9">
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
            $temp = explode("/", $posts[$i]["path"]);
            $name = explode(".", $temp[sizeof($temp)-1])[0];
            $path = "pictures/thumbnail/" . $temp[sizeof($temp)-1];
            if (($i % 6) == 0) {
                echo '<div class="row galleryRow">';
            }

            echo '<a class="img-fluid col-md-2" href="' . $posts[$i]["path"] . '" data-lightbox="' . $name . '" 
            data-title="' . $name . '"><img alt="' . $name . '" src="' . $path . '"></a>';
            if (($i % 6) == 3) {
                echo '</div>';
            }
        }
    ?>
</section>
