<?php

namespace ZnCore\Domain\Relations\libs;

use Illuminate\Support\Collection;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Interfaces\Repository\RelationConfigInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\relations\RelationInterface;
use ZnCore\Domain\Relations\repositories\BaseCommonRepository;
use InvalidArgumentException;

class RelationLoader
{

    /** @var RepositoryInterface */
    private $repository;
    private $relations;

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function setRepository(RepositoryInterface $repository): void
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
        if ($this->repository && $this->repository instanceof RelationConfigInterface) {
            DeprecateHelper::softThrow('RelationConfigInterface is deprecated, use relations2 for definition!');
            return $this->repository->relations();
        }
    }

    public function loadRelations(Collection $collection, Query $query)
    {
        $relations = $this->relations();
        $relations = $this->prepareRelations($relations);
        $relations = ArrayHelper::index($relations, 'name');

        /*$with = $query->getParam(Query::WITH);
        if($with) {
            dd($with);
        }*/

        if ($query->hasParam('with')) {
            $with = $query->getParam(Query::WITH);
            //dump([$with, get_class($this->repository)]);

            $relationTree = [];

            foreach ($with as $withItem) {
                $relParts = explode('.', $withItem);
                $attribute = $relParts[0];
                unset($relParts[0]);
                $relParts = array_values($relParts);
                $relationTree[$attribute] = array_merge($relationTree[$attribute] ?? [], $relParts);
            }

            //dd($asd);

            //foreach ($with as $withItem) {
            foreach ($relationTree as $attribute => $relParts) {
                /*$relParts = explode('.', $withItem);
                $attribute = $relParts[0];
                unset($relParts[0]);
                $relParts = array_values($relParts);*/

                //dump([$attribute, $relParts, get_class($this->repository)]);
                if (empty($relations[$attribute])) {
                    throw new InvalidArgumentException('Relation "' . $attribute . '" not defined in repository "' . get_class($this->repository) . '"!');
                }
                /** @var RelationInterface $relation */
                $relation = $relations[$attribute];
                $relation = $this->ensureRelation($relation);

                if (is_object($relation)) {
                    if ($relParts) {
                        //dump([$attribute, $relParts, get_class($this->repository)]);
                        //$nestedWith = implode('.', $relParts);
                        $relation->query = $relation->query ?: new Query;
                        $relation->query->with($relParts);
                        //dd($relation->query);
                    }
                    $relation->run($collection);
                }
                //$this->runRelation($relation, $collection);
                //$relation->run($collection);
                //dump($collection[0]->getBook());
            }
            //dd(222);
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

    private function runRelation(RelationInterface $relation, Collection $collection)
    {
        $relation->run($collection);
    }

    private function ensureRelation($relation): RelationInterface
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
