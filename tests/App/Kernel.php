<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Tests\App;

use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\AdapterBundle;
use Sofyco\Bundle\PaginationBundle\PaginationBundle;
use Sofyco\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineMongoDBBundle();
        yield new AdapterBundle();

        if ('exclude_pagination' !== $this->getEnvironment()) {
            yield new PaginationBundle();
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['test' => true]);

        $container->extension('doctrine_mongodb', [
            'connections' => [
                'default' => [
                    'server' => '%env(resolve:MONGODB_URL)%',
                ],
            ],
            'default_database' => 'test_database',
            'document_managers' => [
                'default' => [
                    'auto_mapping' => true,
                    'mappings' => [
                        'Document' => [
                            'type' => 'attribute',
                            'dir' => __DIR__ . '/Document',
                            'prefix' => __NAMESPACE__ . '\Document',
                        ],
                    ],
                ],
            ],
        ]);

        $container->services()->set(Paginator::class, Paginator::class)->public()->autowire();
    }
}
