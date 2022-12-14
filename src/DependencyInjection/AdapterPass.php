<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\DependencyInjection;

use Doctrine\ODM\MongoDB;
use Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Adapter;
use Sofyco\Pagination\Adapter\FactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AdapterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition(FactoryInterface::class)) {
            return;
        }

        $factory = $container->getDefinition(FactoryInterface::class);
        $factory->addMethodCall('add', [MongoDB\Query\Builder::class, Adapter\Query\BuilderAdapter::class]);
        $factory->addMethodCall('add', [MongoDB\Aggregation\Builder::class, Adapter\Aggregation\BuilderAdapter::class]);
    }
}
