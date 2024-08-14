#Install

###step1
```
composer require jsadways/operationrecord
```

###step2 : edit config/database.php
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

###step3: add element in .env file
```
MONGO_DB_URI=mongodb://MONGO_USER:MONGO_PASSWORD@SERVER_LOCATION
MONGO_DB_DATABASE=YOUR_DB_NAME
```

---

#Support functions and arguments
1. set(SetDto) : set data to MongoDB collection
2. get(array $filter) : get one document form MongoDB collection
3. list(ListDto) : get list of document form MongoDB collection

- SetDto : Object contains $data_id,$creator_id,$action_name,$data(*optional)
- ListDto : Object contains $filter,$sort_by,$sort_order,$per_page
- array filter : check filter usage from [JsAdways/scopeFilter](https://github.com/JsAdways/scopeFilter)

#Usage

##create a model
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

## use service in your controller
```
use Jsadways\Operationrecord\Services\OperationRecordService;
use Jsadways\Operationrecord\Services\ListDto;
use Jsadways\Operationrecord\Services\SetDto;
use App\Models\ExampleRecord; // the custom model class to accrss MongoDB
```

---

#Use Examples

###Set one record
```
$record = new OperationRecordService(ExampleRecord::class);
$array_data = [
    'id' => 1,
    'name' => 'alvin'
];
$data = new SetDto(
    data_id: 133,
    creator_id: 155,
    action_name: 'package_test',
    data: json_encode($array_data)
);
$result = $record->set($data);
```

###Get one record
```
$record = new OperationRecordService(ExampleRecord::class);
$filter = [
    'creator_id_eq' => 155,//to get record where creator_id = 155
];
$result = $record->get($filter);
```

###Get list of records
```
$record = new OperationRecordService(ExampleRecord::class);
$filter = new ListDto(
    filter: ['creator_id_eq'=>2],
    per_page:0, //per_page set 0 do not create pagination object
);
$result = $record->list($filter);
```
