<?php

namespace Jsadways\Operationrecord\Facades;

use Illuminate\Support\Facades\Facade;
use Jsadways\Operationrecord\Services\OperationRecordService;

/**
 * @method static OperationRecordService for(string $model)
 */
class OperationRecord extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'operation.record';
    }
}
