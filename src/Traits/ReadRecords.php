<?php
namespace Jsadways\Operationrecord\Traits;

use Jsadways\Operationrecord\Exceptions\RecordException;
use Jsadways\Operationrecord\Facades\OperationRecord;
use Jsadways\Operationrecord\Services\ListDto;

trait ReadRecords {
    abstract protected function getRecordModel(): string;

    /**
     * @throws RecordException
     */
    protected function readRecord(array $filter = NULL, string $sort_by = NULL, string $sort_order = NULL, int $per_page = 0,int $page = NULL, bool $show_diff = false): array
    {
        $recordModel = $this->getRecordModel();

        $operationRecord = OperationRecord::for($recordModel);
        return $operationRecord->list(new ListDto(
            filter: $filter,
            sort_by: $sort_by,
            sort_order: $sort_order,
            per_page: $per_page,
            page: $page,
            show_diff: $show_diff
        ))->toArray();
    }
}
