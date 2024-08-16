<?php

namespace Jsadways\Operationrecord\Services;

use stdClass;

final class SetDto
{
    public function __construct
    (
        public readonly int $data_id,
        public readonly int $creator_id,
        public readonly string $action_name,
        public readonly ?stdClass $data = null,
    ){}
}
