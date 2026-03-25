<?php

namespace Ok200\Analytics;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
        $response = $this->http->post($this->endpoint, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
            'form_params' => $payload,
        ]);

        return $response->getStatusCode() === 200;
    }
}
