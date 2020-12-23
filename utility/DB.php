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
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function getUserList(): array
    {
        $users = array();
        $sql = "SELECT * FROM `user`";

        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            // output data of each row
            $users = $result->fetchAll(PDO::FETCH_CLASS, 'User');
        }

        return $users;
    }

    public function getUser(string $username): ?User
    {
        $stmt = $this->conn->prepare("SELECT * FROM `user` WHERE username = ?");
        if ($stmt->execute([$username])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User($row["id"], $row["title"], $row["fname"], $row["lname"], $row["username"], $row["password"],
                $row["email"], $row["admin"], $row["activated"]);
        }
        return null;
    }

    public function registerUser(User $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO `user` (title, fname, lname, username, password, email, 
                                        `admin`, `activated`) VALUES (?, ?, ?, ?, ?, ?, '0', '1')");
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
        }catch (PDOException $e) {
            $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
            if (strpos($e->getMessage(), $existingkey) !== FALSE) {
                return false;
            } else {
                throw $e;
            }
        }
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
        }else{
            return true;
        }
    }

    public function updatePassword(User $user) : bool {
        $stmt = $this->conn->prepare("UPDATE `user` SET password=? WHERE id=?");
        $pw = $user->getPassword();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $id = $user->getId();
        if (!$stmt->execute([$hash, $id])) {
            return false;
        }else{
            return true;
        }
    }

    public function deleteUser(int $id) : void
    {
        $stmt = $this->conn->prepare("DELETE FROM `user` WHERE id=?");
        $stmt->execute([$id]);
    }

    public function loginUser(string $username, string $pw) : bool
    {
        $stmt = $this->conn->prepare("SELECT password FROM `user` WHERE username = :username");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump(password_verify($pw, $row["password"]));
        if (password_verify($pw, $row["password"])){
            return true;
        }else{
            return false;
        }
    }

}