<?php
namespace Jsadways\Operationrecord\Traits;

use Exception;
use Jsadways\Operationrecord\Enums\ActionName;
use Jsadways\Operationrecord\Facades\OperationRecord;
use Jsadways\Operationrecord\Services\SetDto;

trait RecordsOperation
{
    abstract protected function getCreatorId(): int|string;
    abstract protected function getRecordModel(): string;
    /**
     * @throws Exception
     */
    protected function recordOperation(ActionName|string $action, array $data, ?string $memo = null, ?int $creator_id = null, ?string $data_source = null): void
    {
        $recordModel = $this->getRecordModel();

        $operationRecord = OperationRecord::for($recordModel);

        match(true) {
            $action instanceof ActionName => $operationRecord->{strtolower($action->value) . '_action'}(),
            default => $operationRecord->action($action)
        };

        $operationRecord->set(new SetDto(
            data_source: $data_source ?? $this->getDataSource(),
            creator_id: $creator_id ?? $this->getCreatorId(),
            data: $data,
            memo: $memo
        ));
    }

    protected function getDataSource(): string
    {
        return strtolower(str_replace('Controller', '', class_basename($this)));
    }
}
