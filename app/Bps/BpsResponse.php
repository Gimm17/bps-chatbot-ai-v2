<?php

namespace App\Bps;

class BpsResponse
{
    public function __construct(
        public readonly bool $isOk,
        public readonly array $data = [],
        public readonly array $metadata = [],
        public readonly array $rows = [],
        public readonly int $total = 0,
        public readonly int $pages = 0,
        public readonly ?string $errorMessage = null,
        public readonly array $raw = []
    ) {}

    public static function parse(array $body, int $httpStatus = 200): self
    {
        $status = $body['status'] ?? '';
        $availability = $body['data-availability'] ?? '';

        $isOk = ($httpStatus >= 200 && $httpStatus < 300) && ($status === 'OK' || $availability === 'available');

        if (! $isOk) {
            $msg = $body['message'] ?? $body['message2'] ?? 'Data BPS tidak tersedia';

            return new self(
                isOk: false,
                errorMessage: is_string($msg) ? $msg : json_encode($msg),
                raw: $body
            );
        }

        $data = $body['data'] ?? [];
        $metadata = [];
        $rows = [];
        $pages = 0;
        $total = 0;

        if (is_array($data) && count($data) >= 2) {
            $metadata = is_array($data[0]) ? $data[0] : [];
            $rows = is_array($data[1]) ? $data[1] : [];
            $pages = (int) ($metadata['pages'] ?? 1);
            $total = (int) ($metadata['total'] ?? count($rows));
        } elseif (is_array($data)) {
            $rows = $data;
            $total = count($rows);
        }

        return new self(
            isOk: true,
            data: $data,
            metadata: $metadata,
            rows: $rows,
            total: $total,
            pages: $pages,
            raw: $body
        );
    }
}
