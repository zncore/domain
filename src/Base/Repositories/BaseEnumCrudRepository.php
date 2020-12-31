<?php

namespace ZnCore\Domain\Base\Repositories;

use Illuminate\Support\Collection;
use ZnCore\Base\Helpers\EnumHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\Query\Where;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Domain\Interfaces\Repository\ReadOneInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Libs\EntityManager;
use ZnCore\Domain\Libs\Query;

abstract class BaseEnumCrudRepository implements RepositoryInterface, GetEntityClassInterface, ReadAllInterface, ReadOneInterface
{

    private $em;

    abstract public function enumClass(): string;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function all(Query $query = null)
    {
        $items = $this->getItems();
        if($query) {
            $items = $this->processItems($items, $query);
        }
        return $this->em->createEntityCollection($this->getEntityClass(), $items);
    }

    protected function processItems(array $items, Query $query): array
    {
        $collection = new Collection($items);
        /** @var Where[] $where */
        $where = $query->getParam(Query::WHERE_NEW);
        if ($where) {
            foreach ($where as $condition) {
                $values = ArrayHelper::toArray($condition->value);
                $resultCollection = new Collection();
                foreach ($values as $value) {
                    $filteredCollection = $collection->where($condition->column, $condition->operator, $value);
                    $resultCollection = $resultCollection->concat($filteredCollection);
                }
                $collection = $resultCollection;
            }
        }
        return $collection->toArray();
    }

    protected function getItems(): array
    {
        $all = EnumHelper::all($this->enumClass());
        $labels = EnumHelper::getLabels($this->enumClass());
        $items = [];
        foreach ($all as $name => $id) {
            $items[] = [
                'id' => $id,
                'name' => mb_strtolower($name),
                'title' => $labels[$id],
            ];
        }
        return $items;
    }

    public function count(Query $query = null): int
    {
        $collection = $this->all($query);
        return $collection->count();
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        $collection = $this->all($query);
        return $collection->first();
    }
}
