<?php

namespace Ok200\Analytics;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Ok200Client
{
    protected Client $http;

    public function __construct(
        protected string $token,
        protected string $endpoint,
    ) {
        $this->http = new Client;
    }

    /**
     * Send an analytics event to the OK200 platform.
     *
     * @return bool True if sent successfully, false if rate limited (will be retried).
     *
     * @throws GuzzleException
     */
    public function send(array $payload): bool
    {
        $debug = config('ok200.debug');

        if ($debug) {
            Log::debug('OK200 Analytics: sending request', [
                'endpoint' => $this->endpoint,
                'token_set' => ! empty($this->token),
                'token_preview' => substr($this->token, 0, 8).'...',
                'payload' => $payload,
            ]);
        }

        $response = $this->http->post($this->endpoint, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
            'form_params' => $payload,
        ]);

        if ($debug) {
            Log::debug('OK200 Analytics: response received', [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
            ]);
        }

        return $response->getStatusCode() === 200;
    }
}
