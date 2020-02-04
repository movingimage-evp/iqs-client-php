<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder;

use GraphQL\QueryBuilder\QueryBuilder;

class CustomMetadataQueryBuilder extends QueryBuilder
{
    private const OBJECT_NAME = 'customMetadata';

    public const METADATA_STRING = 'MetadataString';
    public const METADATA_NUMBER = 'MetadataNumber';

    /** @var string[] */
    private $validTypes = [
        self::METADATA_STRING,
        self::METADATA_NUMBER
    ];

    public function __construct(string $customMetaDataKey, string $fieldName, string $type = self::METADATA_STRING)
    {
        $this->validateType($type);

        parent::__construct($fieldName.': '.self::OBJECT_NAME);

        $this->setArgument('field', $customMetaDataKey)
            ->selectField((new QueryBuilder('... on '.$type))
                ->selectField('value')
            )
        ;
    }

    private function validateType(string $type): void
    {
        if (!\in_array($type, $this->validTypes)) {
            throw new \InvalidArgumentException('customMetadata type is not supported');
        }
    }
}
