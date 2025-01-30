<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\QueryBuilder\Relation;

use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression;
use PHPUnit\Framework\TestCase;

class RelationExpressionTest extends TestCase
{
    /**
     * @dataProvider validatePredicateProvider
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression::validatePredicate
     */
    public function testValidatePredicate(string $predicate, bool $exceptionThrown): void
    {
        if ($exceptionThrown) {
            $this->expectException(\InvalidArgumentException::class);
        }

        new RelationExpression(
            'left_key',
            true,
            $predicate
        );

        self::assertFalse($exceptionThrown);
    }

    public static function validatePredicateProvider(): array
    {
        return [
            [ RelationExpression::MATCH_OPERATOR_IN, false ],
            [ RelationExpression::MATCH_OPERATOR_EQUALS, false ],
            [ 'not_supported_match_operator', true ],
        ];
    }

    /**
     * @dataProvider toStringProvider
     * @covers \MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression::toString
     */
    public function testToString(
        string $left,
        bool $leftIsCustomMetadata,
        string $predicate,
        ?string $right,
        bool $rightIsCustomMetadata,
        string $expectedOutput
    ): void {
        $relationExpression = new RelationExpression(
            $left,
            $leftIsCustomMetadata,
            $predicate,
            $right,
            $rightIsCustomMetadata
        );

        self::assertEquals($expectedOutput, (string) $relationExpression);
    }

    public static function toStringProvider(): array
    {
        return [
            [
                'left',
                false,
                'IN',
                'right',
                false,
                '{left: "left", predicate: IN, right: "right"}',
            ],
            [
                'left',
                true,
                'EQUALS',
                null,
                true,
                '{left: "customMetadata.left", predicate: EQUALS, right: "customMetadata.left"}',
            ],
            [
                'left',
                true,
                'EQUALS',
                null,
                false,
                '{left: "customMetadata.left", predicate: EQUALS, right: "customMetadata.left"}',
            ],
            [
                'left',
                true,
                'EQUALS',
                'right',
                false,
                '{left: "customMetadata.left", predicate: EQUALS, right: "right"}',
            ],
            [
                'left',
                false,
                'IN',
                'right',
                true,
                '{left: "left", predicate: IN, right: "customMetadata.right"}',
            ],
        ];
    }
}
