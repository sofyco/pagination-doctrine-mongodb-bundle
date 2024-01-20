<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Adapter;

use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Sofyco\Pagination\Adapter\AdapterInterface;
use Sofyco\Pagination\Enum\Filter;
use Sofyco\Pagination\Query;
use Sofyco\Pagination\Result;

abstract class AbstractAdapter implements AdapterInterface
{
    private const array OPERATORS_MAPPING = [
        Filter::EQUAL->value => '$eq',
        Filter::NOT_EQUAL->value => '$ne',
        Filter::IN->value => '$in',
        Filter::NOT_IN->value => '$nin',
        Filter::LESS_THEN->value => '$lt',
        Filter::LESS_THEN_OR_EQUAL->value => '$lte',
        Filter::GREATER_THEN->value => '$gt',
        Filter::GREATER_THEN_OR_EQUAL->value => '$gte',
        Filter::LIKE->value => '$regex',
        Filter::IS_NULL->value => '$exists',
        Filter::NOT_NULL->value => '$exists',
    ];

    public function getResult(Query $query): Result
    {
        $this->addFilters($query);

        if (0 === $count = $this->getCount()) {
            $items = [];
        } else {
            $this->addSorting($query);
            $this->addPagination($query);

            $items = $this->getItems();
        }

        return new Result(skip: $query->skip, limit: $query->limit, count: $count, items: $items);
    }

    protected function getOperator(string $name): string
    {
        return self::OPERATORS_MAPPING[$name];
    }

    abstract protected function addFilters(Query $query): void;

    abstract protected function addSorting(Query $query): void;

    abstract protected function addPagination(Query $query): void;

    abstract protected function getCount(): int;

    abstract protected function getItems(): Iterator;
}
