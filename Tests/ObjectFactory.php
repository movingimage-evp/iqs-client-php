<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Tests;

use MovingImage\Bundle\IqsBundle\Interfaces\ObjectFactoryInterface;

class ObjectFactory implements ObjectFactoryInterface
{
    public function createObject(object $object): object
    {
        $video = new Video();

        return $video->setId($object->video->id)->setTitle($object->video->title);
    }
}
