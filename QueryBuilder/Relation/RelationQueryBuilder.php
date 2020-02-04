<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Relation;

use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder;

class RelationQueryBuilder extends QueryBuilder
{
    private const OBJECT_NAME = 'related';

    /**
     * @param RelationExpression[] $relationExpressions
     */
    public function __construct(
        RelatedVideosQueryBuilder $relatedVideosQueryBuilder,
        array $relationExpressions,
        string $fieldName = ''
    ) {
        $this->validateRelationExpressions($relationExpressions);

        $prefix = $fieldName ? $fieldName.': ' : '';
        parent::__construct($prefix.self::OBJECT_NAME);

        $this->setArgument(
            'exp',
            new RawObject($this->generateRelationExpressionString($relationExpressions))
        );

        $this->selectField($relatedVideosQueryBuilder);
    }

    /**
     * @param RelationExpression[] $relationExpressions
     */
    private function generateRelationExpressionString(array $relationExpressions): string
    {
        return sprintf(
            '[%s]',
            implode(',', $relationExpressions)
        );
    }

    private function validateRelationExpressions(array $relationExpressions): void
    {
        foreach ($relationExpressions as $relationExpression) {
            if (!$relationExpression instanceof RelationExpression) {
                throw new \InvalidArgumentException('array elements must be instance of '.RelationExpression::class);
            }
        }
    }
}
