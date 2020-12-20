<?php
$db = new DB();
$users = $db->getUserList();
?>

<section>
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Address</th>
            <th scope="col">PLZ</th>
            <th scope="col">City</th>
            <th scope="col">Username</th>
            <th scope="col">Password</th>
            <th scope="col">Email</th>
            <th scope="col">Edit?</th>
            <th scope="col">Delete?</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($users as $user) {
            echo '<tr>';
            echo '<th scope = "row" >' . $user->getId() . '</th>';
            echo '<td>' . $user->getTitle() . '</td>';
            echo '<td>' . $user->getFname() . '</td>';
            echo '<td>' . $user->getLname() . '</td>';
            echo '<td>' . $user->getAddress() . '</td>';
            echo '<td>' . $user->getPlz() . '</td>';
            echo '<td>' . $user->getCity() . '</td>';
            echo '<td>' . $user->getUsername() . '</td>';
            echo '<td>[encrypted]</td>';
            echo '<td>' . $user->getEmail() . '</td>';
            echo '<td><a role="button" class="btn btn-warning" href="index.php?section=register&type=edit&id=' . $user->getUsername() . '">Edit.</a></td>';
            echo '<td><a role="button" class="btn btn-danger" href="inc/backend.php?type=delete&id=' . $user->getId() . '">Delete.</a></td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</section>
