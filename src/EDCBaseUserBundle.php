<?php


namespace EDC\BaseUserBundle;


use EDC\BaseUserBundle\DependencyInjection\Compiler\UserCheckerCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EDCBaseUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new UserCheckerCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }

}
