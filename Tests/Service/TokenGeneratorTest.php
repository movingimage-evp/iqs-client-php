<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\Service;

use MovingImage\Bundle\IqsBundle\Service\TokenGenerator;
use MovingImage\Bundle\IqsBundle\Service\TokenGeneratorClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TokenGeneratorTest extends TestCase
{
    /** @var TokenGenerator */
    private $tokenGenerator;

    /** @var MockObject|TokenGeneratorClient */
    private $client;

    /** @var string */
    private $userName = 'username';

    /** @var string */
    private $password = 'password';

    /** @var string */
    private $clientSecret = 'secret';

    /** @var string */
    private $clientId = 'id';

    public function setUp(): void
    {
        $this->client = $this->createMock(TokenGeneratorClient::class);

        $this->tokenGenerator = new TokenGenerator(
            $this->client,
            $this->userName,
            $this->password,
            $this->clientSecret,
            $this->clientId
        );
    }

    public function testConstructor(): void
    {
        self::assertInstanceOf(TokenGenerator::class, $this->tokenGenerator);
    }

    public function testGetToken(): void
    {
        $expectedAccessToken = 'p98aposdk';
        $token = [
            'access_token' => $expectedAccessToken,
        ];

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamInterface->method('getContents')
            ->willReturn(json_encode($token))
        ;

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($streamInterface)
        ;

        $this->client->method('post')
            ->willReturn($response)
        ;

        $accessToken = $this->tokenGenerator->getToken();

        self::assertEquals($expectedAccessToken, $accessToken);
    }
}
