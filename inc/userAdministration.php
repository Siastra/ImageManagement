<?php
$db = new DB();
$users = $db->getUserList();
?>

<section>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Status</th>
                <th scope="col">Reset Password?</th>
                <th scope="col">Activate/Deactivate?</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($users as $user) {
                echo '<tr>';
                echo '<th scope = "row" >' . $user->getId() . '</th>';
                echo '<td>' . $user->getUsername() . '</td>';
                echo '<td>' . $user->getEmail() . '</td>';

                if ($user->getActivated()) {
                    echo '<td>Active</td>';
                } else {
                    echo '<td>Deactivated</td>';
                }

                if ($user->isAdmin()) {
                    echo '<td><b>Pw:</b> ' . $user->getPassword() . '</td>';
                } else {
                    echo '<td><a role="button" class="btn btn-warning" href="inc/backend.php?type=forgotPassword&username=' . $user->getUsername() . '">Reset pw.</a></td>';
                }
                if ($user->isAdmin()) {
                    echo '<td>Cannot deactivate Admin-accounts!</td>';
                } else {
                    echo '<td><a role="button" class="btn btn-danger" href="inc/backend.php?type=changeStatus&username=' . $user->getUsername() . '">Change status.</a></td>';
                }
                echo '</tr>';
                $posts = $db->getPostsByUserID($user->getId());

                echo '<tr>
                          <td colspan="6">
                            <table class="table mb-0 table-success table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Post</th>
                                    <th scope="col">Restriction</th>
                                    <th scope="col">Upload date</th>
                                    <th scope="col">Delete post?</th>
                                </tr>
                                </thead>
                                <tbody>';

                if (empty($posts)) {
                    echo '<tr><td>No posts uploaded.</td></tr>';
                }else {
                    foreach ($posts as $post) {
                        echo '<tr>
                            <td><a href="index.php?section=dash#post' . $post->getId() . '">' . $post->getName() .
                            '</a></td>
                            <td>' . (($post->getRestricted()) ? 'restricted' : 'public') . '</td>
                            <td>' . $post->getDate() . '</td>
                            <td><button class="btn btn-danger" onclick="deletePost(' . $post->getId() . ')">Delete post.</button></td>
                          </tr>';
                    }
                }
                echo '</tbody></table>
                          </td>
                        </tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    function deletePost(id) {
        $.ajax({
            type: "POST",
            url: 'ajax/deletePost.php',
            data:{id: id},
        });
        location.reload();
    }
</script>
