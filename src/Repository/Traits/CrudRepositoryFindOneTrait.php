<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Base\Text\Helpers\Inflector;
use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Domain\Traits\FindOneTrait;
use ZnCore\Domain\Entity\Exceptions\AlreadyExistsException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnLib\Components\I18Next\Facades\I18Next;

trait CrudRepositoryFindOneTrait
{

    use FindOneTrait;

    protected $primaryKey = ['id'];

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        if (empty($id)) {
            throw (new InvalidMethodParameterException('Empty ID'))
                ->setParameterName('id');
        }
        $query = $this->forgeQuery($query);
        $query->where($this->primaryKey[0], $id);
        $entity = $this->one($query);
        return $entity;
    }

    public function one(Query $query = null)
    {
        $query->limit(1);
        $collection = $this->all($query);
        if ($collection->count() < 1) {
            throw new NotFoundException('Not found entity!');
        }
        $entity = $collection->first();
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_READ_ENTITY);
        return $entity;
    }

    public function checkExists(EntityIdInterface $entity): void
    {
        try {
            $existedEntity = $this->oneByUnique($entity);
            if ($existedEntity) {
                $message = I18Next::t('core', 'domain.message.entity_already_exist');
                $e = new AlreadyExistsException($message);
                $e->setEntity($existedEntity);
                throw $e;
            }
        } catch (NotFoundException $e) {
        }
    }


    public function oneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        $unique = $entity->unique();
        if (!empty($unique)) {
            foreach ($unique as $uniqueConfig) {
                $oneEntity = $this->oneByUniqueGroup($entity, $uniqueConfig);
                if ($oneEntity) {
                    return $oneEntity;
                }
            }
        }
        throw new NotFoundException();
    }

    private function oneByUniqueGroup(UniqueInterface $entity, $uniqueConfig): ?EntityIdInterface
    {
        $query = new Query();
        foreach ($uniqueConfig as $uniqueName) {
            $value = EntityHelper::getValue($entity, $uniqueName);
            if ($value === null) {
                return null;
            }
            $query->where(Inflector::underscore($uniqueName), $value);
        }
        $all = $this->all($query);
        if ($all->count() > 0) {
            return $all->first();
        }
        return null;
    }
}
