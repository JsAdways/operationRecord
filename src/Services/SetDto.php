<?php

namespace Jsadways\Operationrecord\Services;

final class SetDto
{
    public function __construct
    (
        public readonly string $data_source,
        public readonly int $creator_id,
        public readonly ?int $data_id = null,
        public readonly ?array $data = null,
    ){}
}
