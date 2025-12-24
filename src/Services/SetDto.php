<?php

namespace Jsadways\Operationrecord\Services;

use Jsadways\Operationrecord\Enums\ActionName;
use stdClass;

final class SetDto
{
    public function __construct
    (
        public readonly string $data_table,
        public readonly int $creator_id,
        public readonly ?int $data_id = null,
        public readonly ?array $data = null,
    ){}
}
