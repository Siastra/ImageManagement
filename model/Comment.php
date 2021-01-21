<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Post.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/DB.php';

class Comment
{
    private ?Post $post;
    private ?User $user;
    private string $text;
    private DateTime $date;

    public function __construct(int $post_id, int $user_id, string $text, DateTime $date)
    {
        $db = new DB();
        $this->post = $db->getPostById($post_id);
        $this->user = $db->getUserByID($user_id);
        $this->text = $text;
        $this->date = $date;
    }

    /**
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post|null $post
     */
    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
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
    public function getDate(): string
    {
        return date_format($this->date, 'd.m.Y H:i');
    }

}