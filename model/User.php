<?php


class User
{
    private int $id ,$admin, $activated;
    private string $title, $fname, $lname, $username, $password, $email;

    public function __construct($id, $title, $fname, $lname, $email, $username, $password, $admin, $activated)
    {
        $this->email = $email;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->title = $title;
        $this->username = $username;
        $this->admin = intval($admin);
        $this->activated = intval($activated);
        $this->id = $id;
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
     * @return int
     */
    public function getActivated(): int
    {
        return $this->activated;
    }

    /**
     * @param int $activated
     */
    public function setActivated(int $activated): void
    {
        $this->activated = $activated;
    }

    /**
     * @return int
     */
    public function isAdmin(): int
    {
        return $this->admin;
    }

    /**
     * @param int $admin
     */
    public function setAdmin(int $admin): void
    {
        $this->admin = $admin;
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
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

}