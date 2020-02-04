<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Service;

use GraphQL\Client;
use MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface;
use MovingImage\Bundle\IqsBundle\Interfaces\ObjectFactoryInterface;

class IqsClient
{
    /** @var Client */
    private $graphQLClient;

    public function __construct(GraphQLClientFactory $clientFactory)
    {
        $this->graphQLClient = $clientFactory->createGraphQLClient();
    }

    public function runQuery(MainQueryBuilderInterface $queryBuilder, ObjectFactoryInterface $factory): object
    {
        $result = $this->graphQLClient->runQuery($queryBuilder->getQuery());

        return $factory->createObject($result->getData());
    }
}
