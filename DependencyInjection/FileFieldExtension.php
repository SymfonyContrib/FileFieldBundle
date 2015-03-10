<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FileFieldExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var string
     */
    public $formTemplate = 'FileFieldBundle:Form:filefield.html.twig';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('filefield.icon_uri', isset($config['icon_uri']) ? $config['icon_uri'] : null);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles      = $container->getParameter('kernel.bundles');
        $loadTemplate = $container->hasParameter('filefield.load_form_template') ? $container->getParameter('filefield.load_form_template') : true;

        // Configure TwigBundle
        if ($loadTemplate && isset($bundles['TwigBundle'])) {
            $this->configureTwigBundle($container);
        }
    }

    /**
     * Adds the confirm form template to the TwigBundle configuration.
     *
     * @param ContainerBuilder $container The service container
     */
    private function configureTwigBundle(ContainerBuilder $container)
    {
        // Get the twig configurations.
        $name       = 'twig';
        $configs    = $container->getExtensionConfig($name);
        $formConfig = [
            'form' => [
                'resources' => [$this->formTemplate],
            ],
        ];

        // Find any existing configurations and add to it them so when the
        // configs are merged they do not overwrite each other.
        foreach ($configs as $config) {
            if (isset($config['form'])) {
                $formConfig = ['form' => $config['form']];

                $formConfig['form']['resources'][] = $this->formTemplate;
                break;
            }
        }

        // Prepend our configuration.
        $container->prependExtensionConfig($name, $formConfig);
    }

    public function getAlias()
    {
        return 'filefield';
    }
}
