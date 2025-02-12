<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\Service;

use MovingImage\Bundle\IqsBundle\Service\GraphQLClientFactory;
use MovingImage\Bundle\IqsBundle\Service\TokenGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class GraphQLClientFactoryTest extends TestCase
{
    use Helper;

    /** @var MockObject|TokenGenerator */
    private $tokenGenerator;

    /** @var string */
    private $expectedEndpoint = 'aasdf.asd.com';

    /** @var GraphQLClientFactory */
    private $graphQLClientFactory;

    public function setUp(): void
    {
        $this->tokenGenerator = $this->createMock(TokenGenerator::class);

        $this->graphQLClientFactory = new GraphQLClientFactory($this->tokenGenerator, $this->expectedEndpoint);
    }

    /**
     * @covers \MovingImage\Bundle\IqsBundle\Service\GraphQLClientFactory::__construct
     */
    public function testConstructor(): void
    {
        self::assertInstanceOf(GraphQLClientFactory::class, $this->graphQLClientFactory);
    }

    /**
     * @covers \MovingImage\Bundle\IqsBundle\Service\GraphQLClientFactory::createGraphQLClient
     */
    public function testCreateClient(): void
    {
        $token = 'access_token';
        $this->tokenGenerator->method('getToken')->willReturn($token);

        $client = $this->graphQLClientFactory->createGraphQLClient();

        $endpoint = $this->getProperty($client, 'endpointUrl');
        self::assertEquals($this->expectedEndpoint, $endpoint);

        $httpHeaders = $this->getProperty($client, 'httpHeaders');

        self::assertArrayHasKey('Authorization', $httpHeaders);
        self::assertEquals('Bearer '.$token, $httpHeaders['Authorization']);
    }
}
