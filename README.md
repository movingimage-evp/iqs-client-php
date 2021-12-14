[![Build Status](https://github.com/movingimage-evp/iqs-client-php/actions/workflows/verify-pull-request.yml/badge.svg)](https://github.com/movingimage-evp/iqs-client-php/actions/workflows/verify-pull-request.yml)

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
Get the credentials of this OAuth consumer from https://pdb.mi24.tv/app/#/secret/826/general
```json
{
  "bitbucket-oauth": {
    "bitbucket.org": {
      "consumer-key": "12345",
      "consumer-secret": "abcdef"
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
                'consumer-key' => '12345',
                'consumer-secret' => 'abcdef',
            ]
        ]
    ];

    file_put_contents($destinationFilePath, json_encode($content));
}
```
This script should run before you run "composer install". 
It's not possible to run it by a composer script triggered by the event "pre-install-cmd", because even though the file is created successfully, composer is at run-time not aware of it. 

## Configure the symfony bundle
* Instances creation should be handled inside projects which use the library.  
* For `MovingImage\Bundle\IqsBundle\Service\TokenGeneratorClient` : `base_url` should be set as full auth url (for requesting token). 
* "username" and "password" must be the credentials of a real VMPro user, who also belongs to the VideoManager whose videos are queried. For this user there must be attributes and role mappings configured on Keycloak. How to do this, have a look in our [Wiki](https://wiki.mi24.tv/pages/viewpage.action?spaceKey=it&title=IQS+-+Query+Service+Developer%27s+Guide). 
* From Keycloak you can get the "client_secret", which must belong to the configured service in "client_id". 

## How to use the client
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

### Here is an example how to build a query with the VideoQueryBuilder
Following query must be built.
```
{
  video(
    id: "-PGfCEnaJMapJwF3vg4KnT"
    vmId: 765
    env: "prod"
    lang: "(DEFAULT)"
  ) {
    videoId
    title
    userId: customMetadata(field: "04-User-ID") {
      ... on MetadataString {
        value
      }
    }
    related (left: "customMetadata.A-01-Match-ID", predicate: EQUALS, page: {}) {
      videos {
        videoId
        length
      }
    }
  }
}
```
Use the VideoQueryBuilder to build a query like the given example.
```php
$videoQueryBuilder = new \MovingImage\Bundle\IqsBundle\QueryBuilder\Video\VideoQueryBuilder('-PGfCEnaJMapJwF3vg4KnT', 765);
$relationExpression = new \MovingImage\Bundle\IqsBundle\QueryBuilder\Relation\RelationExpression('A-01-Match-ID', true);
$relatedVideosQueryBuilder = new \MovingImage\Bundle\IqsBundle\QueryBuilder\Video\RelatedVideosQueryBuilder();
$relatedVideosQueryBuilder->selectVideoId()->selectDuration('length');

$videoQueryBuilder
    ->selectVideoId()
    ->selectTitle()
    ->selectCustomMetadataField('04-User-ID', 'userId')
    ->selectRelatedVideos($relatedVideosQueryBuilder, [$relationExpression]);
```

## Documentation
* [Wiki: IQS - Query Service Developer's Guide](https://wiki.mi24.tv/pages/viewpage.action?spaceKey=it&title=IQS+-+Query+Service+Developer%27s+Guide)
* [Composer: BitBucket driver configuration](https://getcomposer.org/doc/05-repositories.md#bitbucket-driver-configuration)
* [Bitbucket: create an OAuth consumer](https://confluence.atlassian.com/bitbucket/oauth-on-bitbucket-cloud-238027431.html)

## Access IQS with Altair
* [Non-Prod](https://iqs-qa-westeurope.movingimage.services/altair)
* [Prod](https://iqs-prod-westeurope.movingimage.services/altair)