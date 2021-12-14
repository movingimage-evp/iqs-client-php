<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder;

use GraphQL\QueryBuilder\AbstractQueryBuilder;
use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\CustomMetadataQueryBuilder;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class CustomMetadataQueryBuilderTest extends TestCase
{
    use Helper;

    public function testConstructor(): void
    {
        $queryBuilder = new CustomMetadataQueryBuilder('key', 'fieldName');
        self::assertInstanceOf(QueryBuilder::class, $queryBuilder);
        self::assertNotInstanceOf(MainQueryBuilderInterface::class, $queryBuilder);
    }

    public function testConfiguration(): void
    {
        $newFieldName = 'fieldName';
        $expectedCustomMetadataKey = 'key';
        $expectedType = CustomMetadataQueryBuilder::METADATA_STRING;

        $queryBuilder = new CustomMetadataQueryBuilder($expectedCustomMetadataKey, $newFieldName, $expectedType);
        $query = $this->getProperty($queryBuilder, 'query', AbstractQueryBuilder::class);

        $fieldName = $this->getProperty($query, 'fieldName');
        self::assertEquals($newFieldName.': customMetadata', $fieldName);

        $argumentsList = $this->getProperty($queryBuilder, 'argumentsList', AbstractQueryBuilder::class);
        self::assertArrayHasKey('field', $argumentsList);
        self::assertEquals($expectedCustomMetadataKey, $argumentsList['field']);

        $selectionSet = $this->getProperty($queryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        $subQueryBuilder = $this->getQueryBuilderFromArray($selectionSet);
        $subQuery = $this->getProperty($subQueryBuilder, 'query', AbstractQueryBuilder::class);
        $subFieldName = $this->getProperty($subQuery, 'fieldName');
        self::assertEquals('... on '.$expectedType, $subFieldName);

        $subSelectionSet = $this->getProperty($subQueryBuilder, 'selectionSet', AbstractQueryBuilder::class);
        self::assertContains('value', $subSelectionSet);
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

    /**
     * @dataProvider typesDataProvider
     */
    public function testTypes(string $type, bool $expectException): void
    {
        if ($expectException) {
            $this->expectException(\InvalidArgumentException::class);
        }

        new CustomMetadataQueryBuilder('key', 'fieldName', $type);

        self::assertFalse($expectException);
    }

    public function typesDataProvider(): array
    {
        return [
            [ CustomMetadataQueryBuilder::METADATA_NUMBER, false ],
            [ CustomMetadataQueryBuilder::METADATA_STRING, false ],
            [ 'not_existing_type', true ],
        ];
    }
}
