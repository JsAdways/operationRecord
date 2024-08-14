<?php

namespace Jsadways\Operationrecord\Services;

final class ListDto
{
    public function __construct
    (
        public readonly ?array $filter = NULL,
        public readonly ?string $sort_by = NULL,
        public readonly ?string $sort_order = NULL,
        public readonly ?int $per_page = NULL,
    ){}
}
