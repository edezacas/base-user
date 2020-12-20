<?php

namespace DigitalAscetic\BaseUserBundle\Tests;

use DigitalAscetic\BaseUserBundle\DigitalAsceticBaseUserBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class BaseUserTestKernel extends Kernel
{
    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct('test', false);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new JMSSerializerBundle(),
            new DigitalAsceticBaseUserBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $confDir = $this->getProjectDir().'/tests/config';
        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');

    }
}