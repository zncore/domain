<?php

namespace ZnCore\Domain\Relations\libs;

use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\relations\RelationInterface;
use ZnCore\Domain\Relations\repositories\BaseCommonRepository;
use InvalidArgumentException;
use Yii;
use yii\db\ActiveQuery;

class RelationLoader
{
    /** @var BaseCommonRepository */
    private $repository;
    private $relations;

    public function getRepository()/*: BaseCommonRepository*/
    {
        return $this->repository;
    }

    public function setRepository(/*BaseCommonRepository*/ $repository): void
    {
        $this->repository = $repository;
    }

    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    public function relations()
    {
        if ($this->relations) {
            return $this->relations;
        }
        if ($this->repository) {
            return $this->repository->relations();
        }
    }

    public function loadRelations(&$collection, Query $query)
    {
        $relations = $this->relations();
        $relations = $this->prepareRelations($relations);
        $relations = ArrayHelper::index($relations, 'name');

        if ($query->hasParam('with')) {
            $with = $query->getParam(Query::WITH);
            foreach ($with as $withItem) {
                $relParts = explode('.', $withItem);
                $attribute = $relParts[0];
                unset($relParts[0]);
                $relParts = array_values($relParts);
                if (empty($relations[$attribute])) {
                    throw new InvalidArgumentException('Relation "' . $attribute . '" not defined in repository "' . get_class($this->repository) . '"!');
                }
                /** @var RelationInterface $relation */
                $relation = $relations[$attribute];
                $relation = $this->ensureRelation($relation);

                if (is_object($relation)) {
                    if ($relParts) {
                        $nestedWith = implode('.', $relParts);
                        $relation->query = $relation->query ?: new Query;
                        $relation->query->with([$nestedWith]);
                        //dd($relation->query);
                    }
                }
                $this->runRelation($relation, $collection);
            }
        }
    }

    private function prepareRelations(array $relations)
    {
        foreach ($relations as &$relation) {
            if (empty($relation['name'])) {
                $relation['name'] = $relation['relationEntityAttribute'];
            }
        }
        return $relations;
    }

    private function runRelation($relation, /*array*/ &$collection)
    {
        $relation->run($collection);
    }

    private function ensureRelation($relation)
    {
        if ($relation instanceof RelationInterface) {

        } elseif (is_array($relation) || is_string($relation)) {
            $relation = ClassHelper::createObject($relation);
        } else {
            throw new InvalidArgumentException('Definition of relation not correct!');
        }
        return $relation;
    }

}
