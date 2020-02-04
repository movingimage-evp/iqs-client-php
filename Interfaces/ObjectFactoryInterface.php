<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Interfaces;

interface ObjectFactoryInterface
{
    public function createObject(object $object): object;
}