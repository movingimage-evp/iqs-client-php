<?php

declare(strict_types=1);

namespace MovingImage\Bundle\IqsBundle\DependencyInjection;

use MovingImage\Bundle\IqsBundle\Service\GraphQLClientFactory;
use MovingImage\Bundle\IqsBundle\Service\TokenGenerator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 *
 * @codeCoverageIgnore
 */
class IqsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureTokenGenerator($container, $config);
        $this->configureGraphQLClient($container, $config);
    }

    private function configureTokenGenerator(ContainerBuilder $container, array $config): void
    {
        $tokenGeneratorDefinition = $container->getDefinition(TokenGenerator::class);
        $tokenGeneratorDefinition->setArgument('$username', $config['username']);
        $tokenGeneratorDefinition->setArgument('$password', $config['password']);
        $tokenGeneratorDefinition->setArgument('$clientSecret', $config['auth']['client_secret']);
        $tokenGeneratorDefinition->setArgument('$clientId', $config['auth']['client_id']);
    }

    private function configureGraphQLClient(ContainerBuilder $container, array $config): void
    {
        $iqsClientDefinition = $container->getDefinition(GraphQLClientFactory::class);
        $iqsClientDefinition->setArgument('$endpoint', $config['endpoint']);
    }
}
