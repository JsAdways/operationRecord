<?php
namespace Jsadways\Operationrecord\Models;

use Jsadways\ScopeFilter\ScopeFilterTrait;
use MongoDB\Laravel\Eloquent\Model;

abstract class RecodeModel extends Model
{
    use ScopeFilterTrait;

    protected $connection = 'mongodb';
    protected $table;
    protected $fillable = [
        'data_id',
        'creator_id',
        'action_name',
        'previous',
        'next'
    ];
}
