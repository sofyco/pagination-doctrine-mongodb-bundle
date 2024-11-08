<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Adapter\Query;

use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Doctrine\ODM\MongoDB\Query\Builder;
use MongoDB\BSON\Regex;
use Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Adapter\AbstractAdapter;
use Sofyco\Pagination\Enum\Filter;
use Sofyco\Pagination\Query;

final class BuilderAdapter extends AbstractAdapter
{
    public function __construct(private readonly Builder $builder)
    {
    }

    protected function addFilters(Query $query): void
    {
        foreach ($query->filters as $fieldName => $operators) {
            foreach ($operators as $operator => $value) {
                if ($operator === Filter::LIKE->value) {
                    $value = new Regex(pattern: $value, flags: 'i');
                }

                $this->builder->addAnd(
                    $this->builder->expr()->field($fieldName)->operator($this->getOperator($operator), $value)
                );
            }
        }
    }

    protected function addSorting(Query $query): void
    {
        foreach ($query->sorting as $fieldName => $direction) {
            $this->builder->sort($fieldName, $direction);
        }
    }

    protected function addPagination(Query $query): void
    {
        if ($query->skip > 0) {
            $this->builder->skip($query->skip);
        }

        if ($query->limit > 0) {
            $this->builder->limit($query->limit);
        }
    }

    protected function getCount(): int
    {
        $count = (clone $this->builder)->count()->getQuery()->execute();

        return \is_int($count) ? $count : 0;
    }

    protected function getItems(): Iterator
    {
        return $this->builder->getQuery()->getIterator();
    }
}
