<?php
$db = new DB();
$users = $db->getUserList();
?>

<section>
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
        }
        ?>
        </tbody>
    </table>
</section>
