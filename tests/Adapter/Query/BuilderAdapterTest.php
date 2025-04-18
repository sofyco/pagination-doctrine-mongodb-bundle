<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Tests\Adapter\Query;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Tests\App\Document\Product;
use Sofyco\Pagination\Enum\Filter;
use Sofyco\Pagination\Enum\Sort;
use Sofyco\Pagination\Paginator;
use Sofyco\Pagination\Query;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BuilderAdapterTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::getDocumentManager()->createQueryBuilder()->remove(Product::class)->getQuery()->execute();

        foreach (\range(1, 100) as $i) {
            self::getDocumentManager()->persist(new Product(\sprintf('product_%d', $i)));
        }

        self::getDocumentManager()->flush();
    }

    public function testEmptyResult(): void
    {
        $query = new Query(
            filters: ['name' => [Filter::EQUAL->value => 'product_101']],
        );

        $result = self::getPaginator()->paginate(self::getQueryBuilder(), $query);

        self::assertEmpty($result->count);
        self::assertEmpty($result->items);
    }

    public function testPaginator(): void
    {
        $query = new Query(
            filters: ['name' => [Filter::GREATER_THEN_OR_EQUAL->value => 'product_90']],
            sorting: ['name' => Sort::DESC->value],
            skip: 5,
            limit: 3,
        );

        $result = self::getPaginator()->paginate(self::getQueryBuilder(), $query);

        self::assertSame(10, $result->count);

        $expected = ['product_94', 'product_93', 'product_92'];

        /** @var Product $product */
        foreach ($result->items as $i => $product) {
            self::assertSame($expected[$i], $product->name);
        }
    }

    public function testPaginatorLikeOperator(): void
    {
        $query = new Query(
            filters: ['name' => [Filter::LIKE->value => 'duct_9']],
            sorting: ['name' => Sort::ASC->value],
            limit: 100,
        );

        $result = self::getPaginator()->paginate(self::getQueryBuilder(), $query);

        self::assertSame(11, $result->count);

        $expected = [
            'product_9',
            'product_90',
            'product_91',
            'product_92',
            'product_93',
            'product_94',
            'product_95',
            'product_96',
            'product_97',
            'product_98',
            'product_99',
        ];

        /** @var Product $product */
        foreach ($result->items as $i => $product) {
            self::assertSame($expected[$i], $product->name);
        }
    }

    private static function getPaginator(): Paginator
    {
        return self::getContainer()->get(Paginator::class); // @phpstan-ignore-line
    }

    private static function getDocumentManager(): DocumentManager
    {
        return self::getContainer()->get(DocumentManager::class); // @phpstan-ignore-line
    }

    private static function getQueryBuilder(): Builder
    {
        return self::getDocumentManager()->createQueryBuilder(Product::class);
    }
}
