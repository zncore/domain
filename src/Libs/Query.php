<?php

namespace ZnCore\Domain\Libs;

use ZnCore\Base\Exceptions\DeprecatedException;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\Query\Join;
use ZnCore\Domain\Entities\Query\Where;
use ZnCore\Domain\Enums\OperatorEnum;

class Query
{

    const WHERE = 'where';
    const WHERE_NEW = 'whereNew';
    const SELECT = 'select';
//    const SELECT = 'fields';
    const WITH = 'with';
    const PAGE = 'page';
    const PER_PAGE = 'per-page';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER = 'order';
    const GROUP = 'group';
    const JOIN = 'join';

    private $where;
    private $whereNew;
    private $select;
    private $with;
    private $page;
    private $perPage;
    private $limit;
    private $offset;
    private $order;
    private $group;
    private $join;
    protected $filterModel = null;

    protected $query = [
        self::WHERE => [],
        self::WHERE_NEW => [],
        'nestedQuery' => [],
    ];

    /**
     * @return Where[]
     */
    public function getWhere()
    {
//        return $this->getParam(Query::WHERE);
        return $this->where;
    }

    /*public function setWhere($where): void
    {

        $this->where = $where;
    }*/

    public function getSelect()
    {
//        return $this->getParam(Query::SELECT);
        return $this->select;
    }

    /*public function setSelect($select): void
    {
        $this->select = $select;
    }*/

    public function getWith()
    {
//        return $this->getParam(Query::WITH);
        return $this->with;
    }

    /*public function setWith($with): void
    {
        $this->with = $with;
    }*/

    public function getPage()
    {
//        return $this->getParam(Query::PAGE);
        return $this->page;
    }

    /*public function setPage($page): void
    {
        $this->page($page);
        //$this->page = $page;
    }*/

    public function getPerPage()
    {
//        return $this->getParam(Query::PER_PAGE);
        return $this->perPage;
    }

    /*public function setPerPage($perPage): void
    {
        $this->perPage($perPage);
//        $this->perPage = $perPage;
//        $this->limit = $perPage;
    }*/

    public function getLimit()
    {
//        return $this->getParam(Query::LIMIT);
        return $this->limit;
    }

    /*public function setLimit($limit): void
    {
        $this->limit($limit);
//        $this->perPage = $limit;
//        $this->limit = $limit;
    }*/

    public function getOffset()
    {
//        return $this->getParam(Query::OFFSET);
        return $this->offset;
    }

    /*public function setOffset($offset): void
    {
        $this->offset($offset);
//        $this->offset = $offset;
    }*/

    public function getOrder()
    {
        //return $this->getParam(Query::ORDER);
        return $this->order;
    }

    /*public function setOrder($order): void
    {
        //$this->orderBy($order);
        $this->order = $order;
    }*/

    public function getGroup()
    {
        //return $this->getParam(Query::GROUP);
        return $this->group;
    }

    /*public function setGroup($group): void
    {
        $this->groupBy($group);
        //$this->query[self::GROUP] = $group;
//        $this->group = $group;
    }*/

