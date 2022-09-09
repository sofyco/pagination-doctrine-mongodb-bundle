<?php declare(strict_types=1);

namespace Sofyco\Bundle\Pagination\Doctrine\MongoDB\AdapterBundle\Tests\App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: 'products')]
final class Product
{
    #[MongoDB\Id]
    public string $id;

    #[MongoDB\Field]
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
