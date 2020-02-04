<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\Service;

class SimpleVideoEntity
{
    private $id;
    private $title;

    public function __construct(\stdClass $originalEntity)
    {
        $this->id = $originalEntity;
        $this->title = $originalEntity->title;
    }
}
