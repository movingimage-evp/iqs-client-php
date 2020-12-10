<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\QueryBuilder\Video;

use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;

class VideoQueryBuilder extends AbstractVideoQueryBuilder implements MainQueryBuilderInterface
{
    private const VALID_ENVS = ['prod', 'qa'];
    private const OBJECT_NAME = 'video';

    public function __construct(string $videoId, int $vmId, string $fieldName = '', string $env = 'prod')
    {
        $this->validateEnv($env);
        $prefix = $fieldName ? $fieldName.': ' : '';

        parent::__construct($prefix.self::OBJECT_NAME);

        $this
            ->setArgument('vmId', $vmId)
            ->setArgument('env', $env)
            ->setArgument('lang', '(DEFAULT)')
            ->setArgument('id', $videoId)
        ;
    }

    /**
     * @param string $env
     * @return bool
     */
    private function validateEnv(string $env): void
    {
        if (!in_array($env, self::VALID_ENVS)) {
            throw new \InvalidArgumentException();
        }
    }
}
