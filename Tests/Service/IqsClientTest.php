<?php

declare(strict_types=1);

namespace MovingImage\IqsBundle\Tests\Service;

use Couchbase\QueryResult;
use MovingImage\Bundle\IqsBundle\Tests\ObjectFactory;
use GraphQL\Client;
use GraphQL\Results;
use MovingImage\Bundle\IqsBundle\QueryBuilder\Video\VideoQueryBuilder;
use MovingImage\Bundle\IqsBundle\Service\GraphQLClientFactory;
use MovingImage\Bundle\IqsBundle\Service\IqsClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use MovingImage\Bundle\IqsBundle\Tests\Helper;

class IqsClientTest extends TestCase
{
    use Helper;

    /** @var MockObject|GraphQLClientFactory */
    private $clientFactory;

    /** @var IqsClient */
    private $iqsClient;

    /** @var MockObject|Client */
    private $graphQLClient;

    /** @var VideoFactory */
    private $objectFactory;

    public function setUp(): void
    {
        $this->graphQLClient = $this->createMock(Client::class);
        $this->clientFactory = $this->createMock(GraphQLClientFactory::class);
        $this->clientFactory->method('createGraphQLClient')->willReturn($this->graphQLClient);
        $this->objectFactory = new ObjectFactory();

        $this->iqsClient = new IqsClient($this->clientFactory);
    }

    public function testConstructor(): void
    {
        self::assertInstanceOf(IqsClient::class, $this->iqsClient);
    }

    public function testRunQuery(): void
    {
        $videoId = '1234';
        $videoQueryBuilder = (new VideoQueryBuilder($videoId, 234))->selectTitle();
        $expectedVideo = $this->createVideo()->setId($videoId);
        $queryResultObject = (object) [
            'video' => $this->castVideoToStdObject($expectedVideo),
        ];
        $graphQlQueryResults = $this->createMock(Results::class);
        $graphQlQueryResults->method('getData')->willReturn($queryResultObject);
        $this->graphQLClient->method('runQuery')->willReturn($graphQlQueryResults);

        /** @var QueryResult $returnedQueryResult */
        $returnedVideo = $this->iqsClient->runQuery($videoQueryBuilder, $this->objectFactory);

        self::assertEquals($expectedVideo, $returnedVideo);
    }
}
