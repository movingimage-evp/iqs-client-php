<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Relation;

use GraphQL\QueryBuilder\AbstractQueryBuilder;
use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Channels;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Page;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationByKeywordsQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class RelationByKeywordsQueryBuilderTest extends TestCase
{
    use Helper;

    public function testConstructor(): void
    {
        $queryBuilder = new RelationByKeywordsQueryBuilder(
            new RelatedVideosQueryBuilder()
        );
        self::assertInstanceOf(QueryBuilder::class, $queryBuilder);
        self::assertNotInstanceOf(MainQueryBuilderInterface::class, $queryBuilder);
    }

    public function testConfiguration(): void
    {
        $relatedVideosQueryBuilder = new RelatedVideosQueryBuilder();
        $expectedFieldName = 'fieldName';
        $channelsIds = [7,8,9];
        $pageSize = 3;
        $pageCursor = 'QWE';

        $relationQueryBuilder = new RelationByKeywordsQueryBuilder(
            $relatedVideosQueryBuilder,
            $expectedFieldName,
            $channelsIds,
            $pageSize,
            $pageCursor
        );

        $query = $this->getProperty($relationQueryBuilder, 'query', AbstractQueryBuilder::class);
        $fieldName = $this->getProperty($query, 'fieldName');
        self::assertEquals($expectedFieldName.': relatedByKeywords', $fieldName);

        $selectionSet = $this->getProperty($relationQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $subQueryBuilder = $this->getQueryBuilderFromArray($selectionSet);
        self::assertEquals($relatedVideosQueryBuilder, $subQueryBuilder);

        $argumentsList = $this->getProperty($relationQueryBuilder, 'argumentsList', AbstractQueryBuilder::class);
        self::assertArrayHasKey('channels', $argumentsList);
        self::assertArrayHasKey('page', $argumentsList);

        self::assertEquals(
            $argumentsList['channels'],
            (new Channels($channelsIds))->createRawObject()
        );

        self::assertEquals(
            $argumentsList['page'],
            (new Page($pageSize, $pageCursor))->createRawObject()
        );
    }

    private function getQueryBuilderFromArray(array $selectionSet): QueryBuilder
    {
        foreach ($selectionSet as $selection) {
            if ($selection instanceof QueryBuilder) {
                return $selection;
            }
        }

        self::fail('no QueryBuilder in selectionSet');
    }
}
