<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Relation;

class RelationExpression
{
    public const MATCH_OPERATOR_EQUALS = 'EQUALS';
    public const MATCH_OPERATOR_IN = 'IN';
    public const CUSTOM_METADATA_KEY_PREFIX = 'customMetadata.';

    private const MATCH_OPERATORS = [
        self::MATCH_OPERATOR_EQUALS,
        self::MATCH_OPERATOR_IN,
    ];

    /** @var string */
    private $predicate;

    /** @var string */
    private $left;

    /** @var string */
    private $right;

    public function __construct(
        string $left,
        bool $leftIsCustomMetadataField,
        string $predicate = self::MATCH_OPERATOR_EQUALS,
        ?string $right = null,
        bool $rightIsCustomMetadataField = false
    ) {
        if ($leftIsCustomMetadataField) {
            $left = self::CUSTOM_METADATA_KEY_PREFIX.$left;
        }

        if ($rightIsCustomMetadataField && $right) {
            $right = self::CUSTOM_METADATA_KEY_PREFIX.$right;
        }

        $this->predicate = $predicate;
        $this->left = $left;
        $this->right = $right ?? $left;

        $this->validatePredicate();
    }

    public function __toString(): string
    {
        return sprintf(
            '{left: "%s", predicate: %s, right: "%s"}',
            $this->left,
            $this->predicate,
            $this->right
        );
    }

    public function validatePredicate(): void
    {
        if (!\in_array($this->predicate, self::MATCH_OPERATORS, true)) {
            throw new \InvalidArgumentException('predicate contains a non valid value');
        }
    }
}
