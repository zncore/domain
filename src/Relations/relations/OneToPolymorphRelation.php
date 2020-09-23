<?php

namespace ZnCore\Domain\Relations\relations;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\di\Container;

class OneToPolymorphRelation extends BaseRelation implements RelationInterface
{

    /** @var string Имя связной таблицы */
    public $foreignTableAttribute;

    /** @var string Имя связного поля */
    public $foreignIdAttribute;

    /** @var array Объекты запросов для типов сущностей */
    public $queryForType = [];

    /** Связующее поле */
    public $relationAttribute;

    //public $foreignPrimaryKey = 'id';
    //public $foreignAttribute = 'id';

    private $entityManager;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->entityManager = Yii::$app->entityManager;
    }

    protected function loadRelation(&$collection)
    {
        if($this->query instanceof Query && ! in_array($this->relationEntityAttribute, $this->query->with)) {
            return;
        }
        /**
         * @var ActiveRecord[] $collection
         */
        $q = [];
        foreach ($collection as $entity) {
            $foreignId = $entity->{$this->foreignIdAttribute};
            $foreignTable = $entity->{$this->foreignTableAttribute};
            $q[$foreignTable][] = $foreignId;
        }
        foreach ($q as $type => $ids) {
            $relCollection = $this->loadRelationByType($type, $ids);
            foreach ($collection as $entity) {
                $foreignId = $entity->{$this->foreignIdAttribute};
                $foreignTable = $entity->{$this->foreignTableAttribute};
                if($foreignTable == $type) {
                    $entity->{$this->relationEntityAttribute} = $relCollection[$foreignId];
                }
            }
        }
    }

    private function loadRelationByType(string $type, array $ids) {
        $repositoryClassName = $this->entityManager->getRepositoryClassNameByTableName($type);
        /** @var CrudRepositoryInterface $repositoryInstance */
        $repositoryInstance = $this->container->get($repositoryClassName);
        $query = $this->getQueryByType($type);
        //$query->andWhere(['in', 'id', $ids]);
        $query->andWhere(['in', $this->foreignAttribute, $ids]);
        $collection = $this->loadCollection($repositoryInstance, $ids, $query);
        $collection = ArrayHelper::index($collection, $this->foreignAttribute);
        return $collection;
    }

    private function getQueryByType(string $type): \yii\db\Query
    {
        if(isset($this->queryForType[$type])) {
            return $this->queryForType[$type];
        }
        return new Query;
    }
}
