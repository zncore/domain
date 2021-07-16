<?php

namespace ZnCore\Domain\Relations\relations;

use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\Query\Where;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;

abstract class BaseRelation implements RelationInterface
{

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
    //private $cache = [];

    abstract protected function loadRelation(&$collection);

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(Collection $collection)
    {
        $this->loadRelation($collection);
        $collection = $this->prepareCollection($collection);
    }

    protected function prepareCollection(Collection $collection): Collection {
        if($this->prepareCollection) {
            $collection = call_user_func($this->prepareCollection, $collection);
        }
        return $collection;
    }

    protected function loadRelationByIds(array $ids) {
        $foreignRepositoryInstance = $this->getRepositoryInstance();
        //$primaryKey = $foreignRepositoryInstance->primaryKey()[0];
        $query = $this->getQuery();
        $query->whereNew(new Where($this->foreignAttribute, $ids));
        //$query->andWhere(['in', ]);
        return $this->loadCollection($foreignRepositoryInstance, $ids, $query);
    }

    protected function loadCollection(/*CrudRepositoryInterface*/ $foreignRepositoryInstance, array $ids, Query $query): Collection {
        // todo: костыль, надо проверить наверняка
        if (get_called_class() != OneToManyRelation::class) {
            $query->limit(count($ids));
        }

        $collection = $foreignRepositoryInstance->all($query);
        return $collection;

        /*$cacheKey = serialize([$query, $foreignRepositoryInstance]);
        $callback = function () use ($query, $foreignRepositoryInstance) {

        };
        return $callback();*/
        //return \Yii::$app->cache->getOrSet($cacheKey, $callback);
    }

    protected function getQuery(): Query {
        return $this->query ? $this->query : new Query;
    }

    protected function getRepositoryInstance()/*: CrudRepositoryInterface*/ {
        return $this->container->get($this->foreignRepositoryClass);
    }

}
