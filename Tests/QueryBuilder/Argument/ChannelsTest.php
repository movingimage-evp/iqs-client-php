<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Argument;

use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Channels;
use PHPUnit\Framework\TestCase;

class ChannelsTest extends TestCase
{
    /**
     * @dataProvider channelsProvider
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Channels::createRawObject
     */
    public function testCreateRawObject(array $ids, string $expected): void
    {
        $channels = new Channels($ids);
        $this->assertEquals($expected, (string) $channels->createRawObject());
    }

    public static function channelsProvider(): array
    {
        return [
            [[], '{ ids: [] }'],
            [[1], '{ ids: [1] }'],
            [[34,98], '{ ids: [34,98] }'],
        ];
    }
}
