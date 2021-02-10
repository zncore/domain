<?php

namespace ZnCore\Domain\Interfaces\Libs;

interface TransactionInterface
{

    public function beginTransaction();

    public function rollbackTransaction();

    public function commitTransaction();
}