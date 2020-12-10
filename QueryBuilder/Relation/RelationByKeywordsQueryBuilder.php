<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Relation;

use GraphQL\QueryBuilder\QueryBuilder;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\ArgumentAbstract;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Channels;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Argument\Page;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder;

class RelationByKeywordsQueryBuilder extends QueryBuilder
{
    private const OBJECT_NAME = 'relatedByKeywords';
    private const CHANNELS_ARGUMENT_NAME = 'channels';
    private const PAGE_ARGUMENT_NAME = 'page';

    public function __construct(
        RelatedVideosQueryBuilder $relatedVideosQueryBuilder,
        string $fieldName = '',
        array $channels = [],
        int $pageSize = 10,
        string $pageCursor = ''
    )
    {
        $prefix = $fieldName ? $fieldName . ': ' : '';
        parent::__construct($prefix . self::OBJECT_NAME);

        $this->setPageArgument($pageSize, $pageCursor);
        $this->setChannelsArgument($channels);
        $this->selectField($relatedVideosQueryBuilder);
        $this->selectField('cursor');
    }

    private function setObjectArgument(string $argumentName, ArgumentAbstract $object)
    {
        return $this->setArgument($argumentName, $object->createRawObject());
    }

    private function setChannelsArgument(array $channelIds)
    {
        return $this->setObjectArgument(
            self::CHANNELS_ARGUMENT_NAME,
            new Channels($channelIds)
        );
    }

    private function setPageArgument(int $size, string $cursor)
    {
        return $this->setObjectArgument(
            self::PAGE_ARGUMENT_NAME,
            new Page($size, $cursor)
        );
    }
}
