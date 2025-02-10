<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Video;

use GraphQL\QueryBuilder\AbstractQueryBuilder;
use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\IqsQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\AbstractVideoQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class RelatedVideosQueryBuilderTest extends TestCase
{
    use Helper;

    /** @var RelatedVideosQueryBuilder */
    private $relatedVideosQueryBuilder;

    public function setUp(): void
    {
        $this->relatedVideosQueryBuilder = new RelatedVideosQueryBuilder();
    }

    /**
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder::__construct
     */
    public function testConstructor(): void
    {
        self::assertInstanceOf(RelatedVideosQueryBuilder::class, $this->relatedVideosQueryBuilder);
        self::assertInstanceOf(AbstractVideoQueryBuilder::class, $this->relatedVideosQueryBuilder);
        self::assertInstanceOf(QueryBuilder::class, $this->relatedVideosQueryBuilder);
        self::assertNotInstanceOf(MainQueryBuilderInterface::class, $this->relatedVideosQueryBuilder);
    }

    /**
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder::testConfiguration
     */
    public function testConfiguration(): void
    {
        $argumentsList = $this->getProperty($this->relatedVideosQueryBuilder, 'argumentsList', AbstractQueryBuilder::class);
        $query = $this->getProperty($this->relatedVideosQueryBuilder, 'query', AbstractQueryBuilder::class);
        $queryFieldName = $this->getProperty($query, 'fieldName');

        self::assertEquals('videos', $queryFieldName);
        self::assertEmpty($argumentsList);
    }
}
