<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Service;

use GraphQL\Client;

class GraphQLClientFactory
{
    /** @var TokenGenerator */
    private $tokenGenerator;

    /** @var string */
    private $endpoint;

    public function __construct(TokenGenerator $tokenGenerator, string $endpoint)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->endpoint = $endpoint;
    }

    public function createGraphQLClient(): Client
    {
        $accessToken = $this->tokenGenerator->getToken();

        return new Client(
            $this->endpoint,
            [],
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                ]
            ]
        );
    }
}
