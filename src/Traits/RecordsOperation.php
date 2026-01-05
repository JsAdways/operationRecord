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
        // 先檢查當前類別是否為 Controller
        $currentClass = class_basename($this);
        if (str_contains($currentClass, 'Controller')) {
            return strtolower(str_replace('Controller', '', $currentClass));
        }

        // 如果不是 Controller（例如是 Service），則往調用堆疊中尋找
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($trace as $frame) {
            if (isset($frame['class'])) {
                $className = class_basename($frame['class']);
                if (str_contains($className, 'Controller')) {
                    return strtolower(str_replace('Controller', '', $className));
                }
            }
        }

        // 如果找不到 Controller，回傳當前類別名稱
        return strtolower(str_replace(['Controller', 'Service'], '', $currentClass));
    }
}