    public function getJoin()
    {
        //return $this->getParam(Query::JOIN);
        return $this->join;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setQuery(array $query): void
    {
        $this->query = $query;
    }

    /*public function setJoin($join): void
    {
        $this->joinNew($join);
//        $this->query[self::JOIN] = $join;
//        $this->join = $join;
    }*/

    public function getFilterModel(): ?object
    {
        return $this->filterModel;
    }

    public function setFilterModel(object $filterModel): void
    {
        $this->filterModel = $filterModel;
    }



    /*public function getHash()
    {
        $query = $this->query;
        foreach ($query as $key => $value) {
            if (empty($value)) {
                unset($key);
            }
        }
        ksort($query);
        $serialized = serialize($query);
        return hash('crc32b', $serialized);
    }

    public static function forgeClone($query = null)
    {
        $query = self::forge($query);
        return clone $query;
    }*/

    /**
     * @param null $query
     *
     * @return Query
     */
    public static function forge($query = null)
    {
        if ($query instanceof Query) {
            return $query;
        }
        return new Query();
    }

    public function joinNew(Join $join)
    {
        //$table = TableHelpeR::getGlobalName($table);
        $this->query['join_new'][] = $join;
        $this->join[] = $join;
        return $this;
    }

    public function join($type, $table, $on = '', $params = [])
    {
        DeprecateHelper::softThrow('join');
        //$table = TableHelpeR::getGlobalName($table);
        $item = [
            'type' => $type,
            'table' => $table,
            'on' => $on,
            'params' => $params,
        ];
        $this->query[self::JOIN][] = $item;
        return $this;
    }

    public function setNestedQuery($key, Query $query)
    {
        DeprecateHelper::softThrow('setNestedQuery');
        $this->query['nestedQuery'][$key] = $query;
        return $this;
    }

    public function getNestedQuery($key)
    {
        return ArrayHelper::getValue($this->query, "nestedQuery.$key");
    }

    /*public function getWhere($key)
    {
        $where = $this->query[self::WHERE];
        return $this->findWhereInArray($key, $where);
    }*/

    /*private function findWhereInArray($fieldName, $array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        foreach ($array as $key => &$value) {
            if ($key === $fieldName) {
                return $value;
            } elseif (is_array($value)) {
                $value = $this->findWhereInArray($fieldName, $value);
                if (!empty($value)) {
                    return $value;
                }
            }
        }
        return null;
    }*/

    public function whereNew(Where $where)
    {
        $this->query[self::WHERE_NEW][] = $where;
        $this->where[] = $where;
    }

    /**
     * @return array|mixed|Where[]
     */
    public function getWhereNew()
    {
        return $this->query[self::WHERE_NEW] ?? [];
    }

    public function whereByConditions(array $list)
    {
        foreach ($list as $itemName => $itemValue) {
            $this->whereNew(new Where($itemName, $itemValue));
        }
    }

    public function where($key, $value, string $operator = OperatorEnum::EQUAL)
    {
        if (func_num_args() == 1) {
            throw new DeprecatedException('Where from 1 argument deprecated! Use "whereByConditions".');
        }
        if (is_array($key)) {
            throw new DeprecatedException('Where from array deprecated! Use "whereByConditions".');
        }
        $this->whereNew(new Where($key, $value, $operator));
        return $this;
    }

    /*public function addWhere($key, $condition)
    {
        $this->query[self::WHERE][$key] = $condition;

        return $this;
    }*/

    public function andWhere($condition)
    {
        if ($this->query[self::WHERE] === null) {
            $this->query[self::WHERE] = $condition;
            $this->where = $condition;
        } else {
            $this->query[self::WHERE] = ['and', $this->query[self::WHERE], $condition];
            $this->where = ['and', $this->query[self::WHERE], $condition];
        }

        return $this;
    }

    /*public function orWhere($condition)
    {
        if ($this->query[self::WHERE] === null) {
            $this->query[self::WHERE] = $condition;
        } else {
            $this->query[self::WHERE] = ['or', $this->query[self::WHERE], $condition];
        }

        return $this;
    }*/

    /*private function oldWhere($key, $value)
    {
        if ($value === null) {
            unset($this->query[self::WHERE][$key]);
        } else {
            $this->query[self::WHERE][$key] = $value;
        }
        return $this;
    }*/

    /*public function removeWhere($fieldName)
    {
        $where = $this->query[self::WHERE];
        $where = $this->removeWhereInArray($fieldName, $where);
        $this->query[self::WHERE] = $where;
    }

    private function removeWhereInArray($fieldName, $array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        foreach ($array as $key => &$value) {
            if ($key === $fieldName) {
                unset($array[$key]);
            } elseif (is_array($value)) {
                $value = $this->removeWhereInArray($fieldName, $value);
                if (empty($value)) {
                    unset($array[$key]);
                }
            }
        }
        return $array;
    }*/

    public function whereFromCondition($condition)
    {
        if (empty($condition)) {
            return;
        }
        if (!empty($condition)) {
            foreach ($condition as $name => $value) {
                $this->where($name, $value);
            }
        }
    }

    public function select($fields)
    {
        if ($fields === null) {
            unset($this->query[self::SELECT]);
            $this->select = null;
            return $this;
        }
        $this->setParam($fields, self::SELECT);
        $this->select = $fields;
        return $this;
    }

    public function with($names)
    {
        $this->setParam($names, self::WITH);
        $this->with = $names;
        return $this;
    }

    /*public function removeWith($key)
    {
        if (!empty($key)) {
            unset($this->query[self::WITH][$key]);
        } else {
            unset($this->query[self::WITH]);
        }
    }*/

    public function page($value)
    {
        if ($value === null) {
            unset($this->query[self::PAGE]);
            $this->page = null;
            return $this;
        }
        $this->query[self::PAGE] = intval($value);
        $this->page = intval($value);
        return $this;
    }

    public function perPage($value)
    {
        if ($value === null) {
            unset($this->query['per-page']);
            $this->perPage = null;
            $this->limit = null;
            return $this;
        }
        $this->query['per-page'] = intval($value);
        $this->query[self::LIMIT] = intval($value);

        $this->perPage = intval($value);
        $this->limit = intval($value);

        return $this;
    }

    public function limit($value)
    {
        if ($value === null) {
            unset($this->query[self::LIMIT]);
            $this->perPage = null;
            $this->limit = null;
            return $this;
        }
        $this->query[self::LIMIT] = intval($value);
        $this->query['per-page'] = intval($value);
        $this->perPage = intval($value);
        $this->limit = intval($value);
        return $this;
    }

    public function offset($value)
    {
        if ($value === null) {
            unset($this->query[self::OFFSET]);
            $this->offset = null;
            return $this;
        }
        $this->query[self::OFFSET] = intval($value);
        $this->offset = intval($value);
        return $this;
    }

    /**
     * Sets the ORDER BY part of the query.
     * @param string|array|Expression $columns the columns (and the directions) to be ordered by.
     * Columns can be specified in either a string (e.g. `"id ASC, name DESC"`) or an array
     * (e.g. `['id' => SORT_ASC, 'name' => SORT_DESC]`).
     *
     * The method will automatically quote the column names unless a column contains some parenthesis
     * (which means the column contains a DB expression).
     *
     * Note that if your order-by is an expression containing commas, you should always use an array
     * to represent the order-by information. Otherwise, the method will not be able to correctly determine
     * the order-by columns.
     *
     * Since version 2.0.7, an [[Expression]] object can be passed to specify the ORDER BY part explicitly in plain SQL.
     * @return $this the query object itself
     * @see addOrderBy()
     */
    public function orderBy($columns)
    {
        $this->order = $this->normalizeOrderBy($columns);
        $this->query[self::ORDER] = $this->order;
        return $this;
    }

    public function groupBy($columns)
    {
        $this->group = $this->normalizeGroupBy($columns);
        $this->query[self::GROUP] = $this->group;
        return $this;
    }

    /**
     * Adds additional ORDER BY columns to the query.
     * @param string|array|Expression $columns the columns (and the directions) to be ordered by.
     * Columns can be specified in either a string (e.g. "id ASC, name DESC") or an array
     * (e.g. `['id' => SORT_ASC, 'name' => SORT_DESC]`).
     *
     * The method will automatically quote the column names unless a column contains some parenthesis
     * (which means the column contains a DB expression).
     *
     * Note that if your order-by is an expression containing commas, you should always use an array
     * to represent the order-by information. Otherwise, the method will not be able to correctly determine
     * the order-by columns.
     *
     * Since version 2.0.7, an [[Expression]] object can be passed to specify the ORDER BY part explicitly in plain SQL.
     * @return $this the query object itself
     * @see orderBy()
     */
    public function addOrderBy($columns)
    {
        $columns = $this->normalizeOrderBy($columns);
        if (ArrayHelper::getValue($this->query, self::ORDER) === null) {
            $this->query[self::ORDER] = $columns;

            $this->order = $columns;
        } else {
            $this->query[self::ORDER] = array_merge($this->query[self::ORDER], $columns);

            $this->order = array_merge($this->order, $columns);
        }
        return $this;
    }

    public function fields($fields)
    {
        $this->select($fields);
        return $this;
    }

    public function extraFields()
    {
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return $this->query;
    }

    public function hasParam($key)
    {
        return ArrayHelper::has($this->query, $key);
    }

    public function getParam(string $key, $default = null)
    {
        $value = ArrayHelper::getValue($this->query, $key, $default);
        return $value;
    }

    public function removeParam($key)
    {
        ArrayHelper::remove($this->query, $key);
    }

    public static function cloneForCount(Query $query = null)
    {
        $query = self::forge($query);
        $queryClone = self::forge();
        $queryClone->whereFromCondition($query->getParam(self::WHERE));
        return $queryClone;
    }

    protected function normalizeOrderBy($columns)
    {
        if ($columns instanceof Expression) {
            return [$columns];
        } elseif (is_array($columns)) {
            return $columns;
        }

        $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        foreach ($columns as $column) {
            if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                $result[$matches[1]] = strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            } else {
                $result[$column] = SORT_ASC;
            }
        }

        return $result;
    }

