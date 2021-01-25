<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Post.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Comment.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Upload.php';

class DB
{

    private string $charset = 'utf8mb4';
    private array $config;
    private PDO $conn;
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public function __construct()
    {

        $this->config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/config/config.json"),
            true);
        $username = $this->config["db"]["user"];
        $password = $this->config["db"]["password"];
        $db_name = $this->config["db"]["db_name"];
        $dsn = "mysql:host=localhost;dbname=$db_name;charset=$this->charset";
        try {

            $this->conn = new PDO($dsn, $username, $password, $this->options);
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    //Get a list of all users
    public function getUserList(): array
    {
        $result = array();
        $sth = $this->conn->prepare("SELECT * FROM `user`");
        $sth->execute();
        if ($sth->rowCount() > 0) {
            // output data of each row
            try {
                $users = $sth->fetchAll(PDO::FETCH_ASSOC);
                foreach ($users as $user) {
                    array_push($result, new User($user["id"], $user["title"], $user["fname"], $user["lname"],
                        $user["email"], $user["username"], $user["password"], $user["admin"], $user["activated"],
                        $user["picture"]));
                }
            } catch (Exception $e) {
                echo 'Exception abgefangen: ', $e->getMessage(), "\n";
            }
        }
        return $result;
    }

    //Returns an array of all comments related to the post
    public function getAllCommentsByPost(int $post_id): array
    {
        $result = array();
        $sth = $this->conn->prepare("SELECT * FROM `comment` WHERE post_id=?");
        $sth->execute([$post_id]);
        if ($sth->rowCount() > 0) {
            // output data of each row
            try {
                $comments = $sth->fetchAll(PDO::FETCH_ASSOC);
                foreach ($comments as $comment) {
                    array_push($result, new Comment($comment["post_id"], $comment["user_id"], $comment["text"],
                        new DateTime($comment["createdAt"])));
                }
            } catch (Exception $e) {
                echo 'Exception abgefangen: ', $e->getMessage(), "\n";
            }
        }
        return $result;
    }

    //Get a specific user by username
    public function getUser(string $username): ?User
    {
        $stmt = $this->conn->prepare("SELECT * FROM `user` WHERE username = ?");
        if ($stmt->execute([$username])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($row)) {
                return null;
            } else {
                return new User($row["id"], $row["title"], $row["fname"], $row["lname"], $row["email"], $row["username"],
                    $row["password"], $row["admin"], $row["activated"], $row["picture"]);
            }
        }
        return null;
    }

    //Get a specific user by id.
    public function getUserByID(int $id): ?User
    {
        $stmt = $this->conn->prepare("SELECT * FROM `user` WHERE id = ?");
        if ($stmt->execute([$id])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User($row["id"], $row["title"], $row["fname"], $row["lname"], $row["email"], $row["username"],
                $row["password"], $row["admin"], $row["activated"], $row["picture"]);
        }
        return null;
    }

    //Get a specific post by id.
    public function getPostById(int $id): ?Post
    {
        $stmt = $this->conn->prepare("SELECT * FROM `post` WHERE id = ?");
        if ($stmt->execute([$id])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new Post($row["id"], $row["title"], $row["path"], $row["restricted"], $row["user_id"],
                $row["createdAt"], $row["text"]);
        }
        return null;
    }

    //Inserts a user into the user table and returns false if unique constraint fails.
    public function registerUser(User $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO `user` (title, fname, lname, username, password, email, 
                                        `admin`, `activated`, picture) 
                                        VALUES (?, ?, ?, ?, ?, ?, 0, 1, 'res/images/user.svg')");
        $title = $user->getTitle();
        $fname = $user->getFname();
        $lname = $user->getLname();
        $username = $user->getUsername();
        $pw = $user->getPassword();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $email = $user->getEmail();
        try {
            $stmt->execute([$title, $fname, $lname, $username, $hash, $email]);
            return true;
        } catch (PDOException $e) {
            $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
            if (strpos($e->getMessage(), $existingkey) !== FALSE) { // duplicate username
                return false;
            } else {
                throw $e;
            }
        }
    }

    //Activates/deactivates a user account.
    public function changeStatus(string $username): bool
    {
        $value = !($this->getUser($username)->getActivated());
        $stmt = $this->conn->prepare("UPDATE `user` SET activated=? WHERE username=?");

        if (!$stmt->execute([$value, $username])) {
            return false;
        } else {
            return true;
        }
    }

