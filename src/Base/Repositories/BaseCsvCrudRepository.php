<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Enums\OperatorEnum;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Traits\EntityManagerTrait;
use ZnDatabase\Base\Domain\Traits\MapperTrait;

abstract class BaseCsvCrudRepository
{

    use EntityManagerTrait;
    use MapperTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    abstract public function getFile(): string;

    abstract public function hasHead(): bool;

    abstract public function headNames(): array;

    protected function lineToAssoc(string $line): array
    {
        $array = str_getcsv($line);
        $entity = $this->getEntityManager()->createEntity($this->getEntityClass());
        $entityAttributes = EntityHelper::getAttributeNames($entity);
        $item = [];
        foreach ($this->headNames() as $index => $name) {
            $item[$name] = ArrayHelper::getValue($array, [$index]);
        }
        $item = ArrayHelper::extractByKeys($item, $entityAttributes);
        return $item;
    }

    public function isAllow(Query $query, array $data): bool {
        $allow = true;
        foreach ($query->getWhereNew() as $where) {
            if($where->operator == OperatorEnum::EQUAL) {
                if($where->value != $data[$where->column]) {
                    $allow = false;
                }
            }
        }
        return $allow;
    }

    public function count(Query $query = null): int
    {
        $count = 0;
        $query = self::forgeQuery($query);
        $fileName = $this->getFile();
        $file = new \SplFileObject($fileName);
        /*if (!$query->getLimit() || $query->getLimit() > 100) {
            throw new \Exception('limit infinity');
        }*/
        $offset = $query->getOffset();
        if ($this->hasHead()) {
            $offset++;
        }
        $fileIterator = new \LimitIterator($file, $offset);
        $limit = $query->getLimit();
        foreach ($fileIterator as $index => $line) {
            $line = trim($line);
            if (!empty($line)) {
                $count++;
//                $data = $this->lineToAssoc($line);
//                $data['id'] = $index;

                /*$allow = $this->isAllow($query, $data);
                if($allow) {
                    if($limit !== null) {
                        $limit--;
                    }
                    $count++;
                    if($limit !== null && $limit <= 0) {
                        break;
                    }
                }*/
            }
        }
        return $count;
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        $query = new Query();
        $query->limit(1);
        $query->where('id', $id);
        $collection = $this->all($query);
        if($collection->isEmpty()) {
            throw new NotFoundException();
        }
        return $collection->first();
    }

    /**
     * @param Query|null $query
     * @return Query
     */
    protected function forgeQuery(Query $query = null)
    {
        $query = Query::forge($query);
//        $this->dispatchQueryEvent($query, EventEnum::BEFORE_FORGE_QUERY);
        return $query;
    }

    public function create(EntityIdInterface $entity)
    {
        // TODO: Implement create() method.
    }

    public function update(EntityIdInterface $entity)
    {
        // TODO: Implement update() method.
    }

    public function deleteById($id)
    {
        // TODO: Implement deleteById() method.
    }

    public function deleteByCondition(array $condition)
    {
        // TODO: Implement deleteByCondition() method.
    }

}