    protected function normalizeGroupBy($columns)
    {
        if ($columns instanceof Expression) {
            return [$columns];
        } elseif (is_array($columns)) {
            return $columns;
        }
    }

    private function setParam($fields, $nameParam)
    {
        if (is_array($fields)) {
            if (isset($this->query[$nameParam])) {
                $this->query[$nameParam] = ArrayHelper::merge($this->query[$nameParam], $fields);
            } else {
                $this->query[$nameParam] = $fields;
            }
        } else {
            $this->query[$nameParam][] = $fields;
        }

    }

    /**
     * Добавить в запрос поиск по jsonb полю
     *
     * ПРИМЕР поиска по jsonb с вложенными массивами
     * $query->setJsonbCondition(['key_1','key_2','final_key'], 'что то ищу');
     * поиск с 1 вложенностью
     * $query->setJsonbCondition('final_key', 'что то ищу');
     * поиск по IN
     * $query->setJsonbCondition($'group_id', 'что то ищу', false, "IN");
     * поиск с использованием $value
     * $query->setJsonbCondition($['someprop','caption','ru'], 'что то ищу', false, '=', 'properties', 'название');
     *
     * @param mixed $value значение для подстановки
     * @param string||string[] $attr
     * @param string $operator
     * @param string $jsonbFieldName
     */
    public function setJsonbCondition($attrs, $value, $operator = '=', $jsonbFieldName = 'value', $lower = false)
    {
        $condition = $this->getJsonbCondition($attrs, $value, $operator, $jsonbFieldName, $lower);
        $this->andWhere($condition);

    }

