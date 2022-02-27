<?php


namespace EDC\BaseUserBundle\DependencyInjection;


use EDC\BaseUserBundle\Controller\ResetController;
use EDC\BaseUserBundle\Controller\SecurityController;
use EDC\BaseUserBundle\Security\UserChecker;
use EDC\BaseUserBundle\Security\UserProvider;
use EDC\BaseUserBundle\Service\ResetPasswordService;
use EDC\BaseUserBundle\Service\UserPasswordEncoderService;
use EDC\BaseUserBundle\Service\UserService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class EDCBaseUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $userClass = $config['user_class'];
        $firewallName = $config['firewall_name'];
        $userEnabled = $config['user_enabled'];

        $container->setParameter('edc_base_user.firewall_name', $firewallName);

        $passEncoderService = new Definition(UserPasswordEncoderService::class);
        $passEncoderService->addArgument(new Reference('security.user_password_hasher'));
        $passEncoderService->addArgument($userClass);
        $container->setDefinition(UserPasswordEncoderService::SERVICE_NAME, $passEncoderService);
        $container->setAlias(UserPasswordEncoderService::class, UserPasswordEncoderService::SERVICE_NAME);

        $userService = new Definition(UserService::class);
        $userService->addArgument(new Reference('doctrine.orm.entity_manager'));
        $userService->addArgument(new Reference(UserPasswordEncoderService::SERVICE_NAME));
        $userService->addArgument($userClass);
        $userService->addArgument($userEnabled);
        $userService->setPublic(false);
        $container->setDefinition(UserService::SERVICE_NAME, $userService);
        $container->setAlias(UserService::class, UserService::SERVICE_NAME);

        $resetService = new Definition(ResetPasswordService::class);
        $resetService->addArgument(new Reference('doctrine.orm.entity_manager'));
        $resetService->addArgument(new Reference('event_dispatcher'));
        $resetService->addArgument(new Reference(UserPasswordEncoderService::SERVICE_NAME));
        $resetService->addArgument($userClass);
        $container->setDefinition(ResetPasswordService::SERVICE_NAME, $resetService);
        $container->setAlias(ResetPasswordService::class, ResetPasswordService::SERVICE_NAME);

        $userProvider = new Definition(UserProvider::class);
        $userProvider->addArgument(new Reference(UserService::SERVICE_NAME));
        $container->setDefinition(UserProvider::SERVICE_NAME, $userProvider);
        $container->setAlias(UserProvider::class, UserProvider::SERVICE_NAME);

        $userDoctrineConfig = array(
            'user_enabled' => $userEnabled,
        );

        $userChecker = new Definition(UserChecker::class);
        $userChecker->addArgument(new Reference('security.user_checker'));
        $container->setDefinition(UserChecker::SERVICE_NAME, $userChecker);
        $container->setAlias(UserChecker::class, UserChecker::SERVICE_NAME);

        $container->register('edc.base_user.security_controller', SecurityController::class)
            ->setPublic(true)
            ->addTag('controller.service_arguments')
            ->addArgument(new Reference('security.authentication_utils'))
            ->addArgument(new Reference('security.csrf.token_manager', ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->addMethodCall('setContainer', [new Reference('service_container')]);

        $container->register('edc.base_user.reset_controller', ResetController::class)
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
                        'EDCBaseUserBundle' => array(
                            'namespace_prefix' => 'EDC\BaseUserBundle\Entity',
                            'path' => '@EDCBaseUserBundle/Resources/serializer',
                        ),
                    ),
                ),
            );

            $container->prependExtensionConfig('jms_serializer', $serializerConfig);
        }
    }
}
