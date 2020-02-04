<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Video;

use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;

class VideoQueryBuilder extends AbstractVideoQueryBuilder implements MainQueryBuilderInterface
{
    private const OBJECT_NAME = 'video';

    public function __construct(string $videoId, int $vmId, string $fieldName = '')
    {
        $prefix = $fieldName ? $fieldName.': ' : '';

        parent::__construct($prefix.self::OBJECT_NAME);

        $this
            ->setArgument('vmId', $vmId)
            ->setArgument('env', 'prod')
            ->setArgument('lang', '(DEFAULT)')
            ->setArgument('id', $videoId)
        ;
    }
}
