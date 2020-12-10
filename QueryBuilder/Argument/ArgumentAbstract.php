<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Argument;

use GraphQL\RawObject;

Abstract class ArgumentAbstract
{
    public function createRawObject(): RawObject
    {
        return new RawObject($this->generateRawObjectString());
    }

    abstract protected function generateRawObjectString(): string;
}
