<?php

$db = new DB();
$fill = false;
$user = null;
echo '<script>
    $(document).ready(function () {
        var x = document.getElementsByTagName("TITLE")[0];
        x.innerHTML = "Register user";
    });

</script>';

if (isset($_POST["pw"]) && ($_POST["pw"] != $_POST["pwRepeat"])) {
    echo MsgFactory::getWarning("Please make sure your password matches!");
} else if ((isset($_POST["type"]) && $_POST["type"] == "update") || //Update is performed
    (isset($_POST["pw"]) && ($_POST["pw"] == $_POST["pwRepeat"]))) { //Password-Update or insert is performed
    echo '<form id="myForm" action="inc/backend.php" 
    method="post" enctype="multipart/form-data">';
    foreach ($_POST as $a => $b) {
        echo '<input type="hidden" name="' . $a . '" value="' . $b . '">';
    }
    echo '</form>';
    echo '<script type="text/javascript">document.getElementById("myForm").submit();</script>';
}

if (isset($_REQUEST["edit"]) && ($_REQUEST["edit"] == "true")) {
    $user = $db->getUser($_SESSION["username"]);
    $fill = true;
    echo '<script>
            $(document).ready(function () {
                var x = document.getElementsByTagName("TITLE")[0];
                x.innerHTML = "Edit user";
            });
            </script>';
} elseif (isset($_REQUEST["pw"])) {
    $user = new User(null, $_REQUEST["title"], $_REQUEST["fname"], $_REQUEST["lname"], $_REQUEST["email"],
        $_REQUEST["username"], " ", 0, 1, " ");
    $fill = true;
}

?>
<section class="container">
    <?php
    if (isset($_REQUEST["edit"])) {
        echo '
        <h1>Upload a profile image</h1>
        <hr>
        <form method="POST" action="inc/backend.php" enctype="multipart/form-data">
        <input type="hidden" name="type" value="uploadIcon">
            <div class="row">
                <div class="form-group col">
                    <label for="picture">Profile image</label><br><br>
                    <input type="file" id="picture" name="picture" required>
                </div>
                <div class="form-group col">
                    <label for="previewImg">Preview</label><br><br>
                    <img id="previewImg" src="' . $user->getPicture() . '" alt="Placeholder" width="150px"  
                    height="150px">
                </div>
            </div>
            <button type="submit" class="btn btn-success submit" name="upload">Submit</button>
        </form>
        <hr>
        ';
    }
    ?>
    <h1><?php
        if (isset($_REQUEST["edit"])) {
            echo 'Edit User Data';
        } else {
            echo 'User-Registration';
        } ?></h1>
    <hr>
    <form method="POST" action="index.php?section=register" enctype="multipart/form-data" class="was-validated">
        <input type="hidden" name="type" value="<?php
        if (isset($_REQUEST["edit"])) {
            echo 'update';
        } else {
            echo 'insert';
        } ?>">
        <input type="hidden" name="id" <?php echo 'value="' . (($fill) ? $user->getId() : '') . '"'; ?>>
        <label for="title">Title</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="title" id="exampleRadios1" value="Mr."
                <?php echo(($fill && ($user->getTitle() == "Mr.")) ? ' checked' : ''); ?>>
            <label class="form-check-label" for="exampleRadios1">
                Mr.
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="title" id="exampleRadios2" value="Mrs."
                <?php echo(($fill && ($user->getTitle() == "Mrs.")) ? ' checked' : ''); ?>>
            <label class="form-check-label" for="female">
                Mrs.
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="title" id="exampleRadios3" value="kA"
                <?php echo(($fill && ($user->getTitle() == "kA")) ? ' checked' : '');
                echo((!$fill) ? ' checked' : ''); ?>>
            <label class="form-check-label" for="exampleRadios3">
                without title
            </label>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="fname">First name</label>
                <input type="text" class="form-control" id="fname" name="fname"
                       pattern="([A-Z][a-z]+)((\s|-)[A-Z][a-z]+)?" required
                       placeholder="First Name" <?php echo 'value="' . (($fill) ? $user->getFname() : '') . '"'; ?>>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
            <div class="form-group col">
                <label for="lname">Last name</label>
                <input type="text" class="form-control" id="lname" name="lname"
                       pattern="([A-Z][a-z]+)((\s|-)[A-Z][a-z]+)?" required
                       placeholder="Last Name" <?php echo 'value="' . (($fill) ? $user->getLname() : '') . '"'; ?>>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                       pattern="\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*" required
                    <?php echo 'value="' . (($fill) ? $user->getEmail() : '') . '"'; ?>>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please fill out this field correctly.</div>
            </div>
            <div class="form-group col">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       pattern="([A-Z]|[a-z]|(ö|ß|ä|ü)|[0-9]){1,32}" placeholder="Username"
                       aria-describedby="usernameHelp"
                       required <?php echo 'value="' . (($fill) ? $user->getUsername() : '') . '"'; ?>>
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
        if (!isset($_REQUEST["edit"])) {
            echo $passwordInput;
        }
        ?>

        <button type="submit" class="btn btn-success submit">Submit</button>
    </form>
    <?php
    if (isset($_REQUEST["edit"])) {
        echo '<hr><form id="myForm" action="index.php?section=register&edit=true" method="post" enctype="multipart/form-data">
                            <h1>Change password</h1>
                            <hr>
                            <input type="hidden" name="type" value="changePassword">
                            <input type="hidden" name="username" value="' . $user->getUsername() . '">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="oldPw">Old Password</label>
                                    <input type="password" class="form-control" id="oldPw" placeholder="Old Password" name="oldPw"
                                           pattern="([A-Z]|[a-z]|(ö|ß|ä|ü)|[1-9]){1,32}" required>
                                </div>
                                <div class="col"></div>
                            </div>';
        echo $passwordInput;
        echo '<button type="submit" class="btn btn-success submit">Submit</button> 
                          </form>';
    }
    ?>
</section>

<script>
    $('#pw, #pwRepeat').on('keyup', function () {
        if ($('#pw').val() === $('#pwRepeat').val()) {
            $('#message').html('Matching').css('color', '#28a745');
            $('.submit').disabled = false;

        } else
            $('#message').html('Not Matching').css('color', '#dc3545');
        $('.submit').disabled = false;
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }

    $("#picture").change(function() {
        readURL(this);
    });
</script>