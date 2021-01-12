<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/User.php';

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
                        $user["email"], $user["username"], $user["password"], $user["admin"], $user["activated"]));
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
                $row["password"], $row["admin"], $row["activated"]);
        }
        return null;
    }

    public function registerUser(User $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO `user` (title, fname, lname, username, password, email, 
                                        `admin`, `activated`) VALUES (?, ?, ?, ?, ?, ?, 0, 1)");
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
    
    public function checkTag($tag)
    {
        $sql = $this->conn->prepare("SELECT `name` FROM `tag` WHERE `name`  = ?");
        $sql->execute([$tag]);
        $result = $sql->fetch();
        if ($result == FALSE) {
            $sql2 = $this->conn->prepare("INSERT INTO `tag`(`name`) VALUES (?)");
            $sql2->execute([$tag]);
        }
        return $result;
    }

    public function setTag(int $p_id, $tag) : bool
    {

        $sql2 = $this->conn->prepare("INSERT INTO `is_assigned`(`post_id`,`tag_name`) VALUES (?,?)");
        if ($sql2->execute([$p_id, $tag])) {
            return true;
        } else {
            return false;
        }
    }

    public function getPostId(string $path): int
    {
        $sql = $this->conn->prepare("SELECT `id` FROM `post` WHERE `path`  = ?");
        $sql->execute([$path]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["id"];
    }

    public function createPost(string $path, $restricted) : int
    {
        $sql = $this->conn->prepare("INSERT INTO `post`(`id`, `path`, `restricted`, `user_id`)
         VALUES (?,?,?,?)");

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

}