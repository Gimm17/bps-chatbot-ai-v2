<?php

namespace App\Bps;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpsApiClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl = 'https://webapi.bps.go.id',
        private readonly int $timeout = 15,
        private readonly bool $cacheEnabled = true,
        private readonly int $cacheTtlHours = 24
    ) {}

    /**
     * Call BPS API with path-segment auth:
     * https://webapi.bps.go.id/v1/api/{path}/{param}/{val}/key/{key}
     */
    public function get(string $path, array $params = []): BpsResponse
    {
        if (empty($this->apiKey)) {
            return new BpsResponse(
                isOk: false,
                errorMessage: 'BPS WebAPI Key belum dikonfigurasi.'
            );
        }

        // Build path URL
        $segments = array_filter(explode('/', trim($path, '/')));
        $pathUrl = implode('/', $segments);

        $paramSegments = [];
        foreach ($params as $k => $v) {
            if ($v !== null && $v !== '') {
                $paramSegments[] = rawurlencode((string) $k);
                $paramSegments[] = rawurlencode((string) $v);
            }
        }

        $fullPath = $pathUrl;
        if (! empty($paramSegments)) {
            $fullPath .= '/'.implode('/', $paramSegments);
        }
        $fullPath .= '/key/'.rawurlencode($this->apiKey);

        $url = rtrim($this->baseUrl, '/').'/v1/api/'.$fullPath;

        return $this->executeRequest($url);
    }

    /**
     * Call BPS API with query param auth (e.g. dataexim):
     */
    public function getQuery(string $path, array $params = []): BpsResponse
    {
        if (empty($this->apiKey)) {
            return new BpsResponse(
                isOk: false,
                errorMessage: 'BPS WebAPI Key belum dikonfigurasi.'
            );
        }

        $params['key'] = $this->apiKey;
        $queryString = http_build_query($params);
        // Retain literal semicolons for multiple HS codes
        $queryString = str_replace('%3B', ';', $queryString);

        $url = rtrim($this->baseUrl, '/').'/v1/api/'.trim($path, '/').'?'.$queryString;

        return $this->executeRequest($url);
    }

    private function executeRequest(string $url): BpsResponse
    {
        $cacheKey = 'bps:v2:'.md5($url);

        if ($this->cacheEnabled) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null && is_array($cached)) {
                return BpsResponse::parse($cached, 200);
            }
        }

        try {
            $response = Http::timeout($this->timeout)->get($url);

            if ($response->successful()) {
                $body = $response->json() ?? [];
                // Redact API key from cached body
                $redacted = $this->redactKey($body);

                $bpsResponse = BpsResponse::parse($redacted, $response->status());

                if ($bpsResponse->isOk && $this->cacheEnabled) {
                    Cache::put($cacheKey, $redacted, now()->addHours($this->cacheTtlHours));
                }

                return $bpsResponse;
            }

            return new BpsResponse(
                isOk: false,
                errorMessage: 'HTTP BPS API Error: '.$response->status()
            );
        } catch (\Throwable $e) {
            Log::warning('BpsApiClient exception: '.$e->getMessage());

            return new BpsResponse(
                isOk: false,
                errorMessage: 'Gagal terhubung ke server BPS: '.$e->getMessage()
            );
        }
    }

    /**
     * Recursively redact API key from payload.
     */
    private function redactKey(mixed $data): mixed
    {
        if (empty($this->apiKey)) {
            return $data;
        }

        if (is_array($data)) {
            $result = [];
            foreach ($data as $k => $v) {
                $result[$k] = $this->redactKey($v);
            }

            return $result;
        }

        if (is_string($data)) {
            return str_replace(
                [$this->apiKey, urlencode($this->apiKey)],
                '[REDACTED]',
                $data
            );
        }

        return $data;
    }
}
