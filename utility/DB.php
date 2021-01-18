<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/ImageManagement/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/ImageManagement/utility/Upload.php';

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

        $this->config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/ImageManagement/config/config.json"),
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

    public function getUser(string $username): ?User
    {
        $stmt = $this->conn->prepare("SELECT * FROM `user` WHERE username = ?");
        if ($stmt->execute([$username])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User($row["id"], $row["title"], $row["fname"], $row["lname"], $row["email"], $row["username"],
                $row["password"], $row["admin"], $row["activated"], $row["picture"]);
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

    public function getPicCreaterId($path)   {

        $sql=$this->conn->prepare("SELECT `user_id` FROM `post` WHERE `path`=?");
        $sql->execute([$path]);
        $id=$sql->fetch();
        return $id;
    }

    public function getPostPic($picuserid){
        $sql =$this->conn->prepare("SELECT `picture` from `user` WHERE `id` = ?");
        $sql->execute([$picuserid["user_id"]]);
        $profilpic = $sql->fetch();
        return $profilpic["picture"];
    }
    public function getPostCreater($picuserid){
        

        $sql=$this->conn->prepare("SELECT `username` FROM `user` WHERE `id` = ?");
        $sql->execute([$picuserid["user_id"]]);
        $creatername=$sql->fetch();


        return $creatername["username"];
    }
    public function checkTag($tag)
    {
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

    public function getPostId(string $path): int
    {
        $sql = $this->conn->prepare("SELECT `id` FROM `post` WHERE `path`  = ?");
        $sql->execute([$path]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["id"];
    }

    public function createPost(string $path, $restricted): int
    {
        $sql = $this->conn->prepare("INSERT INTO `post`(`id`, `path`, `restricted`, `user_id`, `createdAt`)
         VALUES (?,?,?,?, LOCALTIMESTAMP())");
        $id = $this->getUser($_SESSION["username"])->getId();

        try {
            $sql->execute([NULL, $path, $restricted, $id]);
            return $this->getPostId($path);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function showDashboardAll(): array
    {
        $dashAll = array();
        $sql = "SELECT `path` FROM `post`";

        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            $dashAll = $result->fetchAll();
        }

        return $dashAll;
    }

    public function showDashboardSelf(): array
    {
        $dashSelf = array();
        $u_id = $this->getUser($_SESSION["username"])->getId();
        $sql = "SELECT `path` FROM `post` WHERE `user_id`=$u_id";
        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            $dashSelf = $result->fetchAll();
        }

        return $dashSelf;
    }

    public function showDashboardPublic(): array
    {
        $dashPub = array();
        $sql = "SELECT `path` FROM `post` WHERE `restricted`=0";

        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            $dashPub = $result->fetchAll();
        }

        return $dashPub;
    }

    public function updateUser(User $user): bool
    {
        $stmt = $this->conn->prepare("UPDATE `user` SET title=?, fname=?, lname=?, username=?, 
                                                email=? WHERE id=?");
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

    public function showRatings($path, $type): int
    {
        $postid = $this->getPostId($path);
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM `rating` WHERE post_id = ? AND type = ?");
        $stmt->execute([$postid, $type]);
        return $stmt->fetchColumn();
    }

    public function addRating($path, $type){
        $id = $this->getUser($_SESSION["username"])->getId();
        $postid = $this->getPostId($path);
        $stmt = $this->conn->prepare("SELECT * FROM `rating` WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$id, $postid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            $delete = $this->conn->prepare("DELETE FROM `rating` WHERE user_id = ? AND post_id = ?");
            $delete->execute([$id,$postid]);
        }
        $sql = $this->conn->prepare("INSERT INTO `rating`(`user_id`, `post_id`, `type`)
         VALUES (?,?,?)");
        $sql->execute([$id, $postid, $type]);
    }
}