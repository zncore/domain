<?php

namespace ZnCore\Domain\Enums;

class EventEnum
{

    /** Момент формирования запроса из модели фильтра */
    const BEFORE_FORGE_QUERY_BY_FILTER = 'query.before_forge_query_by_filter';

    const BEFORE_FORGE_QUERY = 'query.before_forge_query';

    /** Выполняется до сохранения сущности */
    const BEFORE_CREATE_ENTITY = 'entity.before_create';

    /** Выполняется после сохранения сущности */
    const AFTER_CREATE_ENTITY = 'entity.after_create';

    /** Выполняется до сохранения сущности */
    const BEFORE_UPDATE_ENTITY = 'entity.before_update';

    /** Выполняется до сохранения сущности */
    const AFTER_UPDATE_ENTITY = 'entity.after_update';

    /** Выполняется после удаления сущности */
    const AFTER_DELETE_ENTITY = 'entity.after_delete';

    /** Выполняется до чтения одной сущности */
    const BEFORE_READ_ENTITY = 'entity.before_read';

    /** Выполняется после чтения одной сущности */
    const AFTER_READ_ENTITY = 'entity.after_read';

    /** Выполняется после чтения коллекции сущностей */
    const AFTER_READ_COLLECTION = 'collection.after_read';
}
