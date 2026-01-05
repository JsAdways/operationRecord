<?php
namespace Jsadways\Operationrecord\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreOperationRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 屬性定義
    protected string $targetModel;
    protected array $recordData;

    // 重試設定
    public int $tries = 3;          // 失敗重試 3 次
    public int $timeout = 60;       // 逾時 60 秒
    public int $backoff = 10;       // 每次重試間隔 10 秒

    // 建構子
    public function __construct(string $targetModel, array $recordData)
    {
        // 儲存目標 Model 類別名稱
        $this->targetModel = $targetModel;
        // 儲存要寫入的資料
        $this->recordData = $recordData;

        $this->onQueue('operation-records');
        $this->onConnection('redis');
    }

    // 執行 Job

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            // 執行寫入
            $this->targetModel::create($this->recordData);

        } catch (Throwable $e) {
            // 記錄錯誤
            Log::error('StoreOperationRecordJob handle error', [
                'model' => $this->targetModel,
                'data' => $this->recordData,
                'error' => $e->getMessage(),
                'attempts' => $this->attempts(),
            ]);

            // 重新拋出例外，讓 Laravel 自動重試
            throw $e;
        }
    }

    // 失敗處理
    public function failed(Throwable $exception): void
    {
        // 記錄錯誤到 log
        \Log::error('StoreOperationRecordJob failed', [
            'model' => $this->targetModel,
            'data' => $this->recordData,
            'exception' => $exception->getMessage()
        ]);
    }
}