    //Uploads a profile image and deletes an existing one in the process.
    public function uploadIcon(array $files): bool
    {
        if (isset($_POST["upload"])) {
            $stmt = $this->conn->prepare("UPDATE `user` SET picture=? WHERE username=?");
            $pathThumb = 'pictures/thumbnail/' . $_SESSION["username"] . ".";
            $pathFull = 'pictures/full/' . $_SESSION["username"] . ".";
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $pathThumb . 'jpg')) {
                unlink($_SERVER["DOCUMENT_ROOT"] . '/' . $pathThumb . 'jpg');
                unlink($_SERVER["DOCUMENT_ROOT"] . '/' . $pathFull . 'jpg');
            } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $pathThumb . 'png')) {
                unlink($_SERVER["DOCUMENT_ROOT"] . '/' . $pathThumb . 'png');
                unlink($_SERVER["DOCUMENT_ROOT"] . '/' . $pathFull . 'png');
            }
            Upload::uploadProfilePicture($files);
            if (!$stmt->execute([$pathThumb . pathinfo($files['picture']['name'],
                    PATHINFO_EXTENSION), $_SESSION["username"]])) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //Checks the tags that the user has assigned to his post and if the tag doesnt already exists, it gets created
    public function checkTag($tag)
    {
        $result = array();
        $size = count($tag);
        $sql = $this->conn->prepare("SELECT `name` FROM `tag` WHERE `name`  = ?");
        for ($i = 0; $i < $size; $i++) {
            if ($tag[$i] != NULL) {
                $sql->execute([$tag[$i]]);
                $result = $sql->fetch();
                if ($result == FALSE) {
                    $sql2 = $this->conn->prepare("INSERT INTO `tag`(`name`) VALUES (?)");
                    $sql2->execute([$tag[$i]]);
                }
            }

        }

        return $result;
    }

    //Connects the given tags with the uploaded picture
    public function setTag(int $p_id, $tag): bool
    {
        $size = count($tag);
        $sql2 = $this->conn->prepare("INSERT INTO `is_assigned`(`post_id`,`tag_name`) VALUES (?,?)");
        for ($i = 0; $i < $size; $i++) {
            if ($tag[$i] != NULL) {
                $sql2->execute([$p_id, $tag[$i]]);
            }
        }
        return true;
    }

    public function readTags($p_id): array
    {
        $sql = $this->conn->prepare("SELECT `tag_name` FROM `is_assigned` WHERE `post_id` = ?");
        $sql->execute([$p_id]);
        return $sql->fetchAll();
    }

    //Returns the next auto_increment value for post_id in the DB.
    public function getNextPostId(): int
    {
        $sql = $this->conn->prepare("SELECT AUTO_INCREMENT FROM information_schema.TABLES
                                               WHERE TABLE_SCHEMA = \"imagemanagement\" 
                                               AND TABLE_NAME = \"post\"");
        $sql->execute();
        $res = $sql->fetchColumn();
        if (empty($res)) {
            return 1;
        } else {
            return $res;
        }
    }

    //Creates a post
    public function createPost(string $title, string $path, int $restricted, string $text): int
    {
        $sql = $this->conn->prepare("INSERT INTO `post`(`id`, `title`, `path`, `restricted`, `user_id`, 
                                                `createdAt`,`text`) VALUES (?,?,?,?,?, LOCALTIMESTAMP(),?)");
        $id = $this->getUser($_SESSION["username"])->getId();

        try {
            $sql->execute([NULL, $title, $path, $restricted, $id, $text]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //Posts a comment assigned to a specific post.
    public function postComment(int $post_id, string $comment, DateTime $date): ?Comment
    {
        $sql = $this->conn->prepare("INSERT INTO `comment`(`post_id`, `user_id`, `text`, `createdAt`) 
                                            VALUES (?,?,?,?)");
        $user_id = $this->getUser($_SESSION["username"])->getId();

        try {
            $sql->execute([$post_id, $user_id, $comment, (date_format($date, 'Y-m-d H:i:s'))]);
            return new Comment($post_id, $user_id, $comment, $date);
        } catch (PDOException $e) {
            return null;
        }
    }

    //Deletes a post and all things related to it(comments, ratings, assignment of tags)
    public function deletePostById(int $id): void
    {
        $post = $this->getPostById($id);
        unlink($_SERVER["DOCUMENT_ROOT"] . "/" . $post->getDashPath());
        unlink($_SERVER["DOCUMENT_ROOT"] . "/" . $post->getThumbnailPath());
        unlink($_SERVER["DOCUMENT_ROOT"] . "/" . $post->getFullPath());

        $sql = $this->conn->prepare("DELETE FROM `rating` WHERE `post_id`=?");
        $sql->execute([$id]);
        $sql = $this->conn->prepare("DELETE FROM `comment` WHERE `post_id`=?");
        $sql->execute([$id]);
        $sql = $this->conn->prepare("DELETE FROM `is_assigned` WHERE `post_id`=?");
        $sql->execute([$id]);
        $sql = $this->conn->prepare("DELETE FROM `post` WHERE `id`=?");
        $sql->execute([$id]);
    }

    //Gives back an array of all posts
    public function showDashboardAll(): array
    {
        $result = array();
        $sql = $this->conn->prepare("SELECT * FROM `post`");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $post) {
                array_push($result, new Post($post["id"], $post["title"], $post["path"], $post["restricted"],
                    $post["user_id"], $post["createdAt"], $post["text"]));
            }
        }

        return $result;
    }

    //Returns all posts of a specific user.
    public function getPostsByUserID(int $id): array
    {
        $result = array();
        $sql = $this->conn->prepare("SELECT * FROM `post` WHERE `user_id`=?");
        $sql->execute([$id]);
        if ($sql->rowCount() > 0) {
            // output data of each row
            try {
                $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
                foreach ($posts as $post) {
                    array_push($result, new Post($post["id"], $post["title"], $post["path"],
                        intval($post["restricted"]), $post["user_id"], $post["createdAt"], $post["text"]));
                }
            } catch (Exception $e) {
                echo 'Exception abgefangen: ', $e->getMessage(), "\n";
            }
        }
        return $result;
    }

    //Gives back an array of all posts with no restriction
    public function showDashboardPublic(): array
    {
        $result = array();
        $sql = $this->conn->prepare("SELECT * FROM `post` WHERE `restricted`=0");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $post) {
                array_push($result, new Post($post["id"], $post["title"], $post["path"], $post["restricted"],
                    $post["user_id"], $post["createdAt"], $post["text"]));
            }
        }

        return $result;
    }

    //Changes the restriction of an image.
    public function changeRestriction(int $id): bool
    {
        $sql = $this->conn->prepare("SELECT `restricted` FROM `post` WHERE id=?");
        $sql->execute([$id]);
        if ($sql->rowCount() > 0) {
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $res = intval($row["restricted"]);
            $val = ($res ? 0 : 1);
            $stmt = $this->conn->prepare("UPDATE `post` SET `restricted`=? WHERE id=?");
            if (!$stmt->execute([$val, $id])) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //Updates user data in the DB.
    public function updateUser(User $user): bool
    {
        $stmt = $this->conn->prepare("UPDATE `user` SET title=?, fname=?, lname=?, username=?, email=? 
                                                WHERE id=?");
        $title = $user->getTitle();
        $fname = $user->getFname();
        $lname = $user->getLname();
        $username = $user->getUsername();
        $email = $user->getEmail();
        $id = $user->getId();
        if (!$stmt->execute([$title, $fname, $lname, $username, $email, $id])) {
            return false;
        } else {
            return true;
        }
    }

    //Updates password in the DB.
    public function updatePassword(User $user): bool
    {
        $stmt = $this->conn->prepare("UPDATE `user` SET password=? WHERE id=?");
        $pw = $user->getPassword();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $id = $user->getId();
        if (!$stmt->execute([$hash, $id])) {
            return false;
        } else {
            return true;
        }
    }

    //User-login is performed
    public function loginUser(string $username, string $pw): int
    {
        $stmt = $this->conn->prepare("SELECT password, activated FROM `user` WHERE username = :username");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) { //user does not exist
            return -2;
        } else {
            $user = $this->getUser($username);
            if ($user->isAdmin()) {
                if ($pw == $row["password"]) { //login successful
                    return true;
                } else { // login fails
                    return false;
                }
            } else {
                if ($row["activated"] == 0) { // user deactivated
                    return -1;
                }
                if (password_verify($pw, $row["password"])) { //login successful
                    return true;
                } else { // login fails
                    return false;
                }
            }
        }
    }

    public function showRatings(int $id, int $type): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM `rating` WHERE post_id = ? AND `type` = ?");
        $stmt->execute([$id, $type]);
        return $stmt->fetchColumn();
    }

    public function addRating(int $post_id, int $user_id, int $type): void
    {
        $stmt = $this->conn->prepare("SELECT * FROM `rating` WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {//if a rating from the same user on the same post is already set delete existing rating
            $delete = $this->conn->prepare("DELETE FROM `rating` WHERE user_id = ? AND post_id = ?");
            $delete->execute([$user_id, $post_id]);
        }
        $sql = $this->conn->prepare("INSERT INTO `rating`(`user_id`, `post_id`, `type`) VALUES (?,?,?)");
        $sql->execute([$user_id, $post_id, $type]);
    }

   public function listAllTags() : array
   {
       $result = array();
       $sql = $this->conn->prepare("SELECT * FROM `tag`");
       $sql->execute();
       if ($sql->rowCount() > 0) {
           $tags = $sql->fetchAll(PDO::FETCH_ASSOC);
           foreach($tags as $tag){
               array_push($result, $tag['name']);
           }
       }
       return $result;
   }


   public function checkTags(array $posts, array $tags) : array
   {
       $result = array();
        foreach($posts as $post){
            $postId = $post->getId();
            if(count($tags) > 1){ //if 2 tags are set check if both tags
                $sql = $this->conn->prepare("SELECT post_id from is_assigned WHERE tag_name = ? AND post_id= ? intersect SELECT post_id from is_assigned WHERE tag_name = ? AND post_id = ?");
                $sql->execute([$tags[0], $postId, $tags[1], $postId]);
            }else{//if 1 tag is set check only for that tag
                $sql = $this->conn->prepare("SELECT post_id from is_assigned WHERE tag_name = ? AND post_id= ?");
                $sql->execute([$tags[0], $postId]);
            }
            if ($sql->rowCount() > 0) {
                $resultPostIds = $sql->fetchAll(PDO::FETCH_ASSOC);
                $resultPost = $this->getPostById($resultPostIds[0]['post_id']);
                if(!in_array($resultPost, $result)){
                    array_push($result, $resultPost);
                }
            }
        }
        return $result;
   }


   public function filterDate(array $posts, string $span) : array //gets date and checks each post if its date is the same
   {
       $result = array();
       foreach ($posts as $post) {
           $date = $post->getDate();
           $postdate = new DateTime($date);
           $now = new DateTime(date("d.m.Y"));
           $age = $postdate->diff($now);
           echo '<br>';
           if($span == '1d'){
               if($age->y < 1 && $age->m < 1 && $age->d < 1){
                   if(!in_array($post, $result)){
                       array_push($result, $post);
                   }
               }
           }elseif ($span == '1w'){
               if($age->y < 1 && $age->m < 1 && $age->d < 8){
                   if(!in_array($post, $result)){
                       array_push($result, $post);
                   }
               }
           }elseif($span == '1m'){
               if($age->y > 0 || $age->m > 0 || $age->d > 7) {
                   if (!in_array($post, $result)) {
                       array_push($result, $post);
                   }
               }
           }
       }
       return $result;
   }


   public function checkSearchRequest(array $posts, string $searchInput): array
   {
       $result = array();
       $search = "%".$searchInput."%";
       foreach($posts as $post){
           $postId = $post->getId();
           $stmt = $this->conn->prepare("SELECT post_id from comment where post_id = ? AND text like ? UNION SELECT id from post WHERE (id = ? AND title LIKE ? ) OR (id = ? AND text LIKE ?)");//checks comments title and text for the search term
           $stmt->execute([$postId, $search, $postId, $search, $postId, $search]);
           if ($stmt->rowCount() > 0) {
               $resultPostIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
               $resultPost = $this->getPostById($resultPostIds[0]['post_id']);
               if(!in_array($resultPost, $result)){
                   array_push($result, $resultPost);
               }
           }
       }
       return $result;
   }
    
   //Function gets array of post_ids ordered by the likes that were given to the post 
    public function getDashboardByLikes():array
    {
        $results = array();
        
            $sql = $this->conn->prepare("SELECT `post_id` ,COUNT(*) AS `rating` FROM `rating` WHERE `type`=1 GROUP BY `post_id` ORDER BY `rating` DESC");
      
            $sql->execute();
        if ($sql->rowCount() > 0) {
            $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $post) {
                array_push($results,$this->getPostById($post["post_id"]));
            }
        }

        return $results;
    }
    //Function gets array of post_ids ordered by the Dislikes that were given to the post 
    public function getDashboardByDislikes():array
    {
        $results = array();
       
            $sql = $this->conn->prepare("SELECT `post_id` ,COUNT(*) AS `rating` FROM `rating` WHERE `type`=0 GROUP BY `post_id` ORDER BY `rating` DESC");
            $sql->execute();
        if ($sql->rowCount() > 0) {
            $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $post) {
                array_push($results,$this->getPostById($post["post_id"]));
            }
        }

        return $results;
    }
    //Function gets array of post_ids ordered by the comments that were written under the post 
    public function getDashboardByComments():array
    {
        $results = array();
        $sql = $this->conn->prepare("SELECT `post_id` ,COUNT(*) AS `comments` FROM `comment` GROUP BY `post_id` ORDER BY `comments` DESC");
        $sql->execute();
       
        if ($sql->rowCount() > 0) {
            $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $post) {
                array_push($results,$this->getPostById($post["post_id"]));
            }
        }

        return $results;
    }
}