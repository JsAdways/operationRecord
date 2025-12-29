<?php
namespace Jsadways\Operationrecord\Traits;

use Exception;
use Jsadways\Operationrecord\Enums\ActionName;
use Jsadways\Operationrecord\Facades\OperationRecord;
use Jsadways\Operationrecord\Services\SetDto;

trait RecordsOperation
{
    /**
     * @throws Exception
     */
    protected function recordOperation(ActionName|string $action, array $data, ?int $creator_id = null, ?string $data_table = null): void
    {
        $recordModel = $this->getRecordModel();

        $operationRecord = OperationRecord::for($recordModel);

        match(true) {
            $action instanceof ActionName => $operationRecord->{strtolower($action->value) . '_action'}(),
            default => $operationRecord->action($action)
        };

        $operationRecord->set(new SetDto(
            data_table: $data_table ?? $this->getDataTable(),
            creator_id: $creator_id ?? $this->getCreatorId(),
            data: $data
        ));
    }

    /**
     * @throws Exception
     */
    protected function getRecordModel(): string
    {
        return $this->record_model
            ?? throw new Exception('Please set $record_model property');
    }

    protected function getDataTable(): string
    {
        return strtolower(str_replace('Controller', '', class_basename($this)));
    }

    /**
     * @throws Exception
     */
    protected function getCreatorId(): int|string
    {
        return $this->creator_id
            ?? throw new Exception('Please set $creator_id property');
    }
}
