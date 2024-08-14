<?php

namespace Jsadways\Operationrecord\Services;

use Jsadways\Operationrecord\Exceptions\ServiceException;
use Throwable;

class OperationRecordService
{
    protected string $target_model;
    public function __construct(string $model_class){

        $this->target_model = $model_class;
    }

    /**
     * @throws ServiceException
     */
    public function set(SetDto $data){
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
            throw new ServiceException($this->get_error($throwable));
        }
    }

    public function get(array $filter){
        try{
            return $this->target_model::filter($filter)->first();
        }catch (Throwable $throwable){
            throw new ServiceException($this->get_error($throwable));
        }
    }

    /**
     * @throws ServiceException
     */
    public function list(ListDto $data){
        try{
            $query_data = get_object_vars($data);
            $sort_by = $query_data['sort_by'] ?? 'id';
            $sort_order = $query_data['sort_order'] ?? 'asc';
            $per_page = $query_data['per_page'] ?? '30';
            $filter = $query_data['filter'] ?? [];

            $query = $this->target_model::filter($filter)->orderBy($sort_by, $sort_order);

            if ($per_page) {
                $result = $query->paginate($per_page, ['*'], 'page');
            } else {
                $result = $query->get();
            }

            return $result;
        }catch (Throwable $throwable){
            throw new ServiceException($this->get_error($throwable));
        }
    }
}
