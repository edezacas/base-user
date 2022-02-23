<?php


namespace EDC\BaseUserBundle\DependencyInjection\Compiler;


use EDC\BaseUserBundle\Security\UserChecker;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserCheckerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $firewallName = $container->getParameter('edc_base_user.firewall_name');

        if ($container->has('security.user_checker.'.$firewallName)) {
            $container->getDefinition(UserChecker::SERVICE_NAME)
                ->setDecoratedService('security.user_checker')
                ->replaceArgument(0, new Reference(UserChecker::SERVICE_NAME.'.inner'));
        }
    }
}
