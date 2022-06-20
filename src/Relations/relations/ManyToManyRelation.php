<?php

namespace ZnCore\Domain\Relations\relations;

use App\Organization\Domain\Entities\LanguageEntity;
use App\Organization\Domain\Entities\OrganizationEntity;
use App\Organization\Domain\Interfaces\Repositories\LanguageRepositoryInterface;
use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\Query\Entities\Where;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnCore\Base\Libs\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;
use yii\di\Container;

class ManyToManyRelation extends BaseRelation implements RelationInterface
{

    /** Связующее поле */
    public $relationAttribute;

    /** @var string Имя связи, указываемое в методе with.
     * Если пустое, то берется из атрибута relationEntityAttribute
     */
    public $name;

    /** @var string Имя поля, в которое записывать вложенную сущность */
    public $relationEntityAttribute;

    /** @var string Имя первичного ключа связной таблицы */
    public $foreignAttribute = 'id';

    /** @var string Имя класса связного репозитория */
    public $foreignRepositoryClass;

    /** @var array Условие для присваивания связи, иногда нужно для полиморических связей */
    public $condition = [];

    /** @var callable Callback-метод для пост-обработки коллекции из связной таблицы */
    public $prepareCollection;

    /** @var Query Объект запроса для связного репозитория */
    public $query;
    protected $container;

    public $viaRepositoryClass;
    public $viaSourceAttribute;
    public $viaTargetAttribute;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(Collection $collection)
    {
        $this->loadRelation($collection);
        $collection = $this->prepareCollection($collection);
    }

    protected function prepareCollection(Collection $collection) {
        if($this->prepareCollection) {
            call_user_func($this->prepareCollection, $collection);
        }
    }

    protected function loadRelationByIds(array $ids) {
        $foreignRepositoryInstance = $this->getRepositoryInstance();
        $query = $this->getQuery();
        $query->whereNew(new Where($this->foreignAttribute, $ids));
        return $this->loadCollection($foreignRepositoryInstance, $ids, $query);
    }

    protected function loadViaByIds(array $ids) {
        $foreignRepositoryInstance = $this->getViaRepositoryInstance();
        $query = $this->getQuery();
        $query->whereNew(new Where($this->viaSourceAttribute, $ids));
        return $this->loadCollection($foreignRepositoryInstance, $ids, $query);
    }

    protected function getQuery(): Query {
        return $this->query ? $this->query : new Query;
    }

    protected function getRepositoryInstance()/*: CrudRepositoryInterface*/ {
        return $this->container->get($this->foreignRepositoryClass);
    }

    protected function getViaRepositoryInstance()/*: CrudRepositoryInterface*/ {
        return $this->container->get($this->viaRepositoryClass);
    }

    protected function loadRelation(Collection $collection)
    {
        $ids = EntityHelper::getColumn($collection, $this->relationAttribute);
        $ids = array_unique($ids);
        $viaCollection = $this->loadViaByIds($ids);
        $targetIds = EntityHelper::getColumn($viaCollection, $this->viaTargetAttribute);
        $targetIds = array_unique($targetIds);
        $foreignCollection = $this->loadRelationByIds($targetIds);
        $foreignCollection = EntityHelper::indexingCollection($foreignCollection, 'id');
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $indexedCollection = EntityHelper::indexingCollection($collection, 'id');

        $result = [];
        foreach ($viaCollection as $viaEntity) {
            $targetRelationIndex = $propertyAccessor->getValue($viaEntity, $this->viaTargetAttribute);
            $sourceIndex = $propertyAccessor->getValue($viaEntity, $this->viaSourceAttribute);
            $sourceEntity = $indexedCollection[$sourceIndex];
            $targetRelationEntity = $foreignCollection[$targetRelationIndex];
            $result[$sourceIndex][] = $targetRelationEntity;
        }
        foreach ($collection as $entity) {
            $sourceIndex = $propertyAccessor->getValue($entity, 'id');
            if (isset($result[$sourceIndex])) {
                $value = $result[$sourceIndex];
                $value = $this->getValueFromPath($value);
                $propertyAccessor->setValue($entity, $this->relationEntityAttribute, new Collection($value));
            }
        }
    }

    protected function loadCollection(ReadAllInterface $foreignRepositoryInstance, array $ids, Query $query): Collection {
        //$query->limit(count($ids));
        $collection = $foreignRepositoryInstance->all($query);
        return $collection;
    }
}
