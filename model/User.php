<?php


class User
{
    private int $id, $plz;
    private string $title, $fname, $lname, $address, $city, $username, $password, $email;

    public function __construct()
    {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 9:
                self::__construct1($argv[0], $argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6], $argv[7], $argv[8]);
                break;
            case 10:
                self::__construct2($argv[0], $argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6], $argv[7], $argv[8], $argv[9]);
                break;
        }
    }



    public function __construct1($title, $fname, $lname, $address, $plz, $city, $username, $password, $email)
    {
        $this->email = $email;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->title = $title;
        $this->address = $address;
        $this->city = $city;
        $this->plz = $plz;
        $this->username = $username;
        $this->password = $password;
    }

    public function __construct2($id, $title, $fname, $lname, $address, $plz, $city, $username, $password, $email)
    {
        $this->id = $id;
        $this->email = $email;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->title = $title;
        $this->address = $address;
        $this->city = $city;
        $this->plz = $plz;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFname(): string
    {
        return $this->fname;
    }

    /**
     * @param string $fname
     */
    public function setFname(string $fname): void
    {
        $this->fname = $fname;
    }

    /**
     * @return string
     */
    public function getLname(): string
    {
        return $this->lname;
    }

    /**
     * @param string $lname
     */
    public function setLname(string $lname): void
    {
        $this->lname = $lname;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getPlz(): int
    {
        return $this->plz;
    }

    /**
     * @param int $plz
     */
    public function setPlz(int $plz): void
    {
        $this->plz = $plz;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPw(string $password): void
    {
        $this->password = $password;
    }

}