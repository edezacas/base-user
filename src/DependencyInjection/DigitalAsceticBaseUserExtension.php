<?php


namespace DigitalAscetic\BaseUserBundle\DependencyInjection;


use DigitalAscetic\BaseUserBundle\Controller\ResetController;
use DigitalAscetic\BaseUserBundle\Controller\SecurityController;
use DigitalAscetic\BaseUserBundle\Security\UserProvider;
use DigitalAscetic\BaseUserBundle\Service\ResetPasswordService;
use DigitalAscetic\BaseUserBundle\Service\UserPasswordEncoderService;
use DigitalAscetic\BaseUserBundle\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

class DigitalAsceticBaseUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $userClass = $config['user_class'];

        $userService = new Definition(UserService::class);
        $userService->addArgument(new Reference('doctrine.orm.entity_manager'));
        $userService->addArgument($userClass);
        $container->setDefinition(UserService::SERVICE_NAME, $userService);
        $container->setAlias(UserService::class, UserService::SERVICE_NAME);

        $resetService = new Definition(ResetPasswordService::class);
        $resetService->addArgument(new Reference('doctrine.orm.entity_manager'));
        $resetService->addArgument(new Reference('event_dispatcher'));
        $resetService->addArgument($userClass);
        $container->setDefinition(ResetPasswordService::SERVICE_NAME, $resetService);
        $container->setAlias(ResetPasswordService::class, ResetPasswordService::SERVICE_NAME);

        $passEncoderService = new Definition(UserPasswordEncoderService::class);
        $userService->addArgument(new Reference('security.password_encoder'));
        $passEncoderService->addArgument($userClass);
        $container->setDefinition(UserPasswordEncoderService::SERVICE_NAME, $passEncoderService);
        $container->setAlias(UserPasswordEncoderService::class, UserPasswordEncoderService::SERVICE_NAME);

        $userProvider = new Definition(UserProvider::class);
        $userProvider->addArgument(new Reference(UserService::SERVICE_NAME));
        $container->setDefinition(UserProvider::SERVICE_NAME, $userProvider);
        $container->setAlias(UserProvider::class, UserProvider::SERVICE_NAME);

        $container->register('digital_ascetic.base_user.security_controller', SecurityController::class)
            ->setPublic(true)
            ->addTag('controller.service_arguments')
            ->addArgument(new Reference('security.authentication_utils'))
            ->addArgument(new Reference('security.csrf.token_manager', ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->addMethodCall('setContainer', [new Reference('service_container')]);

        $container->register('digital_ascetic.base_user.reset_controller', ResetController::class)
            ->setPublic(true)
            ->addTag('controller.service_arguments')
            ->addArgument(new Reference(UserService::SERVICE_NAME))
            ->addArgument(new Reference(ResetPasswordService::SERVICE_NAME))
            ->addMethodCall('setContainer', [new Reference('service_container')]);
    }

    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['JMSSerializerBundle'])) {
            $serializerConfig = array(
                'metadata' => array(
                    'directories' => array(
                        'DigitalAsceticBaseUserBundle' => array(
                            'namespace_prefix' => 'DigitalAscetic\BaseUserBundle\Entity',
                            'path' => '@DigitalAsceticBaseUserBundle/Resources/serializer',
                        ),
                    ),
                ),
            );

            $container->prependExtensionConfig('jms_serializer', $serializerConfig);
        }
    }
}