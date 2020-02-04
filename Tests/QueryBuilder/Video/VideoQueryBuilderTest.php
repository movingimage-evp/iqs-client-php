<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Video;

use GraphQL\QueryBuilder\AbstractQueryBuilder;
use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\IqsQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\AbstractVideoQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\VideoQueryBuilder;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class VideoQueryBuilderTest extends TestCase
{
    use Helper;

    /** @var VideoQueryBuilder */
    private $videoQueryBuilder;

    /** @var int */
    private $videoManagerId = 1234;

    /** @var string */
    private $videoId = '987';

    public function setUp(): void
    {
        $this->videoQueryBuilder = new VideoQueryBuilder($this->videoId, $this->videoManagerId);
    }

    public function testConstructor(): void
    {
        self::assertInstanceOf(VideoQueryBuilder::class, $this->videoQueryBuilder);
        self::assertInstanceOf(AbstractVideoQueryBuilder::class, $this->videoQueryBuilder);
        self::assertInstanceOf(MainQueryBuilderInterface::class, $this->videoQueryBuilder);
    }

    public function testQueryBuilderCorrectlyConfigured(): void
    {
        $argumentsList = $this->getProperty($this->videoQueryBuilder, 'argumentsList', AbstractQueryBuilder::class);
        $query = $this->getProperty($this->videoQueryBuilder, 'query', AbstractQueryBuilder::class);
        $queryFieldName = $this->getProperty($query, 'fieldName');

        self::assertEquals('video', $queryFieldName);
        self::assertEquals($argumentsList['vmId'], $this->videoManagerId);
        self::assertEquals($argumentsList['env'], 'prod');
        self::assertEquals($argumentsList['lang'], '(DEFAULT)');
        self::assertEquals($argumentsList['id'], $this->videoId);
    }
}
