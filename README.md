# IQS (Index Query Service) Client Bundle

## Installation

Add the bitbucket repository to the composer.json file.
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://bitbucket.org/movingimage24/iqs-client-php"
        }
    ]
}
```

Before you can run composer to require the dependency, composer needs to get access to Bitbucket.
This is done by the file auth.json, you need to create it in the same directory as the composer.json, with this content.
```json
{
  "bitbucket-oauth": {
    "bitbucket.org": {
      "consumer-key": "xXw5q9quAUtN34fYzH",
      "consumer-secret": "c6nrRc9gwq488dn8hRjTRSrHKe5QYhbG"
    }
  }
}
```
Install now the dependency.
```
composer require movingimage/iqs-client-php:dev-master
```
### auto-generate auth.json when installing the project
You should put auth.json to the .gitignore file.
The content of auth.json is periodically updated, that's why it must not exist in the repository.
```gitignore
#.gitignore
auth.json
```

Write a script which creates the file auth.json if it doesn't exist yet.
```
#!/usr/bin/env php
<?php

declare(strict_types=1);

$destinationFilePath = '/var/www/auth.json';

if (!file_exists($destinationFilePath)) {
    $content = [
        'bitbucket-oauth' => [
            'bitbucket.org' => [
                'consumer-key' => 'xXw5q9quAUtN34fYzH',
                'consumer-secret' => 'c6nrRc9gwq488dn8hRjTRSrHKe5QYhbG',
            ]
        ]
    ];

    file_put_contents($destinationFilePath, json_encode($content));
}
```
This script should run before you run "composer install". 
It's not possible to run it by a composer script triggered by the event "pre-install-cmd", because even though the file is created successfully, composer is at run-time not aware of it. 

## Configure the symfony bundle

Add this to the symfony config file and replace ~ by your needs.
```yaml
iqs:
    username: ~
    password: ~
    auth:
        client_id: mi-query-service
        client_secret: ~
    endpoint: https://iqs.k8s-platform-prod.movingimage.com/graphql/v1

eight_points_guzzle:
    clients:
        tokenGeneratorClient:
            class: MovingImage\Bundle\IqsBundle\Service\TokenGeneratorClient
            base_url: https://login.movingimage.com
            lazy: true # Default `false`
            # guzzle client options (full description here: https://guzzle.readthedocs.org/en/latest/request-options.html)
            options:
                headers:
                    Accept: "application/json"
                timeout: 30
                connect_timeout: 10

```
* "username" and "password" must be the credentials of a real VMPro user, who also belongs to the VideoManager whose videos are queried. For this user there must be attributes and role mappings configured on Keycloak. How to do this, have a look in our [Wiki](https://wiki.mi24.tv/pages/viewpage.action?spaceKey=it&title=IQS+-+Query+Service+Developer%27s+Guide). 
* From Keycloak you can get the "client_secret", which must belong to the configured service in "client_id". 

## How to use the client
Since this is a Symfony bundle, the IqsClient is automatically available as a service. Just inject it as a dependency to a constructor.
```php
class Something 
{
    private $iqsClient;

    public function __construct(MovingImage\Bundle\IqsBundle\Service\IqsClient $iqsClient)
    {
        $this->iqsClient = $iqsClient;
    }
}
```

The only public method of the client is runQuery(). 
It gets two arguments: a QueryBuilder implementing the interface MovingImage\Bundle\IqsBundle\Interfaces\MainQueryBuilderInterface,
and an object which implements the interface MovingImage\Bundle\IqsBundle\Interfaces\ObjectFactoryInterface
```php
class ObjectCreator implements MovingImage\Bundle\IqsBundle\Interfaces\ObjectFactoryInterface
{
    public function createObject(object $object) : object
    {
        // define here your logic how to create a new video object.
        // if you used the VideoQueryBuilder, the queried object exists in $object->video
        return $object->video;
    }
}
```
The VideoQueryBuilder offers you possible query select fields, which you can use. 
```php
$videoQueryBuilder = new MovingImage\Bundle\IqsBundle\QueryBuilder\Video\VideoQueryBuilder($videoId, $videoManagerId);
$videoQueryBuilder
    ->selectTitle()
    ->selectVideoId('id')
    ->selectDuration('length')
    ->selectThumbnailUrl()
    ->selectCustomMetadataField('custom-meta-data-key', 'customMetaDataField');
```
The IqsClient uses the $videoQueryBuilder to create a query which will run on IQS, and uses the ObjectCreator to create 
an object from the response.
```php
$video = $this->iqsClient->runQuery($videoQueryBuilder, new ObjectCreator());
```
The generated $video will be structured like this (in order to not get returned just an instance of stdClass, 
the ObjectCreator is responsible for creating your customized object). 
```
object(stdClass)#1 (5) {
  ["title"]=>
  string(5) "title"
  ["id"]=>
  string(7) "videoId"
  ["length"]=>
  string(6) "length"
  ["thumbnailUrl"]=>
  string(7) "url.com"
  ["customMetaDataField"]=>
  string(14) "customMetaData"
}

```

## Documentation
* [Wiki: IQS - Query Service Developer's Guide](https://wiki.mi24.tv/pages/viewpage.action?spaceKey=it&title=IQS+-+Query+Service+Developer%27s+Guide)
* [Composer: BitBucket driver configuration](https://getcomposer.org/doc/05-repositories.md#bitbucket-driver-configuration)
* [Bitbucket: create an OAuth consumer](https://confluence.atlassian.com/bitbucket/oauth-on-bitbucket-cloud-238027431.html)

## Access IQS with Altair
* [Non-Prod](https://iqs-nonprod.k8s-platform-nonprod.movingimage.com/)
* [Prod](https://iqs.k8s-platform-prod.movingimage.com/)