    /**
     * Получить массив для построения условия
     * @param string||string[] $attr
     * @param mixed $value значение для подстановки
     * @param string $operator
     * @param string $jsonbFieldName
     * @param mixed $value значение для подстановки
     * @return string
     */
    public function getJsonbCondition($attrs, $value, $operator = '=', $jsonbFieldName = 'value', $lower = false)
    {
        $fn = null;
        if ($lower) {
            $fn = 'lower';
        }
        $jsonPath = null;
        if (is_string($attrs)) {
            $attrs = [$attrs];
        }
        $attr = null;
        $attrsCount = count($attrs);
        $i = 1;
        foreach ($attrs as $ind => $key) {
            if ($i == $attrsCount) {
                $attr = $key;
            }
            if ($i < $attrsCount) {
                $jsonPath .= " -> '{$key}'";
            } else {
                $jsonPath .= " ->> '{$key}'";
                continue;
            }
            $i++;
        }

        return $this->createJsonBCondition($attr, $fn, $jsonbFieldName, $jsonPath, $operator, $value, $lower);
    }

    /**
     * Собираем условие для поиска
     */
    public function createJsonBCondition($attr, $fn, $jsonbFieldName, $jsonPath, $operator, $value, $lower)
    {
        if ($operator == "IN") {
            if (!is_array($value)) {
                $value = [$value];
            }
            $inConditionElements = "";
            foreach ($value as $elem) {
                $elem = $this->cleanStringValue($elem, $lower);
                $inConditionElements .= "'" . $elem . "',";
            }
            $inConditionElements = trim($inConditionElements, ',');
            return "{$fn}({$jsonbFieldName} {$jsonPath}) {$operator} ({$inConditionElements})";
        }
        $value = $this->cleanStringValue($value, $lower);
        $param = "'" . ($operator == 'like' ? '%' . $value . '%' : $value) . "'";
        $condition = "{$fn}({$jsonbFieldName} {$jsonPath}) {$operator} {$param}";

        return $condition;
    }


    /**
     * Зачистка строки перед отправкой в запрос
     * @param $value
     * @return mixed
     */
    public function cleanStringValue($value, $lower = false)
    {
        $value = preg_replace('/[^ a-zа-яё\d]/ui', '', $value);
        if ($lower) {
            $value = mb_strtolower($value);
        }

        return $value;
    }


}
