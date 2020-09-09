<?php

namespace ZnCore\Domain\Interfaces\Entity;

interface EntityIdInterface
{

    /**
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

}