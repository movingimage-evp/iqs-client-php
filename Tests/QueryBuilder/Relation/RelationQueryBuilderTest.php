<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Relation;

use GraphQL\QueryBuilder\AbstractQueryBuilder;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;
use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class RelationQueryBuilderTest extends TestCase
{
    use Helper;

    /**
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationQueryBuilder::__construct
     */
    public function testConstructor(): void
    {
        $queryBuilder = new RelationQueryBuilder(new RelatedVideosQueryBuilder(), [new RelationExpression('comparedField', false)]);
        self::assertInstanceOf(QueryBuilder::class, $queryBuilder);
        self::assertNotInstanceOf(MainQueryBuilderInterface::class, $queryBuilder);
    }

    /**
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationQueryBuilder::createRawObject
     */
    public function testConfiguration(): void
    {
        $relatedVideosQueryBuilder = new RelatedVideosQueryBuilder();
        $expectedComparedField1 = 'comparedField1';
        $expectedComparedField2 = 'comparedField2';
        $expectedFieldName = 'fieldName';

        $relationQueryBuilder = new RelationQueryBuilder(
            $relatedVideosQueryBuilder,
            [
                new RelationExpression($expectedComparedField1, false),
                new RelationExpression($expectedComparedField2, false)
            ],
            $expectedFieldName
        );

        $query = $this->getProperty($relationQueryBuilder, 'query', AbstractQueryBuilder::class);
        $fieldName = $this->getProperty($query, 'fieldName');
        self::assertEquals($expectedFieldName.': related', $fieldName);

        $selectionSet = $this->getProperty($relationQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $subQueryBuilder = $this->getQueryBuilderFromArray($selectionSet);
        self::assertEquals($relatedVideosQueryBuilder, $subQueryBuilder);

        $argumentsList = $this->getProperty($relationQueryBuilder, 'argumentsList', AbstractQueryBuilder::class);
        self::assertArrayHasKey('exp', $argumentsList);
        /** @var RawObject $rawObject */
        $rawObject = $argumentsList['exp'];
        self::assertEquals(
            $rawObject,
            sprintf(
                '[{left: "%s", predicate: EQUALS, right: "%s"},{left: "%s", predicate: EQUALS, right: "%s"}]',
                $expectedComparedField1,
                $expectedComparedField1,
                $expectedComparedField2,
                $expectedComparedField2
            )
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

    /**
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationQueryBuilder::testWrongArgumentForRelationExpressionsThrowsException
     */
    public function testWrongArgumentForRelationExpressionsThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new RelationQueryBuilder(
            new RelatedVideosQueryBuilder(),
            ['not_a_relation_expression']
        );
    }
}
