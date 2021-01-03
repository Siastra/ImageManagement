<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/ImageManagement/model/User.php';

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
public function checkTag($tag){
    $sql = $this->conn->prepare("SELECT `name` FROM `tag` WHERE `name`  = ?");
    $sql->execute([$tag]);
    $result=$sql->fetch();
    if($result==FALSE){
        $sql2 = $this->conn->prepare("INSERT INTO `tag`(`name`) VALUES (?)");
        $sql2->execute([$tag]);
    }
     return $result;
}
public function setTag($result,$tag){

    $sql2 = $this->conn->prepare("INSERT INTO `is_assigned`(`post_id`,`tag_name`) VALUES (?,?)");
    if($sql2->execute([$result,$tag])){
        return true;
    }
    else{
        return false;
    }




}
   
  public function bringUserId(): array{
      $name = $_SESSION["username"];
      $result = array();
    $sql = $this->conn->prepare("SELECT `id` FROM `user` WHERE `username`  = ?");
    $sql->execute([$name]);
    $result=$sql->fetchAll();
     return $result;

  }
  public function bringPostId($path): array{
    $result = array();
    $sql = $this->conn->prepare("SELECT `id` FROM `post` WHERE `path`  = ?");
    $sql->execute([$path]);
    $result=$sql->fetchAll();
    print_r($result);
    return $result;
  }
  
    public function createPost($path,$restricted){
        $name=$_SESSION["username"];
        
        $sql = $this->conn->prepare("INSERT INTO `post`(`id`, `path`, `restricted`, `user_id`)
         VALUES (?,?,?,?)");
        $id= $this->bringUserId();
        foreach($id as $cont){
            
            if($sql->execute([NULL,$path,$restricted,$cont['id']])){  
                $result = $this->bringPostId($path);
                foreach($result as $answer){
                    return $answer['id'];

                }
            }else{
                return false;
            }
        }
    }
    public function showDashboardall(): array{
        $dashall = array();
        $sql = "SELECT `path` FROM `post`";

        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            $dashall = $result->fetchAll();
        }

        return $dashall;
    }
    public function showDashboardself(): array{
        $dashself = array();
        $name=  $_SESSION["username"];
        $id=$this->bringuserId();  
        foreach($id as $way){
            $endid = $way['id'];
        }
        $sql = "SELECT `path` FROM `post` WHERE `user_id`=$endid";
        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            $dashself= $result->fetchAll();
        }

        return $dashself;
    }
    public function showDashboardpublic(): array{
        $dashpub = array();
        $sql = "SELECT `path` FROM `post` WHERE `restricted`=0";

        $result = $this->conn->query($sql);
        if ($result->rowCount() > 0) {
            $dashpub= $result->fetchAll();
        }

        return $dashpub;
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