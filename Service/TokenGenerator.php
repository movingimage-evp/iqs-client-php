<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Service;

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

    public function getToken(): string
    {
        $options = [
            'form_params' => [
                'grant_type' => 'password',
                'client_secret' => $this->clientSecret,
                'client_id' => $this->clientId,
                'username' => $this->username,
                'password' => $this->password,
            ]
        ];

        $response = $this->client->post('auth/realms/platform/protocol/openid-connect/token', $options);

        $content = json_decode($response->getBody()->getContents(), true);

        return $content['access_token'];
    }
}
