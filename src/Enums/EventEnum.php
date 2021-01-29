<?php

namespace ZnCore\Domain\Enums;

class EventEnum
{

    /** Выполняется до сохранения сущности */
    const BEFORE_CREATE_ENTITY = 'entity.before_create';

    /** Выполняется после сохранения сущности */
    const AFTER_CREATE_ENTITY = 'entity.after_create';

    /** Выполняется после удаления сущности */
    const AFTER_DELETE_ENTITY = 'entity.after_delete';

    /** Выполняется до чтения одной сущности */
    const BEFORE_READ_ENTITY = 'entity.before_read';

    /** Выполняется после чтения одной сущности */
    const AFTER_READ_ENTITY = 'entity.after_read';

    /** Выполняется после чтения коллекции сущностей */
    const AFTER_READ_COLLECTION = 'collection.after_read';
}
