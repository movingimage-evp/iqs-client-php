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

    /**
     * @return Client
     */
    public function createGraphQLClient(): Client
    {
        return new Client(
            $this->endpoint,
            [],
            [
                'headers' => $this->getClientHeaders()
            ]
        );
    }

    /**
     * @return string[]
     */
    private function getClientHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->tokenGenerator->getToken(),
        ];
    }
}
