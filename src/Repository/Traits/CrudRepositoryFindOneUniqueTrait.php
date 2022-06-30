<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Base\Text\Helpers\Inflector;
use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Domain\Traits\FindOneTrait;
use ZnCore\Domain\Entity\Exceptions\AlreadyExistsException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnLib\Components\I18Next\Facades\I18Next;

trait CrudRepositoryFindOneUniqueTrait
{

}
