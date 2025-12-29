## Install

step1 use docker-compose set up a MongoDB Server
```
version: "3.3"

services:
  mongo:
    container_name: mongo
    image: mongo:7.0.12
    restart: unless-stopped
    environment:
      - MONGO_INITDB_ROOT_USERNAME=admin
      - MONGO_INITDB_ROOT_PASSWORD=123456
    ports:
      - 27017:27017
    volumes:
      - mongo-data:/data/db
    networks:
      - dev
networks:
  dev:
volumes:
  mongo-data:
```

step2 install package
```
composer require jsadways/operationrecord
```

step3 : edit config/database.php
```
'connections' => [
    ...
    'mongodb' => [
        'driver' => 'mongodb',
        'dsn' => env('MONGO_DB_URI'),
        'database' => env('MONGO_DB_DATABASE', 'forge'),
    ]
    ...
]
```

step4: add element in .env file
```
MONGO_DB_URI=mongodb://admin:123456@SERVER_LOCATION
MONGO_DB_DATABASE=YOUR_DB_NAME
```

---

## Support functions and arguments
1. set(SetDto) : set data to MongoDB collection
2. get(array $filter) : get one document form MongoDB collection
3. list(ListDto) : get list of document form MongoDB collection

- SetDto : Object contains $data_id,$creator_id,$action_name,$data(*optional)
- ListDto : Object contains $filter,$sort_by,$sort_order,$per_page,$show_diff
- array filter : check filter usage from [JsAdways/scopeFilter](https://github.com/JsAdways/scopeFilter)

## Usage

create a model
- this mode describes how to store records to  MongoDB
- for example :
```
#app/Models/ExampleRecord.php

<?php

namespace App\Models;

use Jsadways\Operationrecord\Models\RecodeModel;

class ExampleRecord extends RecodeModel
{
    protected $table = 'example_record';
}

```

use service in your controller
```
use Jsadways\Operationrecord\Services\OperationRecordService;
use Jsadways\Operationrecord\Services\ListDto;
use Jsadways\Operationrecord\Services\SetDto;
use App\Models\ExampleRecord; // the custom model class to accrss MongoDB
```

---

## Use Examples

Set one record with data_id
```
$record = new OperationRecordService(ExampleRecord::class);
$array_data = [
    'id' => 1,
    'name' => 'alvin'
];
$data = new SetDto(
    data_id: 133,
    data_table: 'employee'
    creator_id: 155,
    data: $array_data
);
$result = $record->create_action()->set($data);
```

Set one record without data_id(will be auto take $array_data['id])
```
$record = new OperationRecordService(ExampleRecord::class);
$array_data = [
    'id' => 1,
    'name' => 'alvin'
];
$data = new SetDto(
    data_table: 'employee'
    creator_id: 155,
    data: $array_data
);
$result = $record->create_action()->set($data);
```

using facade set record
```
$array_data = [
    'id' => 1,
    'name' => 'alvin'
];

OperationRecord::for(ExampleRecord::class)
  ->creation_action()
  ->set(new SetDto(
            data_table: 'employee',
            creator_id: 155,
            data: $array_data
        ))
```

using traits in BaseController
```
class Controller extends BaseController
{
    use RecordsOperation;
    
    protected $record_model = ExampleRecord::class;
    protected $creator_id = 172;
}

class BusinessController extends Controller
{
    public function create(Request $request)
    {
        $array_data = [
            'id' => 1,
            'name' => 'alvin'
        ];
        $this->recordOperation(ActionName::Create,$array_data);
    }
}
```

Get one record
```
$record = new OperationRecordService(ExampleRecord::class);
$filter = [
    'creator_id_eq' => 155,//to get record where creator_id = 155
];
$result = $record->get($filter);
```

Get list of records
```
$record = new OperationRecordService(ExampleRecord::class);
$filter = new ListDto(
    filter: ['creator_id_eq'=>2],
    per_page:0, //per_page set 0 do not create pagination object
    show_fidd:true
);
$result = $record->list($filter);
```
