<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Video;

use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\Interfaces\ArgumentInterface;
use MovingImage\Bundle\IqsBundle\QueryBuilder\CustomMetadataQueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationByKeywordsQueryBuilder;
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

    public function selectDescription(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('description', $fieldName);
    }

    public function selectChannels(string $fieldName = ''): self
    {
        $channelsQuery = new QueryBuilder('channels');
        $channelsQuery->selectField('id');
        $channelsQuery->selectField('isRoot');

        $this->selectField($channelsQuery);

        return $this;
    }

    public function selectMetrics(string $fieldName = ''): self
    {
        $metricsQuery = new QueryBuilder('metrics');
        $metricsQuery->selectField('plays');
        $metricsQuery->selectField('views');

        $this->selectField($metricsQuery);

        return $this;
    }

    public function selectKeywords(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('keywords', $fieldName);
    }

    public function selectUploadDate(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('uploadDate', $fieldName);
    }

    public function selectModifiedDate(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('modifiedDate', $fieldName);
    }

    public function selectPublished(string $fieldName = ''): self
    {
        return $this->selectFieldWithNewName('published', $fieldName);
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

    public function selectRelatedByKeywordsVideos(
        RelatedVideosQueryBuilder $relatedVideosQueryBuilder,
        string $fieldName = '',
        array $channels = [],
        int $pageSize = 10,
        string $pageCursor = ''
    ): self {

        $this->selectField(
            new RelationByKeywordsQueryBuilder(
                $relatedVideosQueryBuilder,
                $fieldName,
                $channels,
                $pageSize,
                $pageCursor
            )
        );

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
