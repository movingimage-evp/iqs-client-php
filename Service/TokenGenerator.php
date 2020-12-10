<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Service;

use Psr\Http\Message\ResponseInterface;

class TokenGenerator
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var Client */
    private $client;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $clientId;

    public function __construct(
        TokenGeneratorClient $client,
        string $username,
        string $password,
        string $clientSecret,
        string $clientId
    ) {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
        $this->clientSecret = $clientSecret;
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->sendTokenRequest()['access_token'];
    }

    /**
     * @return array
     */
    private function sendTokenRequest(): array
    {
        return $this->parseResponse(
            $this->client->post('', $this->getFormParams())
        );
    }

    /**
     * @return array
     */
    private function getFormParams(): array
    {
        return [
            'form_params' => [
                'grant_type' => 'password',
                'client_secret' => $this->clientSecret,
                'client_id' => $this->clientId,
                'username' => $this->username,
                'password' => $this->password,
            ]
        ];
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function parseResponse(ResponseInterface $response): array
    {
        return \json_decode($response->getBody()->getContents(), true);
    }
}
