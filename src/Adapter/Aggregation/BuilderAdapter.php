<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Adapter\Aggregation;

use Doctrine\ODM\MongoDB\Aggregation\Builder;
use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Adapter\AbstractAdapter;
use Sofyco\Pagination\Query;

final class BuilderAdapter extends AbstractAdapter
{
    private const array OPTIONS = [
        'allowDiskUse' => true,
    ];

    public function __construct(private readonly Builder $builder)
    {
    }

    public function addFilters(Query $query): void
    {
        $match = $this->builder->match();

        foreach ($query->filters as $fieldName => $operators) {
            foreach ($operators as $operator => $value) {
                $match->addAnd($match->expr()->field($fieldName)->operator($this->getOperator($operator), $value));
            }
        }
    }

    public function addSorting(Query $query): void
    {
        $this->builder->sort($query->sorting);
    }

    public function addPagination(Query $query): void
    {
        if ($query->skip > 0) {
            $this->builder->skip($query->skip);
        }

        if ($query->limit > 0) {
            $this->builder->limit($query->limit);
        }
    }

    public function getCount(): int
    {
        $builder = clone $this->builder;
        $result = (array) $builder->count('count')->getAggregation(self::OPTIONS)->getIterator()->current();

        if (isset($result['count']) && \is_int($result['count'])) {
            return $result['count'];
        }

        return 0;
    }

    public function getItems(): Iterator
    {
        return $this->builder->getAggregation(self::OPTIONS)->getIterator();
    }
}
