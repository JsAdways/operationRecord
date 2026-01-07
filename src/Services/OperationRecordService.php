<?php

namespace Jsadways\Operationrecord\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Jsadways\Operationrecord\Enums\ActionName;
use Jsadways\Operationrecord\Jobs\StoreOperationRecordJob;
use Jsadways\Operationrecord\Traits\LogMessage;
use Jsadways\Operationrecord\Exceptions\RecordException;
use Throwable;

class OperationRecordService
{
    use LogMessage;

    protected string $target_model;
    protected string $action_name;
    public function __construct(string $model_class){

        $this->target_model = $model_class;
    }

    /**
     * @throws RecordException
     */
    public function set(SetDto $data, bool $async = true): Model|null
    {
        if (!$async) {
            return $this->setSync($data);  // 同步模式
        }

        try {
            if (!isset($this->action_name)) {
                throw new RecordException("action_name is null");
            }
            $recordData = $this->prepareData($data);

            StoreOperationRecordJob::dispatch($this->target_model, $recordData);

            return null;  // 非同步模式回傳 null

        } catch (Throwable $throwable) {
            throw new RecordException($this->get_error($throwable));
        }
    }

    protected function prepareData(SetDto $data):array
    {
        $data = get_object_vars($data);
        $data_id = (isset($data['id'])) ? $data['id'] : $data['data']['id'] ?? null;

        return [
            'data_id' => $data_id,
            'data_source' => $data['data_source'],
            'creator_id' => $data['creator_id'],
            'action_name' => $this->action_name,
            'data' => $data['data'],
            'memo' => $data['memo'] ?? null,
        ];
    }

    /**
     * @throws RecordException
     */
    protected function setSync(SetDto $data): Model
    {
        try{
            if(!isset($this->action_name)){
                throw new RecordException("action_name is null");
            }

            return $this->target_model::create($this->prepareData($data));
        }
        catch (Throwable $throwable){
            throw new RecordException($this->get_error($throwable));
        }
    }

    public function create_action(): static
    {
        $this->action_name = ActionName::Create->value;
        return $this;
    }

    public function update_action(): static
    {
        $this->action_name = ActionName::Update->value;
        return $this;
    }

    public function delete_action(): static
    {
        $this->action_name = ActionName::Delete->value;
        return $this;
    }

    public function action(string $action_name): static
    {
        $this->action_name = $action_name;
        return $this;
    }

    /**
     * @throws RecordException
     */
    public function get(array $filter): collection
    {
        try{
            return $this->target_model::filter($filter)->first();
        }catch (Throwable $throwable){
            throw new RecordException($this->get_error($throwable));
        }
    }

    /**
     * @throws RecordException
     */
    public function list(ListDto $data): collection
    {
        try{
            $query_data = get_object_vars($data);
            $sort_by = $query_data['sort_by'] ?? 'id';
            $sort_order = $query_data['sort_order'] ?? 'asc';
            $per_page = $query_data['per_page'] ?? '30';
            $page = $query_data['page'] ?? null;
            $filter = $query_data['filter'] ?? [];
            $show_diff = $query_data['show_diff'];

            $query = $this->target_model::filter($filter)->orderBy($sort_by, $sort_order);

            if ($per_page) {
                if ($page !== null) {
                    // 手動指定頁碼
                    Paginator::currentPageResolver(function () use ($page) {
                        return $page;
                    });
                }
                $result = $query->paginate($per_page, ['*'], 'page');
            } else {
                $result = $query->get();
            }

            return $this->_find_previous_data($result,$show_diff);

        }catch (Throwable $throwable){
            throw new RecordException($this->get_error($throwable));
        }
    }

    /**
     * @throws RecordException
     */
    protected function _find_previous_data(Mixed $data, bool $show_diff):collection
    {
        try {
            if ($show_diff) {
                $target = $data;
                if ($data instanceof LengthAwarePaginator) {
                    $target = collect($data->items());
                }

                $target = $target->map(function ($item) use ($target) {
                    $next = $target->where('created_at', '>', $item['created_at'])->first();
                    $item['previous'] = [];
                    if (!empty($next)) {
                        $item['previous'] = $next['data'];
                    }
                    return $item;
                });

                if ($data instanceof LengthAwarePaginator) {
                    $data = $data->setCollection($target);
                } else {
                    $data = $target;
                }
            }

            return collect($data->toArray());
        }catch (Throwable $throwable){
            throw new RecordException($this->get_error($throwable));
        }
    }
}
