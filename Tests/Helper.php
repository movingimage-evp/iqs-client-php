<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Tests;

use ReflectionProperty;

trait Helper
{
    private function setProperty($object, string $propertyName, $propertyValue): void
    {
        $reflectionProperty = new ReflectionProperty(\get_class($object), $propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $propertyValue);
    }

    private function getProperty($object, string $propertyName, ?string $className = null)
    {
        $className = $className ?: \get_class($object);
        $reflectionProperty = new ReflectionProperty($className, $propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    private function createVideo(): Video
    {
        $video = new Video();

        return $video->setId('id')->setTitle('title');
    }

    private function castVideoToStdObject(Video $video): object
    {
        $object = new \stdClass();
        $object->id = $video->getId();
        $object->title = $video->getTitle();

        return $object;
    }

}