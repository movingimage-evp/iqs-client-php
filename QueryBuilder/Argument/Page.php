<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Argument;

class Page extends ArgumentAbstract
{
    private int $size;

    private string $cursor;

    public function __construct(int $size, string $cursor)
    {
        $this->size = $size;
        $this->cursor = $cursor;
    }

    /**
     * @inheritDoc
     */
    protected function generateRawObjectString(): string
    {
        return sprintf(
            '{ size: %d, cursor: "%s" }',
            $this->size,
            $this->cursor
        );
    }
}
