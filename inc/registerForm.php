<?php

$db = new DB();
$fill = false;
$user = null;
if (isset($_REQUEST["edit"]) && ($_REQUEST["edit"] == "true")) {
    $user = $db->getUser($_REQUEST["id"]);
    $fill = true;
}

if(isset($_POST["pw"]) && ($_POST["pw"] != $_POST["pwRepeat"])) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"> Please make sure your password matches! 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>';
    $user = new User(null,$_REQUEST["title"], $_REQUEST["fname"], $_REQUEST["lname"], $_REQUEST["username"], " ",
        $_REQUEST["email"]);
    $fill = true;
} else if (isset($_REQUEST["username"])){
    echo '<form id="myForm" action="inc/backend.php" method="post" enctype="multipart/form-data">';
    foreach ($_POST as $a => $b) {
        echo '<input type="hidden" name="' . $a . '" value="' . $b . '">';
    }
    echo '</form>';
    echo '<script type="text/javascript">document.getElementById("myForm").submit();</script>';
}

?>

<section class="container">
    <h1><?php if (isset($_REQUEST["edit"])) { echo 'Edit user'; } else{ echo 'User-Registration';}?></h1>
    <hr>
    <form method="POST" action="index.php?section=register" enctype="multipart/form-data" class="was-validated">
        <input type="hidden" name="type" value="<?php if (isset($_REQUEST["edit"])) { echo 'update'; }
        else { echo 'insert'; }?>">
        <input type="hidden" name="id" <?php echo 'value="' . (($fill) ?  $user->getId() :  '') . '"';?>>
        <label for="title">Title</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="title" id="exampleRadios1" value="Mr."
                <?php echo (($fill && ($user->getTitle() == "Mr.")) ? ' checked' :  '');?>>
            <label class="form-check-label" for="exampleRadios1">
                Mr.
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="title" id="exampleRadios2" value="Mrs."
                <?php echo (($fill && ($user->getTitle() == "Mrs.")) ? ' checked' :  '');?>>
            <label class="form-check-label" for="female">
                Mrs.
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="title" id="exampleRadios3" value="kA"
                <?php echo (($fill && ($user->getTitle() == "kA")) ? ' checked' :  '');
                      echo ((!$fill) ? ' checked' :  '');?>>
            <label class="form-check-label" for="exampleRadios3">
                without title
            </label>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="fname">First name</label>
                <input type="text" class="form-control" id="fname" name="fname"
                       pattern="([A-Z][a-z]+)((\s|-)[A-Z][a-z]+)?" required
                       placeholder="First Name" <?php echo 'value="' . (($fill) ?  $user->getFname() :  '') . '"';?>>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
            <div class="form-group col">
                <label for="lname">Last name</label>
                <input type="text" class="form-control" id="lname" name="lname"
                       pattern="([A-Z][a-z]+)((\s|-)[A-Z][a-z]+)?" required
                       placeholder="Last Name" <?php echo 'value="' . (($fill) ?  $user->getLname() :  '') . '"';?>>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                       pattern="([A-Z]|[a-z]|(ö|ß|ä|ü)|[0-9]|(\.|-|\_))+@[a-z]+(.[a-z]+)+" required
                       <?php echo 'value="' . (($fill) ?  $user->getEmail() :  '') . '"';?>>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
            <div class="form-group col">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       pattern="([A-Z]|[a-z]|(ö|ß|ä|ü)|[0-9]){1,32}" placeholder="Username"
                       aria-describedby="usernameHelp"
                       required <?php echo 'value="' . (($fill) ?  $user->getUsername() :  '') . '"';?>>
                <small id="usernameHelp" class="form-text text-muted">Make sure your username only contains letters and
                    digits and is not longer than 16 characters ;)</small>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
        </div>
            <?php
            $passwordInput = '<div class="row">
                            <div class="form-group col">
                                <label for="pw">Password</label>
                                <input type="password" class="form-control" id="pw" placeholder="Password" name="pw"
                                       pattern="([A-Z]|[a-z]|(ö|ß|ä|ü)|[1-9]){1,32}" aria-describedby="pwHelp" required>
                                <small id="pwHelp" class="form-text text-muted">Make sure your password only contains letters and digits
                                    ;)</small>
                                <div class="valid-feedback">Valid.</div>
                                <div class="invalid-feedback">Please fill out this field correctly.</div>
                            </div>
                            <div class="form-group col">
                                <label for="pwRepeat">Repeat password</label>
                                <input type="password" class="form-control" id="pwRepeat" name="pwRepeat"
                                       pattern="([A-Z]|[a-z]|(ö|ß|ä|ü)|[1-9]){1,32}" placeholder="Password repeat" required>
                                <span id="message" style="font-size: 80%;"></span>
                                <div class="valid-feedback">Valid.</div>
                                <div class="invalid-feedback">Please fill out this field correctly.</div>
                            </div>
                        </div>';
                if(!isset($_REQUEST["edit"])) {
                    echo $passwordInput;
                }
                ?>

        <button type="submit" class="btn btn-success" id="submit">Submit</button>
    </form>
    <?php
        if (isset($_REQUEST["edit"])) {
                    echo '<hr><form id="myForm" action="inc/backend.php" method="post" enctype="multipart/form-data">
                            <h1>Change password</h1>
                            <input type="hidden" name="type" value="changePassword">
                            <input type="hidden" name="username" value="' . $user->getUsername() . '">';
                    echo $passwordInput;
                    echo '<button type="submit" class="btn btn-success" id="submit">Submit</button> 
                          </form>';
                }
    ?>
</section>

<script>
    $('#pw, #pwRepeat').on('keyup', function () {
        if ($('#pw').val() == $('#pwRepeat').val()) {
            $('#message').html('Matching').css('color', '#28a745');
            $('#submit').disabled = false;

        } else
            $('#message').html('Not Matching').css('color', '#dc3545');
        $('#submit').disabled = false;
    });
</script>