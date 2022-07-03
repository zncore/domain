<?php

namespace ZnCore\Domain\Collection\Libs;

use Closure;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use phpDocumentor\Reflection\Types\Static_;
use ZnCore\Domain\Collection\Interfaces\Enumerable;

class Collection extends \Doctrine\Common\Collections\ArrayCollection implements Enumerable
//class Collection extends \Illuminate\Support\Collection implements Enumerable
{

    /*public function __construct($elements = [])
    {
        if($elements instanceof static) {
            $elements = $elements->toArray();
        }
        if($elements == null) {
            $elements = [];
        }
        parent::__construct($elements);
    }*/

    /*public function all()
    {
        return $this->toArray();
    }

    public function where($field, $operator, $value) {
        $expr = new Comparison($field, $operator, $value);
        $criteria = new Criteria();
        $criteria->andWhere($expr);
        return $this->matching($criteria);

//        dump(debug_backtrace());
    }

    public function concat(self $source)
    {
        $result = clone $this;

        foreach ($source as $item) {
            $result->add($item);
        }

        return $result;
    }

    public function chunk($size)
    {
        if ($size <= 0) {
            return new static;
        }

        $chunks = [];

        foreach (array_chunk($this->toArray(), $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }*/
}
