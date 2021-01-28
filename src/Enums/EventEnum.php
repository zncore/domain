<?php

namespace ZnCore\Domain\Enums;

class EventEnum
{

    const BEFORE_CREATE_ENTITY = 'entity.before_create';
    const AFTER_CREATE_ENTITY = 'entity.after_create';

    const AFTER_DELETE_ENTITY = 'entity.after_delete';

    const BEFORE_READ_ENTITY = 'entity.before_read';
    const AFTER_READ_ENTITY = 'entity.after_read';

    const AFTER_READ_COLLECTION = 'collection.after_read';
}
