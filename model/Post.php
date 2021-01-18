<?php


class Post
{
    private int $id, $restricted;
    private string $path;
    private User $user;
    private DateTime $date;

public function __construct(int $id, string $path, int $restricted, int $user, string $date)
{
    $db = new DB();
    $this->id = $id;
    $this->path = $path;
    $this->restricted = $restricted;
    $this->user = $db->getUserByID($user);
    $this->date = date_create($date);;
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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return date_format($this->date, 'd.m.Y H:i:s');
    }

    /**
     * @param int $restricted
     */
    public function setRestricted(int $restricted): void
    {
        $this->restricted = $restricted;
    }

    /**
     * @return int
     */
    public function getRestricted(): int
    {
        return $this->restricted;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getFilename(): string {
        $temp = explode("/", $this->path);
        return $temp[sizeof($temp)-1];
    }

    public function getName(): string {
        return explode(".", $this->getFilename())[0];
    }

    public function getThumbnailPath(): string {
        return $path = "pictures/thumbnail/" . $this->getFilename();
    }

    public function getFullPath(): string {
        return $path = "pictures/full/" . $this->getFilename();
    }

    public function getDashPath(): string {
        return $path = "pictures/dashboard/" . $this->getFilename();
    }


}