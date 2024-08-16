<?php

namespace Jsadways\Operationrecord\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Jsadways\Operationrecord\Traits\LogMessage;
use Jsadways\Operationrecord\Exceptions\RecordException;
use Throwable;

class OperationRecordService
{
    use LogMessage;

    protected string $target_model;
    public function __construct(string $model_class){

        $this->target_model = $model_class;
    }

    /**
     * @throws RecordException
     */
    public function set(SetDto $data): collection
    {
        try{
            $data = get_object_vars($data);
            return $this->target_model::create([
                'data_id' => $data['data_id'],
                'creator_id' => $data['creator_id'],
                'action_name' => $data['action_name'],
                'data' => $data['data']
            ]);
        }
        catch (Throwable $throwable){
            throw new RecordException($this->get_error($throwable));
        }
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
            $filter = $query_data['filter'] ?? [];
            $show_diff = $query_data['show_diff'];

            $query = $this->target_model::filter($filter)->orderBy($sort_by, $sort_order);

            if ($per_page) {
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
