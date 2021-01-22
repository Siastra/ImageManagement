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
        $dsn = "mysql:host=localhost;dbname=imagemanagement;charset=$this->charset";
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
            if (strpos($e->getMessage(), $existingkey) !== FALSE) {
                return false;
            } else {
                throw $e;
            }
        }
    }

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

    public function getPostId(string $path): int
    {
        $sql = $this->conn->prepare("SELECT `id` FROM `post` WHERE `path`  = ?");
        $sql->execute([$path]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["id"];
    }

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

    public function showDashboardPublic(): array
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

    public function showDashboardPrivate(): array
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

    public function deleteUser(int $id): void
    {
        $stmt = $this->conn->prepare("DELETE FROM `user` WHERE id=?");
        $stmt->execute([$id]);
    }

    public function loginUser(string $username, string $pw): int
    {
        $stmt = $this->conn->prepare("SELECT password, activated FROM `user` WHERE username = :username");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return -2;
        } else {
            $user = $this->getUser($username);
            if ($user->isAdmin()) {
                if ($pw == $row["password"]) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($row["activated"] == 0) {
                    return -1;
                }
                if (password_verify($pw, $row["password"])) {
                    return true;
                } else {
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

    public function addRating(int $post_id, int $type): void
    {
        $id = $this->getUser($_SESSION["username"])->getId();
        $stmt = $this->conn->prepare("SELECT * FROM `rating` WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$id, $post_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $delete = $this->conn->prepare("DELETE FROM `rating` WHERE user_id = ? AND post_id = ?");
            $delete->execute([$id, $post_id]);
        }
        $sql = $this->conn->prepare("INSERT INTO `rating`(`user_id`, `post_id`, `type`) VALUES (?,?,?)");
        $sql->execute([$id, $post_id, $type]);
    }
}