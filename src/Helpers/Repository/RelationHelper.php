<?php

namespace ZnCore\Domain\Helpers\Repository;

use Illuminate\Support\Collection;
use ZnCore\Base\Exceptions\InvalidConfigException;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Helpers\Helper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Dto\WithDto;
use ZnCore\Domain\Entities\relation\RelationEntity;
use ZnCore\Base\Libs\Entity\Helpers\EntityHelper;
use ZnCore\Base\Libs\Repository\Interfaces\RelationConfigInterface;
use ZnCore\Base\Libs\Query\Entities\Query;

DeprecateHelper::hardThrow();

class RelationHelper
{

    public static function load(RelationConfigInterface $repository, Query $query, $data, WithDto $withDto = null)
    {
        $relations = $repository->relations();
        $relations = self::forgeEntity($relations, RelationEntity::class, true, true);

        /*if($relations) {
            dd($relations);
        }*/

        //$relations = RelationConfigHelper::getRelationsConfig($domain, $repositoryId);
        $remainOfWith = [];
        $withParams = RelationWithHelper::fetch($query, $remainOfWith);

        foreach ($withParams as $relationName) {
            $newWithDto = self::forgeNewWithDto($relationName, $relations);
            $newWithDto->withParams = $withParams;
            $newWithDto->remain = $remainOfWith;
            self::hh($withDto, $newWithDto);
            self::prepareWithDto($query, $newWithDto);
            $data = empty($data) ? [] : $data;
            $data = self::loadRelations($data, $newWithDto);
        }

        return $data;
    }

    /*public static function load222($repository, Query $query, $data, WithDto $withDto = null) {
        //string $domain, string $repositoryId
        $relations = RelationConfigHelper::getRelationsConfig22($repository);
        $remainOfWith = [];
        $withParams = RelationWithHelper::fetch($query, $remainOfWith);
        foreach($withParams as $relationName) {
            $newWithDto = self::forgeNewWithDto($relationName, $relations);
            $newWithDto->withParams = $withParams;
            $newWithDto->remain = $remainOfWith;
            self::hh($withDto, $newWithDto);
            self::prepareWithDto($query, $newWithDto);
            $data = empty($data) ? [] : $data;
            $data = self::loadRelations($data, $newWithDto);
        }
        return $data;
    }

	public static function load($repository, Query $query, $data, WithDto $withDto = null) {
		//$relations = RelationConfigHelper::getRelationsConfig($domain, $repositoryId);
        $relations = RelationConfigHelper::getRelationsConfig22($repository);
		$remainOfWith = [];
		$withParams = RelationWithHelper::fetch($query, $remainOfWith);
		foreach($withParams as $relationName) {
			$newWithDto = self::forgeNewWithDto($relationName, $relations);
			$newWithDto->withParams = $withParams;
			$newWithDto->remain = $remainOfWith;
			self::hh($withDto, $newWithDto);
			self::prepareWithDto($query, $newWithDto);
			$data = empty($data) ? [] : $data;
			$data = self::loadRelations($data, $newWithDto);
		}
		return $data;
	}*/

    private static function forgeEntity($value, string $className, bool $isCollection = null, $isSaveKey = false)
    {

        //throw new \ZnCore\Base\Exceptions\DeprecatedException;

        if (empty($value)) {
            return null;
        }
        if ($value instanceof $className) {
            return $value;
        }
        if (!is_array($value)) {
            return null;
        }
        if (ArrayHelper::isIndexed($value) || $isCollection) {
            $result = [];
            foreach ($value as $key => &$item) {
                if ($isSaveKey) {
                    $result[$key] = self::forgeEntity($item, $className);
                } else {
                    $result[] = self::forgeEntity($item, $className);
                }
            }
        } else {
            $result = new $className();
            EntityHelper::setAttributes($result, $value);
            //$result->load($value);
        }
        /*if($isCollection !== null) {
            if() {

            }
        }*/
        return $result;
    }

    private static function hh($withDto, WithDto $newWithDto): void
    {
        if ($withDto instanceof WithDto) {
            $newWithDto->passed = trim($withDto->passed . '.' . $newWithDto->relationName, '.');
        } else {
            $newWithDto->passed = $newWithDto->relationName;
        }
    }

    private static function forgeNewWithDto(string $relationName, array $relations): WithDto
    {
        if (!array_key_exists($relationName, $relations)) {
            throw new InvalidConfigException('relation not defined ' . $relationName);
        }
        $w = new WithDto;
        $w->relationConfig = $relations[$relationName];
        $w->relationName = $relationName;
        return $w;
        /*if(strpos($w->passed, '.') !== false) {
            if($query instanceof Query && $query->getNestedQuery($w->passed) instanceof Query) {
                print_r($query->getNestedQuery($w->passed)->toArray());exit;
            }
        }*/
    }

    private static function prepareWithDto(Query $query, WithDto $withDto): void
    {
        if ($query->getNestedQuery($withDto->passed) instanceof Query) {
            $withDto->query = $query->getNestedQuery($withDto->passed);
            $withDto->query->with($withDto->remain[$withDto->relationName]);
        } else {
            $withDto->query = clone $query;
            $withDto->query->removeParam('with');
            $withDto->query->with($withDto->remain[$withDto->relationName]);
        }
    }

    private static function loadRelations($data, WithDto $w)
    {
        $isEntity = EntityHelper::isEntity($data);
        /** @var Collection $collection */
        $collection = $isEntity ? [$data] : $data;
        $collection = self::loadRelationsForCollection($collection, $w);
        return $isEntity ? $collection[0] : $collection;
    }

    private static function loadRelationsForCollection(Collection $collection, WithDto $withDto): Collection
    {
        /** @var Collection $relCollection */

        $joinStrategy = new JoinStrategy();
        $joinStrategy->setStrategyName($withDto->relationConfig->type);
        $relCollection = $joinStrategy->join($collection, $withDto->relationConfig);
        if (!empty($relCollection)) {
            foreach ($collection as &$entity) {
                $relationEntity = $joinStrategy->load($entity, $withDto, $relCollection);
                if (!empty($withDto->remain[$withDto->relationName])) {
                    self::load($relationEntity->foreign->model, $withDto->query, $entity->{$withDto->relationName}, $withDto);
                }
            }
        }
        return $collection;
    }

}
