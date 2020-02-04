<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Video;

use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\CustomMetadataQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationQueryBuilder;

abstract class AbstractVideoQueryBuilder extends QueryBuilder
{
    public function selectThumbnailUrl(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('thumbnailUrl', $fieldName);
    }

    public function selectDuration(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('duration', $fieldName);
    }

    public function selectTitle(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('title', $fieldName);
    }

    public function selectVideoId(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('videoId', $fieldName);
    }

    public function selectCustomMetadataField(string $customMetadataKey, string $fieldName): self
    {
        $this->selectField(new CustomMetadataQueryBuilder($customMetadataKey, $fieldName));

        return $this;
    }

    /**
     * @param RelationExpression[] $relationExpressions
     */
    public function selectRelatedVideos(
        RelatedVideosQueryBuilder $relatedVideosQueryBuilder,
        array $relationExpressions,
        string $fieldName = ''
    ): self {
        $this->validateRelationExpressions($relationExpressions);

        $this->selectField(new RelationQueryBuilder($relatedVideosQueryBuilder, $relationExpressions, $fieldName));

        return $this;
    }

    private function selectFieldWithNewName(string $field, string $fieldName = ''): self
    {
        $prefix = $fieldName ? $fieldName.': ' : '';

        $this->selectField($prefix.$field);

        return $this;
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
