<?php
$db = new DB();
if (isset($_POST["upload"])) {
    $newName = strval($db->getNextPostId());
    $restricted = ((isset($_POST["restriction"])) ? 0 : 1);
    $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
    Upload::uploadPost($_FILES, $newName);

    if (isset($_POST["tags"])) {

        $tag = $_POST["tags"];
        $divide = "/[-\s:]/";
        $tag2 = preg_split($divide, $tag);
        #
        $db->checkTag($tag2);
        $result = $db->createPost($_POST["title"], 'pictures/dashboard/' . $newName . "." . $ext, $restricted,$_POST["text"]);
        $db->setTag($result, $tag2);
    }
    header("Location: index.php?section=dash");

}
?>
    <section class="container">
        <h1>Upload your post!</h1><br><br>
        <form method="post" action="index.php?section=create" enctype="multipart/form-data">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col form-group">
                            <label for="picture">Post image</label><br><br>
                            <input type="file" id="picture" name="picture"   accept="image/x-png,image/jpeg"  required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label for="title">Title:</label>
                            <input type="text" placeholder="Title" name="title" id="title" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label for="tags">Tags:</label>
                            <input type="text" placeholder="Tags " name="tags" id="tags"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label for="tags">Text:</label>
                            <input type="text" placeholder="Text " name="text" id="text"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label for="restriction">Restriction:</label>
                            <input type="checkbox" data-toggle="toggle" data-on="Public" data-off="Restricted"
                                   data-onstyle="success" data-offstyle="danger" class="col-1" data-size="small"
                                   id="restriction" checked name="restriction">
                        </div>
                    </div>
                    <button class="btn btn-success submit" type="submit" name="upload">Upload</button>
                </div>
                <div class="form-group col">
                    <label for="previewPost">Preview</label><br><br>
                    <img id="previewPost" src="res/images/user.svg" alt="Placeholder" width="450px"
                         height="450px">
                </div>
            </div>
        </form>
    </section>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    $('#previewPost').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#picture").change(function () {
            readURL(this);
        });
    </script>