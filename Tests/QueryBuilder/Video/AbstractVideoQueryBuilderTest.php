<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Video;

use GraphQL\Query;
use GraphQL\QueryBuilder\AbstractQueryBuilder;
use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\VideoQueryBuilder;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class AbstractVideoQueryBuilderTest extends TestCase
{
    use Helper;

    /** @var AbstractQueryBuilder */
    private $abstractVideoQueryBuilder;

    public function setUp(): void
    {
        $this->abstractVideoQueryBuilder = new VideoQueryBuilder('12', 4);
    }

    public function testConstructor(): void
    {
        self::assertInstanceOf(AbstractQueryBuilder::class, $this->abstractVideoQueryBuilder);
    }

    /**
     * @dataProvider selectFieldProvider
     */
    public function testSelectField(string $expectedFieldName, string $methodName, ?string $newFieldName): void
    {
        $this->abstractVideoQueryBuilder->{$methodName}($newFieldName);
        $selectionSet = $this->getProperty($this->abstractVideoQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);

        self::assertCount(1, $selectionSet);
        self::assertEquals($expectedFieldName, $selectionSet[0]);
    }

    public function selectFieldProvider(): array
    {
        return [
            ['title', 'selectTitle', '' ],
            ['newName: title', 'selectTitle', 'newName' ],
            ['duration', 'selectDuration', ''],
            ['newName: duration', 'selectDuration', 'newName'],
            ['thumbnailUrl', 'selectThumbnailUrl', ''],
            ['newName: thumbnailUrl', 'selectThumbnailUrl', 'newName'],
            ['videoId', 'selectVideoId', ''],
            ['newName: videoId', 'selectVideoId', 'newName'],
        ];
    }

    public function testSelectCustomMetaDataField(): void
    {
        $newFieldName = 'userId';
        $customMetaDataKey = '04-User-ID';
        $this->abstractVideoQueryBuilder->selectCustomMetadataField($customMetaDataKey, $newFieldName);

        $videoQueryBuilderSelectionSet = $this->getProperty($this->abstractVideoQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $customMetaQueryBuilder = $this->getQueryBuilderFromArray($videoQueryBuilderSelectionSet);
        $fieldName = $this->getProperty($customMetaQueryBuilder->getQuery(), 'fieldName');
        self::assertEquals($newFieldName.': customMetadata', $fieldName);

        $customMetaQueryBuilderArgumentsList = $this->getProperty($customMetaQueryBuilder, 'argumentsList', AbstractQueryBuilder::class);
        self::assertEquals($customMetaDataKey, $customMetaQueryBuilderArgumentsList['field']);

        $customMetaQueryBuilderSelectionSet = $this->getProperty($customMetaQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $customMetaQuery = $this->getQueryFromArray($customMetaQueryBuilderSelectionSet);
        $customMetaQueryFieldName = $this->getProperty($customMetaQuery, 'fieldName');
        self::assertEquals('... on MetadataString', $customMetaQueryFieldName);

        $customMetaQuerySelectionSet = $this->getProperty($customMetaQuery, 'selectionSet');
        self::assertContains('value', $customMetaQuerySelectionSet);
    }

    private function getQueryFromArray(array $selectionSet): Query
    {
        foreach ($selectionSet as $selection) {
            if ($selection instanceof Query) {
                return $selection;
            }
        }

        self::fail('no Query found in selectionSet');
    }

    private function getQueryBuilderFromArray(array $selectionSet): QueryBuilder
    {
        foreach ($selectionSet as $selection) {
            if ($selection instanceof QueryBuilder) {
                return $selection;
            }
        }

        self::fail('no QueryBuilder found in selectionSet');
    }

    public function testSelectRelatedVideo(): void
    {
        $expectedRelatedVideosQueryBuilder = new RelatedVideosQueryBuilder();

        $this->abstractVideoQueryBuilder->selectRelatedVideos(
            $expectedRelatedVideosQueryBuilder,
            [ new RelationExpression('key', false) ]
        );

        $videoQueryBuilderSelectionSet = $this->getProperty($this->abstractVideoQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $relationQueryBuilder = $this->getQueryBuilderFromArray($videoQueryBuilderSelectionSet);
        $query = $this->getProperty($relationQueryBuilder, 'query', AbstractQueryBuilder::class);
        $fieldName = $this->getProperty($query, 'fieldName');
        self::assertEquals('related', $fieldName);

        $selectionSet = $this->getProperty($relationQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $relatedVideosQueryBuilder = $this->getQueryBuilderFromArray($selectionSet);
        self::assertEquals($expectedRelatedVideosQueryBuilder, $relatedVideosQueryBuilder);
    }

    public function testWrongArgumentForRelationExpressionsThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $relatedVideosQueryBuilder = new RelatedVideosQueryBuilder();

        $this->abstractVideoQueryBuilder->selectRelatedVideos(
            $relatedVideosQueryBuilder,
            ['not_a_relation_expression']
        );
    }
}
