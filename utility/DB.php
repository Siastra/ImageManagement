<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Ue10_Glavanits/model/User.php';

class DB
{

    private string $username = "phpFormApplication";
    private string $password = "CuTXhYO9STA2j88w";
    private PDO $conn;

    public function __construct()
    {
        $charset = 'utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = "mysql:host=localhost;dbname=Uebung8;charset=$charset";
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function getUserList(): array
    {
        $users = array();
        $sql = "SELECT * FROM users";

        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            // output data of each row
            $users = $result->fetchAll(PDO::FETCH_CLASS, 'User');
        }

        return $users;
    }

    public function getUser(string $username): ?User
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt->execute([$username])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User($row["id"], $row["title"], $row["fname"], $row["lname"], $row["address"], $row["plz"],
                $row["city"], $row["username"], $row["password"], $row["email"]);
        }
        return null;
    }

    public function registerUser(User $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO users (title, fname, lname, address, plz, city,
                username, password, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $title = $user->getTitle();
        $fname = $user->getFname();
        $lname = $user->getLname();
        $address = $user->getAddress();
        $plz = $user->getPlz();
        $city = $user->getCity();
        $username = $user->getUsername();
        $pw = $user->getPassword();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $email = $user->getEmail();
        try {
            $stmt->execute([$title, $fname, $lname, $address, $plz, $city, $username, $hash, $email]);
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
        $stmt = $this->conn->prepare("UPDATE users SET title=?, fname=?, lname=?, address=?, plz=?, city=?, 
                                            username=?, password=?, email=? WHERE id=?");
        $title = $user->getTitle();
        $fname = $user->getFname();
        $lname = $user->getLname();
        $address = $user->getAddress();
        $plz = $user->getPlz();
        $city = $user->getCity();
        $username = $user->getUsername();
        $pw = $user->getPassword();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $email = $user->getEmail();
        $id = $user->getId();
        if (!$stmt->execute([$title, $fname, $lname, $address, $plz, $city, $username, $hash,
            $email, $id])) {
            return false;
        }else{
            return true;
        }
    }

    public function deleteUser(int $id) : void
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$id]);
    }

    public function loginUser(string $username, string $pw) : bool
    {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE username = :username");
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