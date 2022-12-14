<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Tests\App\Kernel;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

final class AdapterPassTest extends TestCase
{
    public function testProcess(): void
    {
        $kernel = new Kernel('prod', false);
        $kernel->boot();

        self::assertArrayHasKey('AdapterBundle', $kernel->getBundles());
    }

    public function testInvalidProcess(): void
    {
        $this->expectException(RuntimeException::class);

        $kernel = new Kernel('exclude_pagination', false);
        $kernel->boot();
    }
}
