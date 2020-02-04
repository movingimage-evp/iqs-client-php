<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TokenGeneratorClient extends Client
{
    public function post($uri, array $options = []): ResponseInterface
    {
        return parent::post($uri, $options);
    }
}
