<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Argument;

class Channels extends ArgumentAbstract
{
    private array $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    protected function generateRawObjectString(): string
    {
        return sprintf(
            '{ ids: [%s] }',
            implode(',', $this->ids)
        );
    }
}
