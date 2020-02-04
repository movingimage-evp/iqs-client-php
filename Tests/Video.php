<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Tests;

class Video
{
    /** @var string */
    private $id;

    /** @var string */
    private $title;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
