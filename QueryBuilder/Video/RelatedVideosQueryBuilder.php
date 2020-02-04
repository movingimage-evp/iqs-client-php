<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Video;

class RelatedVideosQueryBuilder extends AbstractVideoQueryBuilder
{
    private const OBJECT_NAME = 'videos';

    public function __construct(string $fieldName = '')
    {
        $prefix = $fieldName ? $fieldName.': ' : '';
        parent::__construct($prefix.self::OBJECT_NAME);
    }
}
