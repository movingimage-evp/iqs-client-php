<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Argument;

use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    /**
     * @dataProvider pageProvider
     */
    public function testCreateRawObject(int $size, string $cursor, string $expected): void
    {
        $page = new Page($size, $cursor);
        $this->assertEquals($expected, (string) $page->createRawObject());
    }

    public function pageProvider()
    {
        return [
            [10, '', '{ size: 10, cursor: "" }'],
            [17, 'Test', '{ size: 17, cursor: "Test" }'],
        ];
    }
}
