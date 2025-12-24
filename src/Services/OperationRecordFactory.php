<?php

namespace Jsadways\Operationrecord\Services;

class OperationRecordFactory
{
    public function for($model): OperationRecordService
    {
        return new OperationRecordService($model);
    }
}
