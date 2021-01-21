<?php


class Post
{
    private int $id, $restricted;
    private string $path, $title;
    private User $user;
    private DateTime $date;

public function __construct(int $id, string $title, string $path, int $restricted, int $user, string $date, string $text)
{
    $db = new DB();
    $this->id = $id;
    $this->title = $title;
    $this->path = $path;
    $this->restricted = $restricted;
    $this->user = $db->getUserByID($user);
    $this->date = date_create($date);
    $this->text = $text;
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
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
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
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDateTime(): string
    {
        return date_format($this->date, 'd.m.Y H:i:s');
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return date_format($this->date, 'd.m.Y');
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
        return $this->getTitle();
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