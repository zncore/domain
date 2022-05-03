<?php

namespace ZnCore\Domain\Relations\libs;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Interfaces\Repository\RelationConfigInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\relations\RelationInterface;

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

    private function getRelationTree($with): array
    {
        $relationTree = [];
        foreach ($with as $attribute => $withItem) {
            $relParts = null;
            if (is_string($withItem)) {
                $relParts1 = explode('.', $withItem);
                $attribute = $relParts1[0];
                unset($relParts1[0]);
                $relParts1 = array_values($relParts1);
                if ($relParts1) {
                    $relParts = [implode('.', $relParts1)];
                }
            } elseif (is_array($withItem)) {
                $relParts = $withItem;
                /*if(ArrayHelper::isIndexed($withItem)) {
                    
                } else {

                }*/
            } elseif (is_object($withItem) && $withItem instanceof Query) {
                $relParts = $withItem->getParam(Query::WITH);
            }

            if (!empty($relParts)) {
                foreach ($relParts as $relPart) {
                    $relationTree[$attribute][] = $relPart;
                    /*if(strpos($relPart, '.')) {
                        //dd($this->getRelationTree([$relPart]));
                        $relationTree[$attribute] = $this->getRelationTree([$relPart]);
                    } else {
                        $relationTree[$attribute][] = $relPart;
                    }*/
                }
                //$relationTree[$attribute] = array_merge($relationTree[$attribute] ?? [], $relParts);
            } else {
                $relationTree[$attribute] = [];
            }

        }
        return $relationTree;
    }

    public function loadRelations(Collection $collection, Query $query)
    {
        $relations = $this->relations();
        $relations = $this->prepareRelations($relations);
        $relations = ArrayHelper::index($relations, 'name');

        if ($query->hasParam('with')) {
            $with = $query->getParam(Query::WITH);

            $relationTree = $this->getRelationTree($with);

            //dump([$relationTree, get_class($this->repository)]);

            //dd($relationTree);

            foreach ($relationTree as $attribute => $relParts) {

                /*if(is_integer($attribute)) {
                    $attribute = $relParts[0];
                    $relParts = [];
                }*/

                //dump([$attribute, $relParts, get_class($this->repository)]);
                if (empty($relations[$attribute])) {
                    //dd([$relationTree, $attribute, $relParts, get_class($this->repository)]);
                    //dd($attribute , $relParts);
                    throw new InvalidArgumentException('Relation "' . $attribute . '" not defined in repository "' . get_class($this->repository) . '"!');
                }
                /** @var RelationInterface $relation */
                $relation = $relations[$attribute];
                $relation = $this->ensureRelation($relation);

                if (is_object($relation)) {
                    if ($relParts) {
                        //dump([$attribute, $relParts, get_class($this->repository)]);
                        $relation->query = $relation->query ?: new Query;
                        $relation->query->with($relParts);
                        //dd($relation);
                    }
                    $relation->run($collection);
                }
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

    /*private function runRelation(RelationInterface $relation, Collection $collection)
    {
        $relation->run($collection);
    }*/

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
