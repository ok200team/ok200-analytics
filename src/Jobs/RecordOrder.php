<?php

namespace Ok200\Analytics\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ok200\Analytics\Ok200Client;

class RecordOrder implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $userSha1,
        public int|float $orderValue,
        public string $orderId,
    ) {}

    public function handle(Ok200Client $client): void
    {
        $debug = config('ok200.debug');

        if ($debug) {
            Log::debug('OK200 Analytics: RecordOrder job started', [
                'user_sha1' => $this->userSha1,
                'order_value' => $this->orderValue,
                'order_id' => $this->orderId,
                'config' => [
                    'token_set' => ! empty(config('ok200.token')),
                    'endpoint' => config('ok200.endpoint'),
                    'domain' => config('ok200.domain'),
                    'production_only' => config('ok200.production_only'),
                    'app_env' => config('app.env'),
                ],
            ]);
        }

        if (config('ok200.production_only') && config('app.env') !== 'production') {
            if ($debug) {
                Log::debug('OK200 Analytics: RecordOrder skipped (non-production environment)');
            }

            return;
        }

        $payload = [
            'user_sha1' => $this->userSha1,
            'event' => 'order',
            'domain' => config('ok200.domain'),
            'order_value' => $this->orderValue,
            'order_id' => $this->orderId,
        ];

        try {
            $client->send($payload);
        } catch (Exception $exception) {
            if ($exception->getCode() == 429) {
                dispatch(new self($this->userSha1, $this->orderValue, $this->orderId))
                    ->delay(now()->addMinutes(2));

                Log::warning('OK200 Analytics: rate limited on order event, retrying in 2 minutes.');

                return;
            }

            throw $exception;
        }
    }
}
